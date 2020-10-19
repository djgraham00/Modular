<img src="https://github.com/djgraham00/djgraham00.github.io/raw/master/modular_logo.png" alt="Modular Logo" width="300"/>
A Modular PHP Web Application Development Framework

### IMPORTANT
Modular v3 introduces some major changes in the routing engine and structure of modules. Modules created for any previous version of modular will require significant refactoring to be compatible with Module v3.

## Major Changes 
- Support for legacy templates has been removed, all pages must be defined as components
- Modules no longer contain a PHP module class
- Routes are no longer staticlly defined, and are now based on module and component (for example, a page called Index in the module Test would be https://{app_url}/test.Index)
- Built-in support for mysqli has been removed in favor of PDO
- Modular is now entirely namespaced! All modules are also requried to be namespaced.

## New Features
- Introduction of the QueryBuilder system for querying the database
- Helper file for easily creating new Modules and Components (WORK IN PROGRESS)

## Dependencies
Modular only uses features builtin to PHP 7.4 and uses an integrated copy of Twig for templates. It is designed to run on Apache Web Server, but could be modified to run on NGINX or IIS fairly easily (just create the proper rules for URL redirection, using the .htaccess file provided for reference). Modular relies heavily on JSON files for configuration. It is highly recomended to disallow access to files in the src directory using either .htaccess if using Apache, or using your webservers' equavialent.

## Getting Started
This guide will provide some general information on setting up a development enviornment for ModularPHP.

### Modules
Modular is very centric on the idea that related portions of the application be separated into specific modules. For example, Modular includes a module called CoreAuth that provides a basic system for user authentication. To create a module, first create a folder within your modules folder (by default it is src/modules). Within that folder, you will need to create a configuration file for your new module. This file must be named Module.json. This JSON file contains all of the options and rules that Modular will follow when loading your module. 

## JSON Configuration Files
#### Application.json
```  
{
  "APP_NAME"        : "YOUR_APP_NAME",
  "APP_MODULE_DIR"  : "Modules",
  "APP_ENTRY_POINT" : "YOUR_APP_ENTRY_POINT",
  "APP_DB_TYPE"     : "mysql",
  "APP_DB_HOST"     : "localhost",
  "APP_DB_NAME"     : "YOUR_APP_DB_NAME",
  "APP_DB_USER"     : "YOUR_APP_DB_USER",
  "APP_DB_PASS"     : "YOUR_APP_DB_PASS"
}
```

#### Module.json
```
{
  "MOD_PACKAGE"        : "test",
  "MOD_DEPENDENCIES"   : [],
  "MOD_HAS_MODELS"     : true,
  "MOD_MODEL_AUTOINIT" : false
}
```

## Contact
If you have any questions about Modular or would like to help with the project, please feel free to send me an email to d@drewjgraham.com.
