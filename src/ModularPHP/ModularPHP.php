<?php
/**
 * ModularPHP
 * Created by Drew Graham (https://drewjgraham.com)
 * Creation Date: 10/2/2019
 * Updated : 1/4/2020
 */

require ("classes/dbmodel.php");
require ("classes/MPComponent.php");
require ("classes/MPModule.php");

$moduleFolder = "";

if(file_exists(__dir__ . "/../application.json")) {
    $cfg = json_decode(file_get_contents(__dir__."/../application.json"));

    if(is_string($cfg->APP_MODULE_DIR)) {
        $moduleFolder = $cfg->APP_MODULE_DIR;
    }
    else {
        echo "APPLICATION FAILED TO START; INVALID MODULE DIRECTORY";
        exit;
    }
}

$modDir = __dir__ . "/../" . $moduleFolder;

//Scan the modules directory for potential modules
$modules = array_diff(scandir($modDir), array('.', '..'));

//Create an array to store the loaded modules
$mods = array();

//Loop through the potential modules
foreach ($modules as $mod) {
    //Check if a configuration file exists for the module
    if (file_exists("$modDir/$mod/$mod.json")) {
        //If so, decode the configuration
        $moduleInfo = json_decode(file_get_contents("$modDir/$mod/$mod.json"));

        //Then, import the main class for that module
        require("$modDir/$mod/" . $moduleInfo->MOD_FILE);

        //Also, add the module information to the loaded module array
        $mods[$mod] = $moduleInfo;

        //Next, require the database modules if any exist for the given module
        if(isset($moduleInfo->MOD_HAS_MODELS) && $moduleInfo->MOD_HAS_MODELS == true && isset($moduleInfo->MOD_MODEL_DIR)){
            foreach ((array_diff(scandir("$modDir/$mod/{$moduleInfo->MOD_MODEL_DIR}"), array('.', '..'))) as $dbmodel) {
                require("$modDir/$mod/{$moduleInfo->MOD_MODEL_DIR}/$dbmodel");
            }
        }

    }
}

/*
 *
 * ModularPHP Base Class
 * Contains the base code for ModularPHP
 * Essential functionality such as module loading, rendering, routing, and database manipulation
 */
class ModularPHP {

    //Application Configuration (defaults, loaded from application.json if exists)
    private $APP_DB_TYPE    = "mysql";
    private $APP_DB_HOST    = "localhost";
    private $APP_DB_NAME    = "mpdev";
    private $APP_DB_USER    = "root";
    private $APP_DB_PASS    = "";
    private $APP_MODULE_DIR = "";
    public $APP_NAME       = "";
    public $APP_BASE_URL   = "";
    public $APP_VER = "0.0.1";

    //Public Variables
    public $PDO;
    public $MYSQL;
    public $Modules = array();
    public $loadedModNames = array();
    public $Routes = array();

    public $MP_DIR;
    public $MOD_DIR;

    public function __construct()
    {
        //Load the application configuration
        $this->loadConfig();

        //Initiate a connection to the database using the provided information
        $this->PDO = new PDO($this->APP_DB_TYPE . ":host=" . $this->APP_DB_HOST . ";dbname=" . $this->APP_DB_NAME, $this->APP_DB_USER, $this->APP_DB_PASS);

        if($this->APP_DB_TYPE == "mysql") {
            $this->MYSQL = new mysqli($this->APP_DB_HOST, $this->APP_DB_USER, $this->APP_DB_PASS, $this->APP_DB_NAME);
        }
        //Load all of the modules
        $this->loadModules();

        $this->MP_DIR = __DIR__; //dirname(__FILE__);
        $this->MOD_DIR = $this->MP_DIR . "/../" . $this->APP_MODULE_DIR;

    }

    private function loadConfig() {
        $filePath = __DIR__ . "/../application.json";
        if(file_exists($filePath)) {
            $cfg = json_decode(file_get_contents($filePath));
            foreach ($cfg as $opt=>$val) {
                $this->{$opt} = $val;
            }
            return true;
        }
        else {
            return false;
        }
    }

    private function loadModules() {
        global $mods;

        foreach ($mods as $modName=>$modInfo) {

            $mainClass = $modInfo->MOD_CLASS;

            if (isset($modInfo->MOD_DEPENDENCIES)) {
                foreach ($modInfo->MOD_DEPENDENCIES as $depMod) {
                    $this->loadModule($depMod);
                }
            }

            $this->loadModule($modName);
        }
    }

    private function loadModule($modName) {

        global $mods;

        if(!in_array($modName, $this->loadedModNames)) {

            $modInfo = $mods[$modName];

            $mainClass = $modInfo->MOD_CLASS;

            if (isset($modInfo->MOD_DEPENDENCIES)) {
                foreach ($modInfo->MOD_DEPENDENCIES as $depMod) {
                    $this->loadModule($depMod);
                }
            }

            if (isset($modInfo->IsLibrary) && $modInfo->IsLibrary == true) {
                ${$modName} = new $mainClass();
            } else {
                ${$modName} = new $mainClass($this, $modInfo);
            }


            if ($modInfo->MOD_HAS_ROUTES) {
                $routeDefinitionVar = $modInfo->MOD_ROUTES_VAR;

                $routes = ${$modName}->{$routeDefinitionVar};

                foreach ($routes as $rt=>$inf) {
                    $routes[$rt]["mod"] = $modName;
                }

                $this->Routes = array_merge($this->Routes, $routes);
            }

            if (isset($modInfo->MOD_HAS_MODELS) && $modInfo->MOD_HAS_MODELS == true) {
                $funcName = $modInfo->MOD_MODEL_INIT;
                ${$modName}->$funcName();
            }

            $this->Modules = array_merge($this->Modules, array("_$mainClass" => ${$modName}));
            array_push($this->loadedModNames, $modName);
        }
    }

    public function render($postVars, $getVars) {

        $ModularPHP = $this;

        foreach($this->Modules as $key=>$value){
            ${$key} = $value;
        }

        if (isset($getVars['rt'])) {
            $route = $getVars['rt'];
        } else {
            $route = "/";
        }

        if($route != "/") {
            $rt = explode("/", $route);
            $rt = array_filter($rt);

            if(!isset($this->Routes["/" . $rt[1]])){
                //Display error page if route is not found
                include("templates/404.tpl.php");
                exit;
            }

            $thisRoute = $this->Routes["/" . $rt[1]];



        }
        else{
            $rt = array("", "/");
            $thisRoute = $this->Routes["/"];
        }
        if ($this->isRouteDynamic($rt[1])) {

            if (count($rt) > 1) {

                /* Filter through the URL request and put the variable values into an array*/
                $vars = array();

                for ($i = 1; $i < count($rt); $i++) {
                    array_push($vars, $rt[$i + 1]);
                }

                /* Filter through the route path to find the variable names, and put them in an array */
                $varNames = explode("/", $thisRoute["path"]);
                $varNames = array_filter($varNames);
                unset($varNames[1]);
                $varNames = array_values($varNames);

                /* Assign each match up the variable names and values and create the variables */
                for ($i = 0; $i < count($varNames); $i++) {
                    $varName = preg_replace('/{(.*?)}/', '$1', $varNames[$i]);
                    @${$varName} = $vars[$i];
                }

                if(isset($thisRoute["component"])) {
                    $this->loadComponent($thisRoute);
                } else {
                    include(__dir__ . "/../" . $this->APP_MODULE_DIR . "/" . $thisRoute["template"]);
                }
            }
        }
        else{
            if(isset($thisRoute["component"])) {
                $this->loadComponent($thisRoute);
            } else {
                include(__dir__ . "/../" . $this->APP_MODULE_DIR . "/" . $thisRoute["template"]);
            }
        }

    }

    private function loadComponent($thisRoute) {
        $name = $thisRoute["component"]."Controller";
        $moduleName = "_".$thisRoute['mod'];
        $tmp = new $name($this);
        $tmp->__index($_GET);
        $tmp->__render();
    }
    /**
     * Tests if a route is passing data to the requested page
     * @param string $rt
     * @return bool
     */
    private function isRouteDynamic($rt){

        if (isset($this->Routes["/$rt"])){
            $thisRt = $this->Routes["/$rt"];
            if(strstr ($thisRt["path"], "{")){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    /**
     * Checks if a module has been loaded
     * @param string $module This is name of a module
     * @return bool
     */
    public function hasMod($module){
        if(in_array($module, $this->loadedModNames)) {
            return true;
        }
        else {
            return false;
        }
    }


    /*
     *
     * <ModularPHP> Database Function
     *
     */


    public function insertObject($obj){

        if($this->tableExists($obj->tableName)) {
            try {
                $sql = "INSERT INTO ".$obj->tableName." (".$obj->generateInsertStr().") VALUES (".$obj->generateInsertVal().")";
                $this->PDO->exec($sql);
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
        else{
            $objName = get_class($obj);
            $this->createTable(new $objName());
            $this->insertObject($obj);
        }
    }

    public function getObject($ref, $where = array(), $opts = array()){

        $whereSQL = "";
        $isFirst = true;

        foreach ($where as $key=>$val) {
            if($isFirst) {
                $whereSQL .= " WHERE $key='$val'";
                $isFirst = false;
            }
            $whereSQL .= " AND $key='$val'";
        }

        $optsSQL = "";

        foreach ($opts as $opt) {
            $optsSQL .= "$opt ";
        }

        if(!$this->tableExists($ref->tableName)){
            $objName = get_class($ref);
            $this->createTable(new $objName());
            $this->getObject($ref, $where, $opts);
        }

        try{

            $sql = "SELECT * FROM ".$ref->tableName." {$whereSQL} {$optsSQL} LIMIT 1";

            $getObjs = $this->PDO->prepare($sql);
            $getObjs->execute();

            $objs = $getObjs->fetchAll();

            $class = get_class($ref);
            $tmp = new $class();

            if($objs != FALSE) {
                foreach ($objs[0] as $key => $value) {
                    $tmp->{$key} = $value;
                }
            }
            else{
                return false;
            }

            return $tmp;

        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function getObjects($ref, $where = array(), $opts = array()) {

        $whereSQL = "";
        $isFirst = false;

        $whereSQL = "";
        $isFirst = true;

        foreach ($where as $key=>$val) {
            if($isFirst) {
                $whereSQL .= " WHERE $key='$val'";
                $isFirst = false;
            }
            $whereSQL .= " AND $key='$val'";
        }

        $optsSQL = "";

        foreach ($opts as $opt) {
            $optsSQL .= "$opt ";
        }


        if(!$this->tableExists($ref->tableName)){
            $objName = get_class($ref);
            $this->createTable(new $objName());
            $this->getObject($ref, $where, $opts);
        }

        try{

            $sql = "SELECT * FROM ".$ref->tableName."{$whereSQL} {$optsSQL}";
            $getObjs = $this->PDO->prepare($sql);
            $getObjs->execute();

            $tmp = array();

            $objs = $getObjs->fetchAll();

            foreach($objs as $obj) {
                $class = get_class($ref);
                $tmpObj = new $class();
                foreach($obj as $key=>$value){
                    $tmpObj->{$key} = $value;
                }
                array_push($tmp, $tmpObj);
            }

            return $tmp;

        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    /* Backwards-compatibility functions */
    public function getAllObjects($refObj) {
        return $this->getObject($refObj, array(), array());
    }

    public function getInstance($refObj, $key, $value, $customSQL = false, $orderByID = "ASC", $limit = false)
    {

        $opts = array();
        array_push($opts, "ORDER BY id {$orderByID}");

        if ($limit) {
            array_push($opts, "LIMIT $limit");
        }

        return $this->getObject($refObj, array($key => $value), $opts);
    }


    public function tableExists($table) {
        // Try a select statement against the table
        // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
        try {
            $result = $this->PDO->query("SELECT * FROM $table LIMIT 1");
        } catch (Exception $e) {
            // We got an exception == table not found
            return FALSE;
        }

        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return true !== FALSE;
    }

    public function conditionalDeleteRow($objRef, $key, $value) {
        $table = $objRef->tableName;
        // Try a select statement against the table
        // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
        try {
            $result = $this->PDO->query("DELETE FROM $table WHERE $key='$value'");
        } catch (Exception $e) {
            // We got an exception == table not found
            return FALSE;
        }

        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return true;
    }


    public function createTable($obj){
        // Try a select statement against the table
        // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
        try {
            $result = $this->PDO->exec($obj->generateSQLModel());
        } catch (Exception $e) {
            // We got an exception == table not found
            return false;
        }

        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return $result !== FALSE;

    }

}