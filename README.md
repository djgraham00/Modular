# ModularPHP
A easy to use, and simple web application framework for modular development.

### IMPORTANT
The overall stucture of web application created using ModularPHP is not fully decided, so for now compatibility of modules between different ModularPHP versions is not garanteed, however we will try our best not to break compatiblity.

## Dependencies
ModularPHP only uses features builtin to PHP 7 and does not depend on any other libraries. It is designed to run on Apache Web Server, but could be modified to run on NGINX or IIS fairly easily (just create the proper rules for URL redirection, using the .htaccess file provided for reference). ModularPHP relies heavily on JSON files for configuration. It is highly recomended to disallow access to files in the src directory using either .htaccess if using Apache, or using your webservers' equavialent.

## Getting Started
This guide will provide some general information on setting up a development enviornment for ModularPHP.

### Modules
ModularPHP is very centric on the idea that related portions of the application be separated into specific modules. For example, ModularPHP includes a module called CoreAuth that provides a basic system for user authentication. To create a module, first create a folder within your modules folder (by default it is src/modules). Within that folder, you will need to create a configuration file for your new module. This file must be named the exact same (case-sensitive) as the folder it is within (Example: you created a folder inside of modules called ControlPanel, you would need a file inside of that folder called ControlPanel.json). This JSON file contains all of the options and rules that ModularPHP will follow when loading your module. 

#### JSON Configuration File
```
{
    "ImportFileName"      : "CoreAuth.php",
    "MainClass"           : "CoreAuth",
    "HasRouteDefinitions" : true,
    "RouteDefinitionVar"  : "rts",
    "HasDBModels"         : true,
    "MOD_MODEL_DIR"       : "models",
    "ModelsInitMethod"    : "__initModels",
    "LoginRdir"           : "./coreAuthHome"
}
```
