<?php

class coreauth_USER extends dbmodel {

    public $tableName = "coreauth_users";
    public $firstName = "varchar(255) NOT NULL";
    public $lastName = "varchar(255) NOT NULL";
    public $username = "varchar(255) NOT NULL";
    public $password = "varchar(255) NOT NULL";

    public function fullName(){
        return $this->firstName." ".$this->lastName;
    }
    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

}
