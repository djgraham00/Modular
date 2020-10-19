<?php
namespace CoreAuth;

class UserProfile extends \Modular\Model {

    public $tableName = "ca_profiles";
    public $userID;
    public $emailAddress = "VARCHAR(255)";

    public function User() {
        return User::GetWhereID($this->userID);
    }
}