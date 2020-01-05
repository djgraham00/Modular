<?php
class CoreAuth {

    public $rts = array(
        "/" => array(
            "path" => "/",
            "accessTo" => array("1"),
            "template" => "coreauth/templates/auto.routing.php"
        ),
        "/login" => array(
            "path" => "/login",
            "accessTo" => array("1"),
            "template" => "coreauth/templates/index.tpl.php"
        ),
        "/createAccount" => array(
            "path" => "/login",
            "accessTo" => array("1"),
            "template" => "coreauth/templates/index.tpl.php"
        ),
        "/_coreAuthAPI_auth" => array(
            "path" => "/_coreAuthAPI_auth",
            "accessTo" => array("1"),
            "template" => "coreauth/templates/auth.api.php"
        ),
        "/_coreAuthAPI_deAuth" => array(
            "path" => "/_coreAuthAPI_deAuth",
            "accessTo" => array("1"),
            "template" => "coreauth/templates/deauth.api.php"
        ),
        "/coreAuthHome" => array(
            "path" => "/coreAuthHome",
            "accessTo" => array("1"),
            "template" => "coreauth/templates/builtin_home.tpl.php"
        )
    );

    public $Parent;
    public $Config;

    public $enableLoginRedir = true;

    public function __construct($p, $c){
        $this->Parent = $p;
        $this->Config = $c;
    }

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

   public function getUsername($param)
    {

        $obj = $this->Parent->getInstance(new coreauth_USER(), "username", $param);

        if($obj){
            return $obj->username;
        }
        else{
            return false;
        }


    }

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
    public function getUserByID($id)
    {

        $obj = $this->Parent->getInstance(new coreauth_USER(), "id", $id);

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

        if(is_object($obj)){
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

    public function requireAuth(){
        if(!$this->checkAuth()){
            header("Location:".$this->Parent->APP_BASE_URL);
          }
    }

    public function loginRedir() {
        if($this->checkAuth()) {
            header("Location: {$this->Config->LoginRdir}");
        }
     }

    public function __initModels(){
        $this->Parent->createTable(new coreauth_USER());
        $this->Parent->createTable(new coreauth_SESSION());

    }

}