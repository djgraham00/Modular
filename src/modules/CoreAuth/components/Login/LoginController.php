<?php
class LoginController extends MPComponent {

    public function __GET($params)
    {
        $this->template = "login.twig";

        $this->ModularPHP->Modules["_CoreAuth"]->loginRedir();

        $this->__render();
    }

    public function __POST($params)
    {
        header("Content-Type: application/json");

        if($this->ModularPHP->Modules['_CoreAuth']->auth($params['username'], $params['password'])){
            echo '{ "success" : true }';
        }
        else {
            echo '{ "success" : false }';
        }
    }

}