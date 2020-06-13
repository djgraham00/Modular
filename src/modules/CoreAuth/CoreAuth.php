<?php

class CoreAuth extends MPModule {

    public $rts = array(
        "/" => array(
            "path" => "/",
            "redirect" => "./Login"
        ),
        "/Login" => array(
            "path" => "/Login",
            "component" => "Login"
        ),
        "/CoreAuthHome" => array(
            "path" => "/CoreAuthHome",
            "component" => "CoreAuthHome"
        ),
        "/Logout" => array(
            "path" => "/Logout",
            "component" => "Logout"
        )
    );

    public $enableLoginRedir = true;

    public function getPassword($username) {

       $obj = $this->Parent->getObject(new coreauth_USER(), array("username" => $username));

       if($obj){
           return $obj->password;
       }
       else{
           return false;
       }

    }

   public function getUsername($param)
    {

        $obj = $this->Parent->getObject(new coreauth_USER(), array("username"=> $param));

        if($obj){
            return $obj->username;
        }
        else{
            return false;
        }


    }

    public function getUser($username)
    {

        $obj = $this->Parent->getObject(new coreauth_USER(), array("username" => $username));

        if($obj){
            return $obj;
        }
        else{
            return false;
        }

    }
    public function getUserByID($id)
    {

        $obj = $this->Parent->getObject(new coreauth_USER(), array("id" => $id));

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

        $obj = $this->Parent->getObject(new coreauth_USER(), array("id" => $userID));

        if($obj){
            return $obj;
        }
        else{
            return false;
        }
    }

    /*
       Authentication
   */
    public function auth($username, $password){

        if (password_verify($password, $this->getPassword($username))) {
            $this->createSession($this->getUser($username)->id);
            return true;
        } else {
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

        $obj = $this->Parent->getObject(new coreauth_SESSION(), array("sessionID" => $id));

        if(is_object($obj)){

            if($obj->ipAddress != $_SERVER["REMOTE_ADDR"]) {
                $this->destroySession($_COOKIE['session']);
                return false;
            }

            /*if(strtotime('+1 day', $obj->date) > $obj->date) {
                $this->destroySession($_COOKIE['session']);
                return false;
            }*/

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
        $newSession->userID    = $userID;
        $newSession->sessionID = $sessID;
        $newSession->ipAddress = $_SERVER['REMOTE_ADDR'];
        $newSession->date      = time();

        $newSession->save();

        setcookie( "session", $sessID, strtotime( '+30 days' ), '/');



    }

    public function getCurrentUserFromSessionID(){

        if(isset($_COOKIE['session'])){
            $sessID = $_COOKIE['session'];
        }
        else{
            return false;
        }

        $obj = $this->Parent->getObject(new coreauth_SESSION(), array("sessionID" => $sessID));

        if($obj){
            return $obj->userID;
        }
        else{
            return false;
        }
    }


    public function requireAuth(){
        if(!$this->checkAuth()){
            header("Location:".$this->Parent->APP_BASE_URL);
          }
    }

    public function loginRedir() {
        if($this->checkAuth()) {
            header("Location: " . $this->Config->APP_HOME_URL);
        }
     }



    public function __initModels() {
        $this->Parent->createTable(new coreauth_USER());
        $this->Parent->createTable(new coreauth_SESSION());
    }

}