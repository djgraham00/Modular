<?php
/**
 * Drew Graham
 * Date: 10/2/2019
 *
 */

class BoilerPlate {

    public $conn;
    public $salesTax;
    public $displayName;

    private $SQL_CONN = array(
        "localhost",
        "root",
        "",
        "swzd_GAMETIME"
    );


    /*
        Define routes
    */
    public $Routes = array(
        "/admin" => array(
            "path" => "/admin",
            "accessTo" => array("1"),
            "template" => "admin/index.tpl.php"
        ),

        "/login" => array(
            "path" => "/login",
            "accessTo" => NULL,
            "template" => "login/index.tpl.php"
        ),

        "/home" => array(
            "path" => "/home",
            "accessTo" => array("1"),
            "template" => "home/index.tpl.php"
        ),
        "/inv" => array(
            "path" => "/inv",
            "accessTo" => array("1"),
            "template" => "admin/inv.tpl.php"
        ),
        "/register" => array(
            "path" => "/register",
            "accessTo" => array("1"),
            "template" => "home/register.tpl.php"
        ),
        "/finalizeTransaction" => array(
            "path" => "/finalizeTransaction",
            "accessTo" => array("1"),
            "template" => "home/finalize.tpl.php"
        )
    );


    public function __construct() {
        $this->conn = new mysqli($this->SQL_CONN[0],$this->SQL_CONN[1], $this->SQL_CONN[2],$this->SQL_CONN[3]);
        $this->salesTax = $this->config("salesTax");
        $this->displayName = $this->config("displayName");
    }

    /*
        Authentication
    */
    public function auth($username, $password){

        if(password_verify($password, $this->getPassword($username))){
            $this->createSession($this->getUser($username)["id"]);
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
        Permissions and Access Control
    */

    public function checkRole($id){
        $thisUserID = $this->getUser()["id"];
        $sql = "SELECT role FROM accesscontrol WHERE userID='$thisUserID' AND role='$id'";

        if($this->conn->query($sql)->num_rows > 0){
            return true;
        }
        else{
            return false;
        }
    }

    /*
        Session Management
    */

    public function validateSession($id){

        //SQL query that tests if a session id exists in the database
        $sql = "SELECT sessionID FROM session WHERE sessionID='$id'";

        if($this->conn->query($sql)->num_rows > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function destroySession($id){
        $sql = "DELETE FROM session WHERE sessionID='$id'";

        if($this->conn->query($sql) === TRUE){
            return true;
        }
        else{
            return false;
        }
    }
    public function createSession($userID){

        $sessID = md5(time()*rand(10, 10000));

        $sql = "INSERT INTO session (userID, sessionID) VALUES ('$userID', '$sessID')";

        $result = $this->conn->query($sql);

        if( $result === TRUE){
            setcookie( "session", $sessID, strtotime( '+30 days' ), '/');
            return true;
        }
        else{
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

        $sql = "SELECT userID FROM session WHERE sessionID='$sessID'";

        $result = $this->conn->query($sql);

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                return $row['userID'];
            }
        }
        else{
            return false;
        }
    }


    public function render($postVars, $getVars){

        $ep = $this;

        if(isset($getVars['rt'])){
            $route = $getVars['rt'];
        }
        else {
            $route = "/";
        }

        if($this->checkAuth() && $route == "/"){
            header("Location: ./home");
        } else if($route == "/"){
            header("Location: ./login");
        }

        if(isset($this->Routes[$route])){
            $rt = $this->Routes[$route];

            if(!$this->checkAuth()){
                $roles = "0";
            }
            else{
                $roles = $this->getUserRoles($this->getCurrentUser()['id']);
            }

            $access = false;

            if($roles != "0"){
                foreach ($roles as $role){
                    if(in_array($role, $rt['accessTo'])){
                        $access = true;
                    }
                }
            }

            if($rt["accessTo"] != null){
                if($access){
                    include("templates/".$rt["template"]);
                }
                else{
                    include("templates/errors/403.tpl.php");
                }
            }
            else{
                include("templates/".$rt["template"]);
            }
        }
        else{
            include("templates/errors/404.tpl.php");
        }

    }


}