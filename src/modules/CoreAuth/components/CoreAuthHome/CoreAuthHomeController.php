<?php

class CoreAuthHomeController extends MPComponent {

    public function __GET($params)
    {

        $this->_CoreAuth->requireAuth();
        $this->template = "home.twig";

        $this->__render();
    }
}