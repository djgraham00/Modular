<?php

//$eng = new RoutingEngine();

//$eng->defineRoute("/test/", "test");

$Routes = array(
    "/admin" => array(
        "path" => "/admin",
        "accessTo" => array("1"),
        "template" => "admin/index.tpl.php"
    ),

    "/login" => array(
        "path" => "/login",
        "accessTo" => NULL,
        "template" => "login/index.tpl.php"
    ),

    "/home" => array(
        "path" => "/home",
        "accessTo" => array("1"),
        "template" => "home/index.tpl.php"
    ),
    "/inv" => array(
        "path" => "/inv",
        "accessTo" => array("1"),
        "template" => "admin/inv.tpl.php"
    ),
    "/register" => array(
        "path" => "/register",
        "accessTo" => array("1"),
        "template" => "home/register.tpl.php"
    ),
    "/finalizeTransaction" => array(
        "path" => "/finalizeTransaction",
        "accessTo" => array("1"),
        "template" => "home/finalize.tpl.php"
    ),
    "/generateReport" => array(
        "path" => "/generateReport/{type}",
        "accessTo" => array("1"),
        "template" => "home/finalize.tpl.php"
    )
);



if(isset($_GET['rt'])){
    $route = $_GET['rt'];
}
else{
    $route = "/";
}

$rt = explode("/",$route);
$rt = array_filter($rt);

if(isRouteDynamic($rt[1])){
    $thisRoute = $Routes["/".$rt[1]];

    if(count($rt) > 1) {

        /* Filter through the URL request and put the variable values into an array*/
        $vars = array();

        for ($i = 1; $i < count($rt); $i++) {
           array_push($vars, $rt[$i+1]);
        }

        /* Filter through the route path to find the variable names, and put them in an array */
        $varNames = explode("/", $thisRoute["path"]);
        $varNames = array_filter($varNames);
        unset($varNames[1]);
        $varNames = array_values($varNames);

        /* Assign each match up the variable namess and values and create the variables */
        for ($i = 0; $i < count($varNames); $i++){
            $varName = preg_replace('/(.*?)\/{(.*?)}/', '$2', $varNames[$i]);
            @${$varName} = $vars[$i];
            echo ${$varName};
            echo "<HR/>";
        }

    }
}


function isRouteDynamic($rt){
    global $Routes;

    if (isset($Routes["/$rt"])){
        $thisRt = $Routes["/$rt"];
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




/*

class RoutingEngine {



    public $definedRoutes;

    public function defineRoute($path, $templatePath, $access = array("0")){
            $rt = array(
                "$path" => array(
                                "path" => "$path",
                                "accessTo" => $access,
                                "template" => "$templatePath"
                                )
                );

            $this->definedRoutes = array_merge($this->definedRoutes, $rt);
    }


    public function render($postVars, $getVars)
    {

        $ep = $this;

        if (isset($getVars['rt'])) {
            $route = $getVars['rt'];
        } else {
            $route = "/";
        }

        if ($this->checkAuth() && $route == "/") {
            header("Location: ./home");
        } else if ($route == "/") {
            header("Location: ./login");
        }

        if (isset($this->Routes[$route])) {
            $rt = $this->Routes[$route];

            if (!$this->checkAuth()) {
                $roles = "0";
            } else {
                $roles = $this->getUserRoles($this->getCurrentUser()['id']);
            }

            $access = false;

            if ($roles != "0") {
                foreach ($roles as $role) {
                    if (in_array($role, $rt['accessTo'])) {
                        $access = true;
                    }
                }
            }

            if ($rt["accessTo"] != null) {
                if ($access) {
                    include("templates/" . $rt["template"]);
                } else {
                    include("templates/errors/403.tpl.php");
                }
            } else {
                include("templates/" . $rt["template"]);
            }
        } else {
            include("templates/errors/404.tpl.php");
        }

    }

    } */