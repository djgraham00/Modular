<?php
namespace CoreAuth;

class Logout extends \Modular\Component {

    public function Post($params)
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