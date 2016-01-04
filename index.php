<?php

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));
	
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../library/dompdf/',
    realpath(APPLICATION_PATH . '/../library')
)));

require_once 'Zend/Application.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

try {

    $application->bootstrap()
                ->run();
} catch ( Exception $e ) {

    $view = new Zend_View();

    $view->exception = $e;
    $view->setBasePath( APPLICATION_PATH . '/modules/default/views' );
    echo $view->render( 'error/error.phtml' );
}
