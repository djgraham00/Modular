<?php

if($ModularPHP->hasMod("CoreAuth_Enterprise"))
{

    $_CoreAuth_Enterprise->loginRedir();

    if($_CoreAuth_Enterprise->enableLoginRedir)
    {
         header("Location: ./login");
         exit;
    }
}