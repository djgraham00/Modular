<?php

namespace CoreAuth;

class User extends \Modular\Model {

    public $tableName = "ca_users";
    public $firstName;
    public $lastName;
    public $username;
    public $password;

    public function fullName(){
        return $this->firstName." ".$this->lastName;
    }

    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function Profile() {
        $profile = UserProfile::GetWhere(["userID" => $this->id]);

        if(is_object($profile)) {
            return $profile;
        } else {
            return new UserProfile();
        }
    }
}
