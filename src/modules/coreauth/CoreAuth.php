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
        ),
        "/_coreAuthAPI" => array(
            "path" => "/_coreAuthAPI/auth",
            "accessTo" => array("1"),
            "template" => "modules/coreauth/templates/auth.api.php"
        ),
        "/coreAuthHome" => array(
            "path" => "/coreAuthHome",
            "accessTo" => array("1"),
            "template" => "modules/coreauth/templates/builtin_home.tpl.php"
        )
    );

    public $Parent;

    public function __construct($p){
        $this->Parent = $p;
    }

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

       $obj = $this->Parent->getInstance(new coreauth_USER(), "username", $username);

       if($obj){
           return $obj->password;
       }
       else{
           return false;
       }

    }

   /* public function getUsername($param)
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

    } */

    /* public function getUserRoles($userID)
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

    } */

    public function getUser($username)
    {

        $obj = $this->Parent->getInstance(new coreauth_USER(), "username", $username);

        if($obj){
            return $obj;
        }
        else{
            return false;
        }

    }

    public function getCurrentUser()
    {
        $userID = $this->getCurrentUserFromSessionID();

        $obj = $this->Parent->getInstance(new coreauth_USER(), "id", $userID);

        if($obj){
            return $obj->password;
        }
        else{
            return false;
        }
    }

    /*
       Authentication
   */
    public function auth($username, $password){

        if(password_verify($password, $this->getPassword($username))){
            $this->createSession($this->getUser($username)->id);
            return true;
        }
        else{
            return false;
        }

    }

    public function deAuth(){
        $sessID = $_COOKIE['session'];

        setcookie( "session", "NULL", strtotime( '+30 days' ), '/');

        if($this->destroySession($sessID)){
            return true;
        }
        else{
            return false;
        }
    }

    public function checkAuth(){

        if(!isset($_COOKIE['session'])){
            return false;
        }
        else if(!$this->validateSession($_COOKIE['session'])){
            return false;
        }
        else{
            return true;
        }

    }

    /*
        Session Management
    */

    public function validateSession($id){

        $obj = $this->Parent->getInstance(new coreauth_SESSION(), "sessionID", $id);

        if($obj){
            return true;
        }
        else{
            return false;
        }
    }

    public function destroySession($id){

        $obj = $this->Parent->conditionalDeleteRow(new coreauth_SESSION(), "sessionID", $id);

        if($obj){
            unset($_COOKIE['session']);
            return true;
        }
        else{
            return false;
        }
    }

    public function createSession($userID){

        $sessID = md5(time()*rand(10, 10000));

        $newSession = new coreauth_SESSION();
        $newSession->userID = $userID;
        $newSession->sessionID = $sessID;


        $obj = $this->Parent->insertObject($newSession);

        if($obj){
            setcookie( "session", $sessID, strtotime( '+30 days' ), '/');
            return true;
        }
        else {
            return false;
        }

    }

    public function getCurrentUserFromSessionID(){

        if(isset($_COOKIE['session'])){
            $sessID = $_COOKIE['session'];
        }
        else{
            return false;
        }

        $obj = $this->Parent->getInstance(new coreauth_SESSION(), "sessionID", $sessID);

        if($obj){
            return $obj->userID;
        }
        else{
            return false;
        }

    }

}