<?php
namespace Modular;

require ("inc/vendor/autoload.php");
/* Modular v3.0 */

/* Require all application classes */
require ("classes/Component.php");
require ("classes/DBHelper.php");
require ("classes/MPHelper.php");
require ("classes/QueryBuilder.php");
require ("classes/Model.php");

$moduleFolder = "";

if(file_exists(__dir__ . "/../Application.json")) {
    $cfg = json_decode(file_get_contents(__dir__."/../Application.json"));

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
    if (file_exists("$modDir/$mod/Module.json")) {
        //If so, decode the configuration
        $moduleInfo = json_decode(file_get_contents("$modDir/$mod/Module.json"));


        $cmpDir = "{$modDir}/$mod/Components";

        //Scan the modules directory for potential modules
        $cmps = array_diff(scandir($cmpDir), array('.', '..'));

        foreach ($cmps as $cmp) {
            require ("{$cmpDir}/{$cmp}/{$cmp}.php");
        }

        //Next, require the database modules if any exist for the given module
        if(isset($moduleInfo->MOD_HAS_MODELS) && $moduleInfo->MOD_HAS_MODELS == true){
            foreach ((array_diff(scandir("$modDir/$mod/Models"), array('.', '..'))) as $dbmodel) {
                require("$modDir/$mod/Models/$dbmodel");

                if($moduleInfo->MOD_MODEL_AUTOINIT) {
                    $class = chop($dbmodel, ".php");
                    $class = "$mod\\$class";
                    $tmp = new $class();
                    DBHelper::createTable($tmp);
                }

            }
        }

    }
}



class Modular {

    //Application Configuration (defaults, loaded from application.json if exists)
    private $APP_DB_TYPE    = "mysql";
    private $APP_DB_HOST    = "localhost";
    private $APP_DB_NAME    = "mpdev";
    private $APP_DB_USER    = "root";
    private $APP_DB_PASS    = "";
    private $APP_MODULE_DIR = "";
    private $APP_ENTRY_POINT = "";
    public $APP_NAME       = "";
    public $APP_BASE_URL   = "";
    public $APP_VER = "0.0.1";
    public $APP_ORG = "";

    //Public Variables
    public $PDO;
    public $MYSQL;
    public $Modules = array();
    public $loadedModNames = array();
    private $Routes = [];

    function __construct() {
        //Load the application configuration
        $this->loadConfig();

    }

    public function render() {
        /* Get the route */
        if(isset($_GET['rt'])) {
            $this->loadComponent($_GET['rt']);
        } else {
            $this->loadComponent($this->APP_ENTRY_POINT);
        }



    }

    public function loadComponent($thisRoute) {

        $routeComponents = explode(".", $thisRoute);

        $namespace = $routeComponents[0];
        $class = $routeComponents[1];



        $name = "$namespace\\$class";

        $tmp = new $name($this);

        if(!$tmp->denyAccess) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $tmp->Post($_POST);
            } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $tmp->Get($_GET);
            } else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                $tmp->Get($_GET);
            } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                $tmp->Get($_GET);
            }
        } else {
            echo "Access is denied";
        }

    }

    private function loadConfig() {
        $filePath = __DIR__ . "/../Application.json";
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


}