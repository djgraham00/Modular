<?php
namespace CoreAuth;

class Login extends \Modular\Component {

    public function Get($params)
    {
        $this->template = "login.twig";

        $this->render();
    }

    public function Post($params)
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