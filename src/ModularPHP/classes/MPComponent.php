<?php

class MPComponent {

    public $template;
    public $ModularPHP;

    public function __construct($p)
    {
        $this->ModularPHP = $p;
    }

    public function __index($params) {

    }

    public function __render() {
        foreach ($this->getFields() as $val) {
            ${$val} = $this->{$val};
        }

        $reflection = new ReflectionClass($this);

        include(dirname($reflection->getFileName()) . "\\" . $this->template);
    }

    public function getFields()
    {
        $fields = array();

        foreach ($this as $u => $v) {
            array_push($fields, $u);
        }

        return $fields;
    }

}