<?php
class BPDevApp {

    public $rts = array(
        "/" => array(
            "path" => "/",
            "accessTo" => array("1"),
            "template" => "modules/bpdev/templates/index.tpl.php"
        )
    );

    public $Parent;
    public $Config;

    public function __construct($p, $c){
        $this->Parent = $p;
        $this->Config = $c;
    }

}