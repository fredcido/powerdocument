<?php

abstract class App_Util_Access
{
    /**
     *
     * @var Zend_Auth
     */
    protected static $_auth;
    
    /**
     *
     * @var array
     */
    protected static $_perm;
    
    /**
     *
     * @var Zend_Session_Namespace
     */
    protected static $_session;
    
    
    const DOWNLOAD = 1;
    
    const UPLOAD = 2;
    
    const EDITAR = 3;
    
    const EXCLUIR = 4;
    
    /**
     * 
     */
    protected static function configSession()
    {
	self::$_auth = Zend_Auth::getInstance();
	
	// Init Session
	$config = Zend_Registry::get( 'config' );

	self::$_session = new Zend_Session_Namespace( $config->geral->appid );
	
	self::$_perm = empty( self::$_session->permissoes ) ? array() : self::$_session->permissoes;
    }
    
    /**
     *
     * @param string $nivel
     * @return bool
     */
    public static function checkNivel( $nivel )
    {
	self::configSession();
		
	switch ( true ) {
	    case !self::$_auth->hasIdentity():
		return false;
		break;
	    case 'A' === $nivel && 'A' !== self::$_auth->getIdentity()->nivel:
		return false;
		break;
	    case !in_array( $nivel, array( 'A', 'U', 'T' ) ):
		return false;
		break;
	    default:
		return true;
	}
    }
    
    /**
     *
     * @param string $module
     * @return bool
     */
    public static function checkByModule( $module )
    {
	return self::checkNivel( $module == 'admin' ? 'A' : 'U' );
    }
    
    /**
     *
     * @param int $acesso
     * @return bool
     */
    public static function checkAccess( $acesso )
    {
	self::configSession();
	
	return in_array( $acesso, self::$_perm );
    }
    
    /**
     *
     * @return bool
     */
    public static function checkLogin()
    {
	self::configSession();
	
	switch ( true ) {
	    case !self::$_auth->hasIdentity():
		return false;
		break;
	    case empty( self::$_session->limite ):
		return false;
		break;
	    default:
		return true;
	}
    }
    
    
    /**
     * 
     */
    public static function clear()
    {
	self::configSession();
	
	self::$_auth->clearIdentity();
	
	unset( self::$_session->permissoes );
	unset( self::$_session->categorias );
    }
}