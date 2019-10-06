<?php
$conn = new mysqli("localhost", "root", "", "swzd_GAMETIME");

$modules = array_diff(scandir("modules"), array('.', '..'));

$routes = array();

foreach($modules as $mod){

    $subdir = array_diff(scandir("modules/$mod"), array('.', '..'));

    if(file_exists("modules/$mod/$mod.json")){
        $moduleInfo = json_decode(file_get_contents("modules/$mod/$mod.json"));
        
        require ("modules/$mod/".$moduleInfo->ImportFileName);
        $mainClass =  $moduleInfo->MainClass;

        ${$mod} = new $mainClass();

        if($moduleInfo->HasRouteDefinitions){
            $routeDefinitionVar = $moduleInfo->RouteDefinitionVar;
            foreach (${$mod}->{$routeDefinitionVar} as $rt){
                array_push($routes, $rt);
            }
        }

    }
    else{
        echo "Module Configuration Not Found";
    }

    echo "<hr/>";

}

echo "<HR/> <H1>ROUTE VAR DUMP:</H1>";
var_dump($routes);


