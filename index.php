<?php
require("src/ModularPHP/ModularPHP.php");

$MP = new ModularPHP();

$MP->render($_POST, $_GET);
