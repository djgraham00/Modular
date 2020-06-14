<?php
class coreauth_SESSION extends MPModel {

    public $tableName = "coreauth_session";
    public $userID    = "varchar(255) NOT NULL";
    public $sessionID = "varchar(255) NOT NULL";
    public $ipAddress = "varchar(255) NOT NULL";
    public $date      = "varchar(255) NOT NULL";

}
