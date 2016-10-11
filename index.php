<?php
use nigiri\Psr4AutoloaderClass;
use nigiri\Site;

ini_set('display_errors', false);
ini_set('log_errors', false);
ini_set('error_log', __DIR__.'/nigiri_error.log');

require_once __DIR__.'/classes/class_loader.php';
require_once __DIR__.'/includes/functions.php';

set_error_handler('error_to_exception_handler', E_ALL);
set_exception_handler('uncaught_exception_handler');
register_shutdown_function('fatal_error_handler');

$autoloader = new Psr4AutoloaderClass();
$autoloader->register();
$autoloader->addNamespace('nigiri', __DIR__.'/classes');
$autoloader->addNamespace('site', __DIR__);

Site::init(require_once __DIR__.'/includes/settings.php');

Site::getTheme()->append(Site::getRouter()->routeRequest());

Site::printPage();
