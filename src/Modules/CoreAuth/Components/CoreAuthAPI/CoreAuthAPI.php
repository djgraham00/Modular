<?php
namespace CoreAuth;

class CoreAuthAPI extends \Modular\Component {

    public function Get($params) {
        header("Content-Type: application/json");
        if(isset($params["Endpoint"])) {
            $action = $params["Endpoint"];
        } else {
            $action = false;
        }

        /* if($action == "IsUserAuthenticated"){
            if($this->_CoreAuth->checkAuth()){
                echo "{ \"success\" : true } ";
            } else {
                echo "{ \"success\" : false } ";
            }
            
        } else {
            echo "{ \"success\" : false, \"error\" : \"Invalid Endpoint\" }";
        } */

        $this->render();

    }

}