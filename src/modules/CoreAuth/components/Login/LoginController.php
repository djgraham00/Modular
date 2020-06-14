<?php
class LoginController extends MPComponent {

    public function __GET($params)
    {
        $this->template = "login.twig";

        $this->_CoreAuth->loginRedir();

        $this->__render();
    }

    public function __POST($params)
    {
        header("Content-Type: application/json");

        if($this->_CoreAuth->auth($params['username'], $params['password'])){
            echo '{ "success" : true }';
        }
        else {
            echo '{ "success" : false }';
        }
    }

}