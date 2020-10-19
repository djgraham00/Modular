<?php

namespace test;

class Index extends \Modular\Component {

    public $template = "test.twig";

    public $fruits = ["Apple", "Orange", "Banana"];

    public function Get($params)
    {
        $this->render();
    }

}