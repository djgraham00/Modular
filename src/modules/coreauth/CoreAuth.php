<?php
require("models/coreauth_USER.php");
require("models/coreauth_SESSION.php");

class CoreAuth {

    public $rts = array(
        "/login" => array(
            "path" => "/login",
            "accessTo" => array("1"),
            "template" => "modules/coreauth/templates/index.tpl.php"
        ),
        "/createAccount" => array(
            "path" => "/login",
            "accessTo" => array("1"),
            "template" => "modules/coreauth/templates/index.tpl.php"
        )
    );

    /*
        Account Management
    */
    /* public function createAccount($firstName, $lastName, $username, $password, $orgParent, $passwordUpdated, $gradeLevel)
    {
        if (!$this->checkAuth()) {
            exit;
        }


        $sql = "INSERT INTO users (`sisID`, `firstName`, `lastName`, `username`, `password`, `orgParent`, `passwordUpdated`, `gradeLevel`) VALUES ('$sisID', '$firstName', '$lastName', '$username','$password', '$orgParent', '$passwordUpdated'
    , '$gradeLevel')";

        $result = $this->conn->query($sql);

        if ($result === TRUE) {
            return true;
        } else {
            return false;
        }

    } */

    public function getPassword($username)
    {

        if($inst = Boilerplate::getInstance(new coreauth_USER(), "username", $username) === TRUE) {
            return $inst;
        }
        else {
            return false;
        }

    }

    public function getUsername($param)
    {

        $sql = "SELECT username FROM users WHERE username='$param' LIMIT 1";

        $result = $this->conn->query($sql);
        if ($result->num_rows > 0) {
            while ($result->fetch_assoc()) {
                return $row;
            }
        } else {
            return false;
        }

    }

    public function getUserRoles($userID)
    {

        $sql = "SELECT * FROM accesscontrol WHERE userID='$userID'";

        $tmp = array();

        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($tmp, $row['role']);
            }
        }

        return $tmp;

    }

    public function getUser($username)
    {

        $sql = "SELECT * FROM users WHERE username='$username' LIMIT 1";

        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                return $row;
            }
        }

    }

    public function getCurrentUser()
    {
        $userID = $this->getCurrentUserFromSessionID();

        $sql = "SELECT * FROM users WHERE id='$userID' LIMIT 1";

        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                return $row;
            }
        } else {
            return false;
        }

    }

}