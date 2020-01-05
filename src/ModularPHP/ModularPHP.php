<?php
/**
 * ModularPHP
 * Created by Drew Graham (https://drewjgraham.com)
 * Creation Date: 10/2/2019
 * Updated : 1/4/2020
 */

require ("classes/dbmodel.php");
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
        require("$modDir/$mod/" . $moduleInfo->ImportFileName);

        //Also, add the module information to the loaded module array
        $mods[$mod] = $moduleInfo;

        //Next, require the database modules if any exist for the given module
        if(isset($moduleInfo->HasDBModels) && $moduleInfo->HasDBModels == true && isset($moduleInfo->MOD_MODEL_DIR)){
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

    //Public Variables
    public $PDO;
    public $Modules = array();
    public $loadedModNames = array();
    public $Routes = array();

    public function __construct()
    {
        //Load the application configuration
        $this->loadConfig();

        //Initiate a connection to the database using the provided information
        $this->PDO = new PDO($this->APP_DB_TYPE . ":host=" . $this->APP_DB_HOST . ";dbname=" . $this->APP_DB_NAME, $this->APP_DB_USER, $this->APP_DB_PASS);

        //Load all of the modules
        $this->loadModules();

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

            $mainClass = $modInfo->MainClass;

            ${$modName} = new $mainClass($this, $modInfo);

            if ($modInfo->HasRouteDefinitions) {
                $routeDefinitionVar = $modInfo->RouteDefinitionVar;
                $this->Routes = array_merge($this->Routes, ${$modName}->{$routeDefinitionVar});
            }

            /* if(isset($modInfo->HasDBModels) && $modInfo->HasDBModels == true){
                $funcName = $modInfo->ModelsInitMethod;
                ${$modName}->$funcName();
            } */

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

                include(__dir__ . "/../". $this->APP_MODULE_DIR . "/" . $thisRoute["template"]);
            }
        }
        else{
            include(__dir__ . "/../". $this->APP_MODULE_DIR . "/" . $thisRoute["template"]);
        }

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

    public function getInstance($refObj, $key, $value, $customSQL = false, $orderByID = "ASC", $limit = false){

        if(!$this->tableExists($refObj->tableName)){
            $objName = get_class($refObj);
            $this->createTable(new $objName());
            $this->getInstance($refObj, $key, $value);
        }

        try{

            $sql = "SELECT * FROM ".$refObj->tableName. " WHERE $key='$value' ORDER BY id $orderByID";

            if($limit != false) {
                $sql .= " LIMIT $limit";
            }

            if($customSQL != false){
                $sql = $customSQL;
            }

            $getObjs = $this->PDO->prepare($sql);
            $getObjs->execute();

            $objs = $getObjs->fetchAll();

            if($objs != FALSE) {
                foreach ($objs[0] as $key => $value) {
                    $refObj->{$key} = $value;
                }
            }
            else{
                return false;
            }

            return $refObj;

        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public function getAllObjects($refObj){

        if(!$this->tableExists($refObj->tableName)){
            $objName = get_class($refObj);
            $this->createTable(new $objName());
            $this->getAllObjects($refObj);
        }

        try{
            $sql = "SELECT * FROM ".$refObj->tableName;
            $getObjs = $this->PDO->prepare($sql);
            $getObjs->execute();

            $tmp = array();

            $objs = $getObjs->fetchAll();

            foreach($objs as $obj) {
                $class = get_class($refObj);
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