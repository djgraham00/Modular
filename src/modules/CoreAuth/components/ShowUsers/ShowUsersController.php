<?php

class ShowUsersController extends MPComponent {
    public $users;

    public function __GET($params)
    {
        $this->template = "showUsers.twig";
        $this->users = coreauth_USER::GetAll();

        $this->__render();
    }

    public function __POST($params)
    {
        /*
         * Implement features for when a POST request is sent
         */
    }

}