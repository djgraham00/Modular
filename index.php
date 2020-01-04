<?php
require("src/ModularPHP.php");

$bp = new ModularPHP();

$bp->render($_POST, $_GET);
