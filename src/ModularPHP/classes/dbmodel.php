<?php

require ("MPDatabaseHandler.php");

class dbmodel {

    public $tableName = "";
    public $id = "int(11) NOT NULL";

    public function getFields()
    {
        $fields = array();

        foreach ($this as $u => $v) {
            if ($u != "tableName") {
                array_push($fields, $u);
            }
        }

        return $fields;
    }

    public function generateSQLModel()
    {
        $tableName = $this->tableName;
        $fields = $this->getFields();

        $model = "CREATE TABLE `$tableName` (";
        $count = 0;
        foreach ($fields as $f) {
            $model .= "`$f`" . " " . $this->{$f};
            if ($count < count($fields) - 1) {
                $model .= ",";
            }
            $count++;
        }
        $model .= "); ALTER TABLE `" . $this->tableName . "` ADD PRIMARY KEY (`id`); ALTER TABLE `" . $this->tableName . "` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;";

        return $model;

    }

    public function generateInsertStr()
    {

        $fields = $this->getFields();

        $fieldsStr = "";
        $count = 1;
        foreach ($fields as $f) {
            if($f == "id") {
                if($f !=  "int(11) NOT NULL") {
                    $fieldsStr .= "$f";
                    if ($count < count($fields) - 1) {
                        $fieldsStr .= ",";
                    }
                }
            } else {
                if ($f != "id") {
                    $fieldsStr .= "$f";
                    if ($count < count($fields) - 1) {
                        $fieldsStr .= ",";
                    }
                }
            }
            $count++;
        }

        return $fieldsStr;
    }

    public function generateInsertVal()
    {
        $fieldsStr = "";
        $count = 1;
        foreach ($this as $f => $v) {
            if ($f != "id" && $f != "tableName") {
                $fieldsStr .= "'$v'";
                if ($count < count($this->getFields())) {
                    $fieldsStr .= ",";
                }
            }
            $count++;
        }

        return $fieldsStr;
    }

    public function save() {
        $return = false;

        if ($this->id != "int(11) NOT NULL") {
            $return = MPDatabaseHandler::deleteObject($this);
            $return = $return & MPDatabaseHandler::insertObject($this);
        } else {
            $return = $return & MPDatabaseHandler::insertObject($this);
        }

        return $return;
    }

    public static function GetWhereID($id) {

        $class = static::class;
        $obj = new $class();

        return MPDatabaseHandler::getInstance($obj, "id", $id);

    }

    public static function GetAll() {
        $class = static::class;
        $obj = new $class();
        return MPDatabaseHandler::getAllObjects($obj);
    }

    public static function GetWhere($where) {

    }

    public static function GetAllWhere($where) {

    }

}
