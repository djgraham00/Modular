<?php

class MPString {}

class MPInt {

    public static $sqlInit = "int(%len%) NOT NULL";

    public static function init($len = 11) {
        return preg_replace('/%len%/', $len, MPInt::$sqlInit);
    }

}

class MPRef {}

