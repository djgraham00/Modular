<?php
header("Content-Type: application/json");

error_reporting(0);
ini_set('display_errors', 0);
if($_CoreAuth_Enterprise->deAuth()){
    echo '{ "success" : true }';
}
else {
    echo '{ "success" : false }';
}