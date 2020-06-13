<?php

class MPModule {

    public $Parent;
    public $Config;

    public function __construct($p, $c) {
        $this->Parent = $p;
        $this->Config = $c;
        $this->loadComponents();
        $this->init();
    }

    protected function init() { }

    private function loadComponents () {
        $reflection = new ReflectionClass($this);

        $cmpDir = dirname($reflection->getFileName()) . "\\".$this->Config->MOD_COMPONENT_DIR;

        $components = array_diff(scandir($cmpDir), array('.', '..'));

        foreach ($components as $cmp) {
            if (file_exists("$cmpDir/$cmp/{$cmp}Controller.php")) {
                require("$cmpDir/$cmp/{$cmp}Controller.php");
            }
        }
    }

    public function getComponent($name) {
        $name = $name."Controller";

        $cmp = new $name($this->Parent);

        $cmp->__index($_GET);

        $cmp->__render();

    }

}