<?php

class App_Plugins_Access extends Zend_Controller_Plugin_Abstract
{
    /**
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function routeShutdown( Zend_Controller_Request_Abstract $request )
    {
	$modelConfiguracao = new Model_Configuracao();
	$rowConfig = $modelConfiguracao->fetchRow();
	
	if ( empty( $rowConfig ) )
	    $this->goRoute( $request );
	else {
	
	    $modelIp = new Model_Ip();
	    $rowsIp = $modelIp->fetchAll();

	    $dataIp = array();

	    foreach ( $rowsIp as $rowIp ) 
		$dataIp[] = $rowIp->ip;

	    if ( 'L' === $rowConfig->regra_ip ) {

		if ( !in_array( $_SERVER['REMOTE_ADDR'], $dataIp ) )
			$this->goRoute( $request );

	    } else if ( in_array( $_SERVER['REMOTE_ADDR'], $dataIp ) ) {

		$this->goRoute( $request );

	    }
	
	}
    } 
    
    /**
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    protected function goRoute ( $request )
    {
	Zend_Controller_Front::getInstance()->unregisterPlugin( 'App_Plugins_Auth' );
	
	$request->setModuleName('default')
		->setControllerName('error')
		->setActionName('request')
		->setParam('code', '403');
    }
    
}