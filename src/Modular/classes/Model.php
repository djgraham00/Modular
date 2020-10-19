<?php
namespace Modular;

class Model {
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



            if(!isset($this->{$f})) {
                $val = "VARCHAR(255)";
            } else {
                $val = $this->{$f};
            }

            $model .= "`$f`" . " " . $val;
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
                if(!MPHelper::contains("int", $this->{$f})) {
                    $fieldsStr .= ",$f";
                    if ($count < count($fields) - 1) {
                        $fieldsStr .= ",";
                    }
                }
            } else {
                if ($f != "id" && $f != "tableName") {
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

            $safeVal = addslashes($v);

            if($f == "id") {
                if(!MPHelper::contains("int", $v)) {
                    $fieldsStr .= ",'{$safeVal}'";
                    if ($count < count($this->getFields())) {
                        $fieldsStr .= ",";
                    }
                }
            } else {
                if($f != "tableName") {
                    $fieldsStr .= "'{$safeVal}'";
                    if ($count < count($this->getFields())) {
                        $fieldsStr .= ",";
                    }
                }
            }
            $count++;
        }

        return $fieldsStr;
    }


    public function generateUpdateString() {

        //return json_encode($this->getFields());

        $str = "UPDATE {$this->tableName} SET ";

        $count = count($this->getFields());
        $inc = 0;

        foreach ($this->getFields() as $f) {

            $str .= "`{$f}` = '{$this->{$f}}',";

            $inc++;
        }

        $str = substr_replace($str ,"",-1);

        $str .= " WHERE id='{$this->id}'";

        return $str;

    }

    public function save() {
        $return = true;

        if ($this->id != "int(11) NOT NULL") {
            $return = $return & DBHelper::updateObject($this);
        } else {
            $return .= $return & DBHelper::insertObject($this);
        }

        return $return;
    }

    public static function Reference() {
        $class = static::class;
        return new $class();
    }

    public static function GetWhereID($id) {

        $class = static::class;
        $obj = new $class();

        return DBHelper::getInstance($obj, "id", $id);

    }

    public static function GetAll() {
        $class = static::class;
        $obj = new $class();
        return DBHelper::getAllObjects($obj);
    }

    public static function GetWhere($where) {
        $class = static::class;
        $obj = new $class();
        return DBHelper::getObject($obj, $where, array());
    }

    public static function GetAllWhere($where) {
        $class = static::class;
        $obj = new $class();
        return DBHelper::getObjects($obj, $where);
    }

    public function __set($name,$value) {
        switch ($name) {
            default:
                // we don't allow any magic properties set or overwriting our properties
        }
    }

    public function __get($name)
    {
        switch ($name) {
            default:
                // we don't allow any magic properties
        }
        return null;
    }

    public function __isset($name)
    {
        switch ($name) {
            default:
                return false;
        }
    }

    public static function init() {
        $class = static::class;
        $obj = new $class();
        return DBHelper::createTable($obj);
    }

    public static function Get($f = FALSE) {

        $class = static::class;
        $obj = new $class();

        return new QueryBuilder("SELECT", $f, $obj->tableName, $obj);
    }

    public static function Select($query, $f = false) {
        $class = static::class;
        $obj = new $class();

        $qb = new QueryBuilder("SELECT", $f, $obj->tableName, $obj);
        $qb->Query = $query;

        return $qb->exec();

    }

}