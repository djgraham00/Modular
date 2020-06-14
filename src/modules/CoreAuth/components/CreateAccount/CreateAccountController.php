<?php


class CreateAccountController extends MPComponent {

    public function __GET($params)
    {

        $this->_CoreAuth->loginRedir();

        $this->template = "CreateAccount.twig";

                $this->__render();
    }

    public function __POST($params)
    {
        header("Content-Type: application/json");

        if(is_object(coreauth_USER::GetWhere(["username" => $params['username']]))) {
            echo '{ "success" : false, "msg": "Username is taken" }';
        } else {
            $newUser = new coreauth_USER();
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