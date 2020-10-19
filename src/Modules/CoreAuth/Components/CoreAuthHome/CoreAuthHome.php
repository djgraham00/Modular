<?php
namespace CoreAuth;

class CoreAuthHome extends \Modular\Component {

    public function Get($params)
    {

        //$this->_CoreAuth->requireAuth();
        $this->template = "home.twig";

        $this->__render();
    }
}