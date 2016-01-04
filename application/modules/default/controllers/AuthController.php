<?php

class AuthController extends Zend_Controller_Action
{

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	$this->_helper->layout()->setLayout( 'auth' );

	$this->_helper->viewRenderer->setRender( 'index' );
    }

    /**
     * 
     * Enter description here ...
     */
    public function indexAction()
    {
	
    }

    /**
     * 
     * Enter description here ...
     */
    public function loginAction()
    {
	if ( 
		Zend_Auth::getInstance()->hasIdentity() || 
		!$this->getRequest()->isXmlHttpRequest()
	) {
	    $this->_helper->redirector->goToSimple( 'index', 'index' );
	    return;
	}

	$config = Zend_Registry::get( 'config' );
	$session = new Zend_Session_Namespace( $config->geral->appid );

	$rota = empty( $session->triedroute ) ?
		$this->_helper->url( 'index', 'index' ) :
		$session->triedroute;
	
	$session->triedroute = null;
	unset( $session->triedroute );
	
	$result = array(
	    'redirect' => $rota,
	    'valid' => false
	);

	if ( $this->getRequest()->isPost() ) {

	    $data = $this->getRequest()->getPost();

	    $modelUsuario = new Model_Usuario();
	    $modelUsuario->setData( $data );

	    $result['valid'] = $modelUsuario->login();
	}

	$this->_helper->json( $result );
    }

    /**
     * 
     * Enter description here ...
     */
    public function logoutAction()
    {
	App_Util_Access::clear();

	// Limpa essas merda de cookie
	setcookie( $this->_config->geral->cookie, false );

	$this->_helper->redirector->gotoSimple( 'index' );
    }

    /**
     * 
     * Enter description here ...
     */
    public function passwordAction()
    {
	$modelUsuario = new Model_Usuario();

	if ( $this->getRequest()->isPost() ) {

	    $data = $this->getRequest()->getPost();

	    $modelUsuario->setData( $data );

	    $result = array('valid' => $modelUsuario->checkEmailPasswordRecovery());

	    $this->_helper->json( $result );
	} else {

	    $hash = $this->_getParam( 'hash' );

	    if ( empty( $hash ) )
		return $this->_helper->redirector->gotoSimple( 'index' );

	    $modelUsuario->setData( $hash );

	    $this->view->result = $modelUsuario->passwordRecovery();

	    $this->_helper->viewRenderer->setRender( 'password' );
	}
    }
    
    public function viewFileAction()
    {
	$this->_helper->viewRenderer->setNoRender();
	$this->_helper->layout->disableLayout();

	$modelArquivo = new Model_Arquivo();
	$arquivo = $modelArquivo->getArquivoView( $this->_getParam( 'hash' ) );

	if ( empty( $arquivo ) )
	    //$this->_helper->redirector->goToSimple( 'request', 'error', 'default', array( 'code' => '403' ) );
	    $this->getResponse()->clearHeaders()->setHttpResponseCode(403)->appendBody("Forbidden")->sendResponse();
	else {
	    
	    $filePath = APPLICATION_PATH . '/../files/' . $arquivo->path . '.' . $arquivo->extensao;
	    if ( !file_exists( $filePath ) )
		$this->_helper->redirector->goToSimple( 'request', 'error', 'default', array( 'code' => '403' ) );
	    else {
		
		$mimeFinder = new App_Util_Mime();
		$mime = $mimeFinder->getMimeFile( $arquivo->extensao );
		
		$this->getResponse()
		    ->setHeader('Content-type', $mime[0] );
		
		$this->getResponse()->setBody( file_get_contents( $filePath ) );
	    }
	}
    }

}