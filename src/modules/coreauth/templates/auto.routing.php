<?php

if($ModularPHP->hasMod("coreauth"))
{
    $_CoreAuth->loginRedir();

    if($_CoreAuth->enableLoginRedir)
    {
         header("Location: ./login");
         exit;
    }
}