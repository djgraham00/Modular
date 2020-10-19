<?php
namespace CoreAuth;

class CreateAccount extends \Modular\Component {

    public function Get($params)
    {

        //$this->_CoreAuth->loginRedir();

        $this->template = "CreateAccount.twig";
        $this->render();
    }

    public function Post($params)
    {
        header("Content-Type: application/json");

        if(is_object(User::GetWhere(["username" => $params['username']]))) {
            echo '{ "success" : false, "msg": "Username is taken" }';
        } else {
            $newUser = new User();
            $newUser->firstName = $params["firstName"];
            $newUser->lastName = $params["lastName"];
            $newUser->username = $params["username"];
            $newUser->setPassword($params["password"]);

            if($newUser->save()) {
                echo '{ "success" : true }';

            } else {
                echo '{ "success" : false, "msg" : "An error occurred while creating your account" }';
            }

        }

    }



}