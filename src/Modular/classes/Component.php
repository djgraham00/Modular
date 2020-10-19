<?php

namespace Modular;

class Component {

    private $Modular;

    public $template = NULL;
    public $denyAccess = false;

    public $params;

    function __construct($Modular) {
        $this->Modular = $Modular;
    }

    public function Get($params) {
        $this->template = MPHelper::getError(404);
        $this->params = $_GET;
        include($this->template);
        exit;
    }

    public function Post($params) {
        $this->template = MPHelper::getError(404);
        include($this->template);
    }

    public function Put($params) {
        $this->template = MPHelper::getError(404);
        include($this->template);
    }

    public function Delete($params) {
        $this->template = MPHelper::getError(404);
        include($this->template);
    }

    public function render() {
        if($this->template != null) {
            foreach ($this->getFields() as $val) {
                ${$val} = $this->{$val};
            }

            $reflection = new \ReflectionClass($this);

            if(MPHelper::contains(".twig", $this->template)) {


                $loader = new \Twig\Loader\FilesystemLoader(dirname($reflection->getFileName()) );
                $twig = new \Twig\Environment($loader, [
                    'cache' => __dir__.'/../cache',
                ]);

                $twig = new \Twig\Environment($loader);
                $function = new \Twig\TwigFunction('component', function ($name) {
                    $this->ModularPHP->loadComponent(array("component" => $name));
                });

                $twig->addFunction($function);

                //getComponent($name)

                $twigArray = array();

                foreach ($this as $k => $v) {
                    $twigArray[$k] = $v;
                }

                echo $twig->render($this->template, $twigArray);

            } else {
                include(dirname($reflection->getFileName()) . "\\" . $this->template);
            }
        }
    }

    public function getFields() {
        $fields = array();

        foreach ($this as $u => $v) {
            array_push($fields, $u);
        }

        return $fields;
    }

    public static function Load($mp, $req = "GET") {
        $class = static::class;
        $obj = new $class($mp);

        if($req == "GET") {
            $obj->__GET($_GET);
        } else if($req == "POST") {
            $obj->__POST($_POST);
        } else {
            return false;
        }
    }

}