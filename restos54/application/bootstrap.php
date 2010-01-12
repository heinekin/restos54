<?php
date_default_timezone_set('Europe/Paris');

if(defined('ENV') !== TRUE) {
	define('ENV','prod');
}

define('RACINE_DIR', $_SERVER['DOCUMENT_ROOT']);
define('CACHE_DIR', RACINE_DIR . '/../data/cache/');
define('LOG_ERROR_DIR', RACINE_DIR . '/../data/log/error/');
define('ROOT_PATH', dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR );

set_include_path('.'
    . PATH_SEPARATOR . '../library/'
    . PATH_SEPARATOR . '../library/smarty/'
    . PATH_SEPARATOR . '../application/models/'
    . PATH_SEPARATOR . get_include_path());

require_once 'My/Initializer.php';
require_once('My/OFC/open-flash-chart.php');

$frontController = Zend_Controller_Front::getInstance();
$frontController->registerPlugin(new My_Initializer(ENV, ROOT_PATH), 1);
$frontController->registerPlugin(new My_Plugin_Auth(), 2);
$frontController->registerPlugin(new My_Plugin_Module(), 3);
$frontController->registerPlugin(new My_Plugin_Acl(), 4);
$frontController->registerPlugin(new My_Plugin_Campagne(), 5);

$frontController->dispatch();