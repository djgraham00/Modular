<?php
/**
 * Drew Graham
 * Date: 10/2/2019
 *
 */

class Boilerplate {

    private $dbtype = "mysql";
    private $dbhost = "localhost";
    private $APP_DB_NAME = "bpdev";
    private $dbuser = "root";
    private $dbpass = "";
    public $PDO;
    public $Modules = array();
    public $loadedModNames = array();
    public $AppName = "Boilerplate Dev";
    public $Routes = array();
    public $BASE_URL = "http://localhost/boilerplate/";


    public function __construct()
    {
        $this->PDO = new PDO($this->dbtype . ":host=" . $this->dbhost . ";dbname=" . $this->APP_DB_NAME, $this->dbuser, $this->dbpass);
        $this->loadModules();

    }

    public function loadModules() {


        $modules = array_diff(scandir(__DIR__ . "\\modules"), array('.', '..'));

        $routes = array();

        foreach ($modules as $mod) {

            if (file_exists(__DIR__ . "/modules/$mod/$mod.json")) {
                $moduleInfo = json_decode(file_get_contents(__DIR__ ."/modules/$mod/$mod.json"));

                require(__DIR__ . "/modules/$mod/" . $moduleInfo->ImportFileName);
                $mainClass = $moduleInfo->MainClass;

                ${$mod} = new $mainClass($this);

                if ($moduleInfo->HasRouteDefinitions) {
                    $routeDefinitionVar = $moduleInfo->RouteDefinitionVar;
                    $this->Routes = array_merge($this->Routes, ${$mod}->{$routeDefinitionVar});

                }

                $this->Modules = array_merge($this->Modules, array("_$mainClass" => ${$mod}));
                array_push($this->loadedModNames, $mod);
            }
        }

     }

    public function render($postVars, $getVars) {

        $BoilerPlate = $this;

        if (isset($getVars['rt'])) {
            $route = $getVars['rt'];
        } else {
            $route = "/";
        }

        if($route != "/") {
            $rt = explode("/", $route);
            $rt = array_filter($rt);

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

                /* Assign each match up the variable namess and values and create the variables */
                for ($i = 0; $i < count($varNames); $i++) {
                    $varName = preg_replace('/{(.*?)}/', '$1', $varNames[$i]);
                    @${$varName} = $vars[$i];
                }

                include($thisRoute["template"]);
            }
        }
        else{
            include($thisRoute["template"]);
        }

    }

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

    public function hasMod($module){
        if(in_array($module, $this->loadedModNames)) {
            return true;
        }
        else {
            return false;
        }
    }

    /*
     * OLD RENDER FUNCTION
     *
     * public function render($postVars, $getVars){

        $BoilerPlate = $this;

        if(isset($getVars['rt'])){
            $route = $getVars['rt'];
        }
        else {
            $route = "/";
        }

        if($this->checkAuth() && $route == "/"){
            header("Location: ./home");
        } else if($route == "/"){
            header("Location: ./login");
        }

        if(isset($this->Routes[$route])){
            $rt = $this->Routes[$route];

            if(!$this->checkAuth()){
                $roles = "0";
            }
            else{
                $roles = $this->getUserRoles($this->getCurrentUser()['id']);
            }

            $access = false;

            if($roles != "0"){
                foreach ($roles as $role){
                    if(in_array($role, $rt['accessTo'])){
                        $access = true;
                    }
                }
            }

            if($rt["accessTo"] != null){
                if($access){
                    include("templates/".$rt["template"]);
                }
                else{
                    include("templates/errors/403.tpl.php");
                }
            }
            else{
                include("templates/".$rt["template"]);
            }
        }
        else{
            include("templates/errors/404.tpl.php");
        }

    } */

    /*
     *
     * Boilerplate Database Function
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

    public function getInstance($refObj, $key, $value){

        try{
            $sql = "SELECT * FROM ".$refObj->tableName. " WHERE $key='$value'";

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
            $result = $this->PDO->query("SELECT 1 FROM $table LIMIT 1");
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

            echo $e->getMessage();
        }

        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return $result !== FALSE;
    }

}