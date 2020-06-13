<?php

class ShowUsersController extends MPComponent {
    public $template = "showUsers.twig";

    public $users;

    public function __index($params)
    {
        $this->users = coreauth_USER::GetAll();
    }

}