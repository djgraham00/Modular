<?php
require ("src/BoilerPlate.php");

$bp = new Boilerplate();

$bp->render($_POST, $_GET);


//$eng = new RoutingEngine();

//$eng->defineRoute("/test/", "test");

/* $Routes = array(
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