<?php
class LogoutController extends MPComponent {

    public function __POST($params)
    {
        header("Content-Type: application/json");
        error_reporting(0);
        ini_set('display_errors', 0);
        if($this->_CoreAuth->deAuth()){
            echo '{ "success" : true }';
        }
        else {
            echo '{ "success" : false }';
        }
    }

}