<?php

class ShowUsersController extends MPComponent {
    public $template = "showUsers.php";

    public $users;

    public function __index($params)
    {
        $this->users = coreauth_USER::GetAll();
    }

}