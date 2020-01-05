<?php
class coreauth_SESSION extends dbmodel {

    public $tableName = "coreauth_session";
    public $userID = "varchar(255) NOT NULL";
    public $sessionID = "varchar(255) NOT NULL";

}
