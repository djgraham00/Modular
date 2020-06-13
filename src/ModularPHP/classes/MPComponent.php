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

        if(MPHelper::contains(".twig", $this->template)) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname($reflection->getFileName()) );
            $twig = new \Twig\Environment($loader, [
                'cache' => __dir__.'/../cache',
            ]);

            $twigArray = array();

            foreach ($this as $k => $v) {
                $twigArray[$k] = $v;
            }

            echo $twig->render($this->template, $twigArray);

        } else {
            include(dirname($reflection->getFileName()) . "\\" . $this->template);
        }

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