<?php
/**
 * Created by PhpStorm.
 * User: dgraham
 * Date: 10/6/2019
 * Time: 7:58 PM
 */
header("Content-Type: application/json");
error_reporting(0);
ini_set('display_errors', 0);

if($_CoreAuth_Enterprise->auth($_POST['username'], $_POST['password'])){
    echo '{ "success" : true }';
}
else {
    echo '{ "success" : false }';
}