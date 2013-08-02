<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Define path to application directory
//defined('PUBLIC_PATH')
//    || define('PUBLIC_PATH', realpath(dirname(__FILE__) . '.'));

defined('PUBLIC_PATH')
    || define('PUBLIC_PATH', realpath(dirname(__FILE__)));


// Define path to customer_image directory
defined('IMAGE_PATH')
    || define('IMAGE_PATH', realpath(dirname(__FILE__) . '/images'));

    
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/models'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

//error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('America/Argentina/Buenos_Aires');

set_include_path('.' . PATH_SEPARATOR . './library' . PATH_SEPARATOR . get_include_path());

Zend_Session::start();

$application->bootstrap()
            ->run();