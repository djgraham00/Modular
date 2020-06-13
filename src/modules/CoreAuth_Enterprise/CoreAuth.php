<?php

class CoreAuth_Enterprise extends MPModule {

    public $rts = array(
        "/" => array(
            "path" => "/",
            "accessTo" => array("1"),
            "template" => "CoreAuth_Enterprise/templates/auto.routing.php"
        ),
        "/auto" => array(
            "path" => "/auto",
            "accessTo" => array("1"),
            "template" => "CoreAuth_Enterprise/templates/auto.routing.php"
        ),
        "/login" => array(
            "path" => "/login",
            "accessTo" => array("1"),
            "template" => "CoreAuth_Enterprise/templates/index.tpl.php"
        ),
        "/createAccount" => array(
            "path" => "/login",
            "accessTo" => array("1"),
            "template" => "CoreAuth_Enterprise/templates/index.tpl.php"
        ),
        "/_coreAuthAPI_auth" => array(
            "path" => "/_coreAuthAPI_auth",
            "accessTo" => array("1"),
            "template" => "CoreAuth_Enterprise/templates/auth.api.php"
        ),
        "/_coreAuthAPI_deAuth" => array(
            "path" => "/_coreAuthAPI_deAuth",
            "accessTo" => array("1"),
            "template" => "CoreAuth_Enterprise/templates/deauth.api.php"
        ),
        "/coreAuthHome" => array(
            "path" => "/coreAuthHome",
            "accessTo" => array("1"),
            "template" => "CoreAuth_Enterprise/templates/builtin_home.tpl.php"
        ),
        "/passwordResetSelfService" => array(
            "path" => "/passwordResetSelfService",
            "accessTo" => array("1"),
            "template" => "CoreAuth_Enterprise/templates/passwordreset.tpl.php"
        ),
        "/users" => array(
            "path"  => "/users",
            "component" => "ShowUsers"
        )
    );


    public $enableLoginRedir = true;

    protected function init()
    {

    }

    public function getPassword($username)
    {

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