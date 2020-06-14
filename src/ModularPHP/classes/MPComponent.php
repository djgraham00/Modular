<?php

class MPComponent {

    public $template;
    public $ModularPHP;

    public function __construct($p)
    {
        $this->ModularPHP = $p;

        foreach ($p->Modules as $m=>$ref) {
            $this->{$m} = $ref;
        }

    }

    public function __GET($params) {
        $this->template = MPHelper::getError(404);
    }

    public function __POST($params) {
        $this->template = MPHelper::getError(404);
    }

    private function showComponent($cmp) {

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

            $twig = new \Twig\Environment($loader);
            $function = new \Twig\TwigFunction('component', function ($name) {
                $this->ModularPHP->loadComponent(array("component" => $name));
            });

            $twig->addFunction($function);

            //getComponent($name)

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