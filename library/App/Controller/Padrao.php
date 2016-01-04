<?php

/**
 * 
 */
abstract class App_Controller_Padrao extends Zend_Controller_Action
{

    /**
     * @var mixed
     */
    protected $_form;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * @var Zend_Config_Ini
     */
    protected $_config;

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::preDispatch()
     */
    public function preDispatch()
    {
	$this->_config = Zend_Registry::get( 'config' );

	$this->_session = new Zend_Session_Namespace( $this->_config->geral->appid );

	$this->_hookAction( 'pre' );
    }

    public function postDispatch()
    {
	$this->_hookAction( 'post' );
    }

    /**
     * @access 	public
     * @return 	void
     */
    public function indexAction()
    {
	$rows = $this->_model->fetchAll();
	
	$this->view->rows = $rows;
    }

    protected function _hookAction( $moment )
    {
	// Pega o nome do action
	$action = $this->getRequest()->getActionName();

	$method = $this->_parseActionName( $action ) . ucfirst( $moment ) . 'Hook';

	// Ve se existe o gancho para action
	if ( method_exists( $this, $method ) )
	    call_user_func( array( $this, $method ) );
    }

    protected function _parseActionName( $action )
    {
	$pieces = explode( '-', $action );

	$init = array_shift( $pieces );

	return $init . implode( '', array_map( 'ucfirst', $pieces ) );
    }

    /**
     * @access 	public
     * @return 	void
     */
    public function formAction()
    {
	$this->_helper->viewRenderer->setRender( 'form' );
	$this->_helper->layout()->disableLayout();

	$form = $this->_getForm( $this->_helper->url( 'save' ) );

	$this->view->form = $form;
    }

    /**
     * @access 	public
     * @return 	void
     */
    public function saveAction()
    {
	$this->_helper->viewRenderer->setRender( 'form' );

	$form = $this->_getForm( $this->_helper->url( 'save' ) );
	
	if ( $this->getRequest()->isPost() ) {

	    if ( $form->isValid( $this->getRequest()->getPost() ) ) {
	    	
		$this->_model->setData( $form->getValues() );

		$return = $this->_model->save();

		//Se a requisicao for em ajax
		if ( $this->getRequest()->isXmlHttpRequest() ) {
			
		    $message = $this->_model->getMessage()->toArray();

		    $result = array(
			'status' => (bool) $return,
			'id'	=> $return,
			'description' => $message,
			'data'	=> $form->getValues()
		    );

		    $this->_helper->json( $result );
		} else {

		    $this->_helper->FlashMessenger( $this->_model->getMessage() );

		    if ( $return )
			$this->_helper->redirector->goToSimple( 'index' );
		}
	    } else {
		
		$message = new App_Message();
		$message->addMessage( $this->_config->messages->warning, App_Message::WARNING );

		if ( $this->getRequest()->isXmlHttpRequest() ) {

		    $result = array(
			'status' => false,
			'description' => $message->toArray(),
			'errors' => $form->getMessages()
		    );

		    $this->_helper->json( $result );
		} else
		    $this->_helper->FlashMessenger( $message );
	    }
	}

	$this->view->form = $form;
    }
    
    /**
     * @access 	public
     * @return 	void
     */
    public function saveUploadAction()
    {
	$form = $this->_getForm( $this->_helper->url( 'save-upload' ) );
	
	if ( $this->getRequest()->isPost() ) {

	    if ( $form->isValid( $this->getRequest()->getPost() ) ) {

		$this->_model->setData( $form->getValues() );

		$return = $this->_model->save();

		$message = $this->_model->getMessage()->toArray();

		$result = array(
		    'status' => (bool) $return,
		    'id' => $return,
		    'description' => $message,
		    'data'	=> $form->getValues()
		);
		
	    } else {
		
		$message = new App_Message();
		$message->addMessage( $this->_config->messages->warning, App_Message::WARNING );
		
		$result = array(
		    'status' => false,
		    'description' => $message->toArray(),
		    'errors' => $form->getMessages()
		);
	    }
	}
	
	$this->_helper->viewRenderer->setRender( 'return-upload' );
	$this->_helper->layout->disableLayout();
	
	$this->view->retorno = json_encode( $result );
    }

    /**
     * @access 	public
     * @return 	void
     */
    public function editAction()
    {
	if ( $this->getRequest()->isXmlHttpRequest() )
	    $this->_helper->layout()->disableLayout();

	$this->_helper->viewRenderer->setRender( 'form' );

	$form = $this->_getForm( $this->_helper->url( 'save' ) );

	$id = $this->_getParam( 'id', 0 );

	$row = $this->_model->fetchRow( $id );

	if ( empty( $row ) )
	    $this->_helper->redirector->goToSimple( 'index' );

	$data = $row->toArray();

	$form->populate( $data );

	$this->view->data = $data;
	$this->view->form = $form;
    }

    /**
     * Exibe imagem
     */
    public function imageAction()
    {
	$this->_helper->viewRenderer->setNoRender();
	$this->_helper->layout->disableLayout();

	$id = $this->_getParam( 'id' );

	$image = $this->_model->getImage( $id );
	
	if ( !empty( $image->mimetype ) )
	    header( 'Content-type: ' . $image->mimetype );
	else
	    header( 'Content-type: image/jpeg' );

	echo empty( $image->imagem ) ? $image->arquivo : $image->imagem;
    }
    
    /**
     * @access public
     * @return void
     */
    public function statusAction()
    {
	$id = $this->getRequest()->getPost();

	$result = $this->_model->setStatus( $id );

	$this->_helper->json( $result );
    }

    /**
     * 
     */
    public function deleteAction()
    {
	$result = $this->_model->delete( $this->_getAllParams() );
	$this->_helper->json( $result );
    }
}