<?php
use site\Psr4AutoloaderClass;
use site\Site;

require_once __DIR__.'/classes/class_loader.php';
require_once __DIR__.'/includes/functions.php';

ini_set('display_errors', false);
set_error_handler('error_to_exception_handler', E_ALL);
set_exception_handler('uncaught_exception_handler');
register_shutdown_function('fatal_error_handler');

$autoloader = new Psr4AutoloaderClass();
$autoloader->addNamespace('site', __DIR__.'/classes');
$autoloader->register();

Site::init(require_once __DIR__.'/includes/settings.php');

//TODO page loading

Site::printPage();
