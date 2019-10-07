<?php
/**
 * Created by PhpStorm.
 * User: dgraham
 * Date: 10/6/2019
 * Time: 2:51 PM
 */
$APP_DB_NAME = "bpdev";
$pdo = new PDO("mysql:host=localhost;dbname=$APP_DB_NAME", "root","");

/*
try {


    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE DATABASE $APP_DB_NAME";
    $conn->exec($sql);
    echo "Database successfully created";
}
catch (PDOException $e){
    echo $sql . "<br>" . $e->getMessage();
}

$conn = NULL;
*/

$usr = new coreauth_USER();
$usr->firstName = "Drew";
$usr->lastName = "Graham";
$usr->username = "djgraham";
$usr->password = "supersecretpassword";


//echo $usr->generateSQLModel();


//
//insertObject($usr);

$allUsers = getAllObjects(new coreauth_USER());

echo "<table border=\"1\"><tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Username</th><th>Password</th></tr>";

foreach($allUsers as $user){
    echo "<tr>";
    echo "<td>".$user->id."</td>";
    echo "<td>".$user->firstName."</td>";
    echo "<td>".$user->lastName."</td>";
    echo "<td>".$user->username."</td>";
    echo "<td>".$user->password."</td>";
    echo "</tr>";
}
echo "</table>";
//createTable(new coreauth_USER());

//$tmp = getInstance(new coreauth_USER(), "id", "1");

//echo $tmp->id;

/*
if(tableExists("users")){
    echo "USERS EXISTS";
}
else{
    echo "USERS DOES NOT EXIST";
} */




function insertObject($obj){
    global $pdo;

    if(tableExists($obj->tableName)) {
        try {
            $sql = "INSERT INTO ".$obj->tableName." (".$obj->generateInsertStr().") VALUES (".$obj->generateInsertVal().")";
            $pdo->exec($sql);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    else{
        $objName = get_class($obj);
        createTable(new $objName());
        insertObject($obj);
    }
}

function getInstance($refObj, $key, $value){
    global $pdo;

    try{
        $sql = "SELECT * FROM ".$refObj->tableName. " WHERE $key='$value'";

        $getObjs = $pdo->prepare($sql);
        $getObjs->execute();

        $objs = $getObjs->fetchAll();

        foreach($objs[0] as $key=>$value){
            $refObj->{$key} = $value;
        }

        return $refObj;

    }
    catch (PDOException $e){
        echo $e->getMessage();
    }
}

function getAllObjects($refObj){
    global $pdo;

    try{
        $sql = "SELECT * FROM ".$refObj->tableName;
        $getObjs = $pdo->prepare($sql);
        $getObjs->execute();

        $tmp = array();

        $objs = $getObjs->fetchAll();

        foreach($objs as $obj) {
            $class = get_class($refObj);
            $tmpObj = new $class();
            foreach($obj as $key=>$value){
                $tmpObj->{$key} = $value;
            }
            array_push($tmp, $tmpObj);
        }

        return $tmp;

    }
    catch (PDOException $e){
        echo $e->getMessage();
    }

}

function tableExists($table) {
    global $pdo;
    // Try a select statement against the table
    // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
    try {
        $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
    } catch (Exception $e) {
        // We got an exception == table not found
        return FALSE;
    }

    // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
    return $result !== FALSE;
}

function createTable($obj){
    global $pdo;
    // Try a select statement against the table
    // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
    try {
        $result = $pdo->exec($obj->generateSQLModel());
    } catch (Exception $e) {
        // We got an exception == table not found

        echo $e->getMessage();
    }

    // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
    return $result !== FALSE;
}