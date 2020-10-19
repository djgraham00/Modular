<?php
namespace CoreAuth;

class Session extends \Modular\Model {

    public $tableName = "ca_sessions";
    public $userID;
    public $sessionID;
    public $ipAddress;
    public $date;

}
