<?php

class App_Plugins_Auth extends Zend_Controller_Plugin_Abstract
{
    /**
     *
     * @var Zend_Controller_Request_Abstract 
     */
    protected $_request;
    
    /**
     *
     * @var Zend_Auth
     */
    protected $_auth;
    
    /**
     *
     * @var Zend_Config
     */
    protected $_config;
    
    /**
     *
     * @var array
     */
    protected $_noAuth = array(
	'module'	=> 'default',
	'controller'	=> 'auth',
	'action'	=> 'index'
    );
    
    /**
     *
     * @var array
     */
    protected $_noAdmin = array(
	'module'	=> 'default',
	'controller'	=> 'error',
	'action'	=> 'request'
    );
    
    /**
     * 
     */
    public function __construct()
    {
	$this->_config = $config = Zend_Registry::get( 'config' );
	
	$this->_auth = Zend_Auth::getInstance();
	
	// Define storage de auth
	$this->_auth->setStorage( new Zend_Auth_Storage_Session( 'Auth_' . ucfirst( $this->_config->geral->appid ) ) );
	
	$this->_session = new Zend_Session_Namespace( $this->_config->geral->appid );
    }
    
    
    /**
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function dispatchLoopStartup( Zend_Controller_Request_Abstract $request )
    {
	$this->_request = $request;
	
	switch ( true )
	{
	    case 'auth' == $this->_request->getControllerName() :
	    case 'error' == $this->_request->getControllerName() :
		
		return true;
		break;
	    case !App_Util_Access::checkLogin():
		
		if ( isset( $_COOKIE[$this->_config->geral->cookie] ) ) {

		    $usuarioDb = new Model_DbTable_Usuario();
		    $usuario = $usuarioDb->fetchRow( $usuarioDb->select()->where('keeplogged = ?', $_COOKIE[$this->_config->geral->cookie] ) );
		    
		    if ( !empty( $usuario ) ) {
			
			$dados = $usuario->toArray();
			$dados['keep-logged'] = false;
			
			$modelUsuario = new Model_Usuario();
			$modelUsuario->setData( $dados );
				
			if ( !$modelUsuario->login( false ) )
			    $this->_routeNoAuth();
			    
		    } else
			$this->_routeNoAuth();

		} else $this->_routeNoAuth();
		
		break;
	    case !App_Util_Access::checkByModule( $this->_request->getModuleName() ) :
		
		$this->_request->setParam('code', '403');
		$this->_setRoute( $this->_noAdmin );
		break;
	}
    }
    
    /**
     * 
     */
    protected function _routeNoAuth()
    {
	App_Util_Access::clear();
	$baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
	
	if ( !$this->_request->isXMLHttpRequest() )
	    $this->_session->triedroute = str_replace( $baseUrl, '', $this->_request->getRequestUri() );
	else {
	    
	    $helperBroker = Zend_Controller_Action_HelperBroker::getStaticHelper( 'json' );
	    $helperBroker->direct( array( 'error' => true, 'status' => false, 'logout' => true ) );
	}
	
	$this->_setRoute( $this->_noAuth );
    }
    
    /**
     * @param array $rota
     */
    protected function _setRoute( array $rota )
    {
	$this->_request->setControllerName( $rota['controller'] );
	$this->_request->setModuleName( $rota['module'] );
	$this->_request->setActionName( $rota['action'] );
    }
}