<?php

/**
 * 
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * 
     * Enter description here ...
     */
    protected function _initConfig ()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/config.ini');
        
        Zend_Registry::set('config', $config);
    }
    
    protected function _initCacheDir()
    {
	$frontendOptions = array( 'lifetime' => 86400, 
				  'automatic_serialization' => true, 
				  'automatic_cleaning_factor' => 1 );

	$backendOptions  = array( 'cache_dir' => APPLICATION_PATH . '/cache');

	$cache = Zend_Cache::factory( 'Core', 'File', $frontendOptions, $backendOptions );
	Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
	Zend_Date::setOptions(
		array( 'cache' => $cache )
	);
    }
    
    protected function _initNavigation ()
    {
        $view = $this->bootstrap('view')->getResource('view');
        
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml', 'nav');
        
        $navigation = new Zend_Navigation($config);
        
        $view->navigation($navigation);
    }
    
    protected function _initRoutes ()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini');
        
        $frontController = Zend_Controller_Front::getInstance();
        
        $router = $frontController->getRouter();
        $router->addConfig($config, 'routes');
    }
    
    protected function _initTranslate ()
    {
        $translate = new Zend_Translate('array', APPLICATION_PATH . '/language/pt_br.php', 'pt_Br', array('disableNotices' => true));
        
        $registry = Zend_Registry::getInstance();
        $registry->set('translate', $translate);
        
        Zend_Validate_Abstract::setDefaultTranslator($translate);
    }
    
    protected function _initAutoload ()
    {
        new Zend_Application_Module_Autoloader(
        	array(
        		'basePath' => APPLICATION_PATH, 
        		'namespace' => ''
        	)
        );
        
        new Zend_Application_Module_Autoloader(
        	array(
        		'namespace' => 'Admin', 
        		'basePath' => APPLICATION_PATH . '/modules/admin')
        	);
        	
        new Zend_Application_Module_Autoloader(
        	array(
        		'namespace' => 'Default', 
        		'basePath' => APPLICATION_PATH . '/modules/default'
        	)
        );
    }
}