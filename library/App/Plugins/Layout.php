<?php

class App_Plugins_Layout extends Zend_Controller_Plugin_Abstract
{

    /**
     * 
     * @var unknown_type
     */
    protected $_request;

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Plugin_Abstract::dispatchLoopStartup()
     */
    public function dispatchLoopStartup( Zend_Controller_Request_Abstract $request )
    {
	$this->_request = $request;

	$this->_configView();
	$this->_configNavigation();
	$this->_includeJsController();
	$this->_includeCssController();
    }

    /**
     * 
     * Enter description here ...
     */
    protected function _configView()
    {
	$view = Zend_Controller_Front::getInstance()->getParam( 'bootstrap' )->getResource( 'view' );

	$config = Zend_Registry::get( 'config' );

	$view->headTitle( $config->geral->title );

	//Favicon
//	$view->headLink(
//		array(
//		    'rel' => 'shortcut icon',
//		    'type' => 'image/x-icon',
//		    'href' => $view->baseUrl( 'favicon.ico' )
//		)
//	);
//	$view->headLink(
//		array(
//		    'rel' => 'icon',
//		    'type' => 'image/png',
//		    'href' => $view->baseUrl( 'favicon.png' )
//		)
//	);

	// Global stylesheets
	$view->headLink()->appendStylesheet( $view->baseUrl( 'public/styles/reset.css' ), '' );
	$view->headLink()->appendStylesheet( $view->baseUrl( 'public/styles/common.css' ), '' );
	$view->headLink()->appendStylesheet( $view->baseUrl( 'public/styles/form.css' ), '' );
	$view->headLink()->appendStylesheet( $view->baseUrl( 'public/styles/standard.css' ), '' );
	$view->headLink()->appendStylesheet( $view->baseUrl( 'public/styles/geral.css' ), '' );

	// Comment/uncomment one of these files to toggle between fixed and fluid layout
	$view->headLink()->appendStylesheet( $view->baseUrl( 'public/styles/960.gs.fluid.css' ), '' );

	// Custom styles
	$view->headLink()->appendStylesheet( $view->baseUrl( 'public/styles/simple-lists.css' ), '' );
	$view->headLink()->appendStylesheet( $view->baseUrl( 'public/styles/block-lists.css' ), '' );
	$view->headLink()->appendStylesheet( $view->baseUrl( 'public/styles/planning.css' ), '' );
	$view->headLink()->appendStylesheet( $view->baseUrl( 'public/styles/table.css' ), '' );
	$view->headLink()->appendStylesheet( $view->baseUrl( 'public/styles/calendars.css' ), '' );
	$view->headLink()->appendStylesheet( $view->baseUrl( 'public/styles/wizard.css' ), '' );
	$view->headLink()->appendStylesheet( $view->baseUrl( 'public/styles/gallery.css' ), '' );

	// Generic libs
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/html5.js' ) );
	//$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/jquery-1.4.2.min.js' ) );
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/jquery-1.11.0.min.js' ) );
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/jquery-migrate-1.2.1.js' ) );
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/old-browsers.js' ) );
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/jquery.form.js' ) );

	// Template libs
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/jquery.accessibleList.js' ) );
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/searchField.js' ) );
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/common.js' ) );
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/standard.js' ) );

	// if IE8
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/standard.ie.js' ), 'text/javascript', array('conditional' => 'lte IE 8') );

	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/jquery.tip.js' ) );
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/jquery.hashchange.js' ) );
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/jquery.contextMenu.js' ) );
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/jquery.modal.js' ) );

	//Custom styles lib
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/list.js' ) );

	//Plugins 
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/jquery.dataTables.min.js' ) );
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/jquery.datepick/jquery.datepick.min.js' ) );

	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/ajaxupload.js' ) );

	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/geral.js' ) );

	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/jquery.maskedinput.js' ) );

	// Highcharts
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/highcharts/highcharts.js' ) );
	$view->headScript()->appendFile( $view->baseUrl( 'public/scripts/js/highcharts/modules/exporting.js' ) );
    }

    /**
     * 
     * Enter description here ...
     */
    protected function _configNavigation()
    {
	$view = Zend_Controller_Front::getInstance()->getParam( 'bootstrap' )->getResource( 'view' );

	// Define controller active
	$controllerActive = $view->navigation()->findOneBy( 'controller', $this->_request->getControllerName() );

	if ( $controllerActive )
	    $controllerActive->setActive( true );
    }

    protected function _includeJsController()
    {
	$ds = '/';//DIRECTORY_SEPARATOR;

	$file = 'public' . $ds
		. 'scripts' . $ds
		. 'js' . $ds
		. 'controller' . $ds
		. $this->_request->getControllerName() . '.js';

	// Se arquivo de js exclusivo para controller existir, insere
	if ( file_exists( APPLICATION_PATH . $ds . '..' . $ds . $file ) ) {

	    $view = Zend_Controller_Front::getInstance()->getParam( 'bootstrap' )->getResource( 'view' );

	    $view->headScript()->appendFile( $view->baseUrl( $file ) );
	}
    }

    /**
     * 
     * Enter description here ...
     */
    protected function _includeCssController()
    {
	$ds = '/'; //DIRECTORY_SEPARATOR;

	$file = 'public' . $ds
		. 'styles' . $ds
		. 'controller' . $ds
		. $this->_request->getControllerName() . '.css';

	// Se arquivo css exclusivo para controller existir, insere
	if ( file_exists( APPLICATION_PATH . $ds . '..' . $ds . $file ) ) {
	    $view = Zend_Controller_Front::getInstance()->getParam( 'bootstrap' )->getResource( 'view' );
	    $view->headLink()->appendStylesheet( $view->baseUrl( $file ) );
	}
    }

}