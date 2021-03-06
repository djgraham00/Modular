<?php

class MPDatabaseHandler {
    public static function GET_APP_CFG() {
        return json_decode(file_get_contents(__DIR__."\\..\\..\\application.json"));
    }

    private static function GET_PDO() {
        $cfg = MPDatabaseHandler::GET_APP_CFG();
        return new PDO($cfg->APP_DB_TYPE . ":host=" . $cfg->APP_DB_HOST . ";dbname=" . $cfg->APP_DB_NAME, $cfg->APP_DB_USER, $cfg->APP_DB_PASS);
    }

    public static function GET_SQL_CONNECTION() {
        $cfg = MPDatabaseHandler::GET_APP_CFG();
        return new mysqli($cfg->APP_DB_HOST, $cfg->APP_DB_USER, $cfg->APP_DB_PASS, $cfg->APP_DB_NAME);
    }

    public static function insertObject($obj){

        if(MPDatabaseHandler::tableExists($obj->tableName)) {
            try {
                $sql = "INSERT INTO ".$obj->tableName." (".$obj->generateInsertStr().") VALUES (".$obj->generateInsertVal().")";
                MPDatabaseHandler::GET_PDO()->exec($sql);
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
        else{
            $objName = get_class($obj);
            MPDatabaseHandler::createTable(new $objName());
            MPDatabaseHandler::insertObject($obj);
        }
    }

    public static function deleteObject ($ref) {
        try {
            // Delete Row
            $sql = "DELETE FROM {$ref->tableName} WHERE id=".$ref->id;

            // use exec() because no results are returned
            MPDatabaseHandler::GET_PDO()->exec($sql);

            return true;
        } catch(PDOException $e) {
            return false;
        }
    }

    public static function getObject($ref, $where = array(), $opts = array()){

        $whereSQL = "";
        $isFirst = true;

        foreach ($where as $key=>$val) {
            if($isFirst) {
                $whereSQL .= " WHERE $key='$val'";
                $isFirst = false;
            }
            $whereSQL .= " AND $key='$val'";
        }

        $optsSQL = "";

        foreach ($opts as $opt) {
            $optsSQL .= "$opt ";
        }

        if(!MPDatabaseHandler::tableExists($ref->tableName)){
            $objName = get_class($ref);
            MPDatabaseHandler::createTable(new $objName());
            MPDatabaseHandler::getObject($ref, $where, $opts);
        }

        try{

            $sql = "SELECT * FROM ".$ref->tableName." {$whereSQL} {$optsSQL} LIMIT 1";

            $getObjs = MPDatabaseHandler::GET_PDO()->prepare($sql);
            $getObjs->execute();

            $objs = $getObjs->fetchAll();

            $class = get_class($ref);
            $tmp = new $class();

            if($objs != FALSE) {
                foreach ($objs[0] as $key => $value) {
                    $tmp->{$key} = $value;
                }
            }
            else{
                return false;
            }

            return $tmp;

        }
        catch (PDOException $e){
            echo $e->getMessage();
        }
    }

    public static function getObjects($ref, $where = array(), $opts = array()) {

        $whereSQL = "";
        $isFirst = false;

        $whereSQL = "";
        $isFirst = true;

        foreach ($where as $key=>$val) {
            if($isFirst) {
                $whereSQL .= " WHERE $key='$val'";
                $isFirst = false;
            }
            $whereSQL .= " AND $key='$val'";
        }

        $optsSQL = "";

        foreach ($opts as $opt) {
            $optsSQL .= "$opt ";
        }


        if(!MPDatabaseHandler::tableExists($ref->tableName)){
            $objName = get_class($ref);
            MPDatabaseHandler::createTable(new $objName());
            MPDatabaseHandler::getObject($ref, $where, $opts);
        }

        try{

            $sql = "SELECT * FROM ".$ref->tableName."{$whereSQL} {$optsSQL}";
            $getObjs = MPDatabaseHandler::GET_PDO()->prepare($sql);
            $getObjs->execute();

            $tmp = array();

            $objs = $getObjs->fetchAll();

            foreach($objs as $obj) {
                $class = get_class($ref);
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

    /* Backwards-compatibility functions */
    public static function getAllObjects($refObj) {
        return MPDatabaseHandler::getObjects($refObj, array(), array());
    }

    public static function getInstance($refObj, $key, $value, $customSQL = false, $orderByID = "ASC", $limit = false)
    {

        $opts = array();
        array_push($opts, "ORDER BY id {$orderByID}");

        if ($limit) {
            array_push($opts, "LIMIT $limit");
        }

        return MPDatabaseHandler::getObject($refObj, array($key => $value), $opts);
    }


    public static function tableExists($table) {
        // Try a select statement against the table
        // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
        try {
            $result = MPDatabaseHandler::GET_PDO()->query("SELECT * FROM $table LIMIT 1");
        } catch (Exception $e) {
            // We got an exception == table not found
            return FALSE;
        }

        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return true !== FALSE;
    }

    public static function conditionalDeleteRow($objRef, $key, $value) {
        $table = $objRef->tableName;
        // Try a select statement against the table
        // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
        try {
            $result = MPDatabaseHandler::GET_PDO()->query("DELETE FROM $table WHERE $key='$value'");
        } catch (Exception $e) {
            // We got an exception == table not found
            return FALSE;
        }

        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return true;
    }


    public static function createTable($obj){
        // Try a select statement against the table
        // Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
        try {
            $result = MPDatabaseHandler::GET_PDO()->exec($obj->generateSQLModel());
        } catch (Exception $e) {
            // We got an exception == table not found
            return false;
        }

        // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
        return $result !== FALSE;

    }
}