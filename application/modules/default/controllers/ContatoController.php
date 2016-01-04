<?php

/**
 * 
 */
class ContatoController extends App_Controller_Padrao
{

    /**
     * @var Model_Contato
     */
    protected $_model;

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	$this->_model = new Model_Contato();
	$this->view->noToolBar = true;
    }

    /**
     * @access 	protected
     * @param 	string 	$action
     * @return 	Default_Form_Tag
     */
    protected function _getForm( $action )
    {
	$this->_form = new Default_Form_Contato( array( 'action' => $action ) );

	return $this->_form;
    }
    
    /**
     * 
     */
    public function indexAction()
    {
	$form = $this->_getForm( $this->_helper->url( 'save' ) );
	$user = Zend_Auth::getInstance()->getIdentity();
	
	$data['email'] = $user->email;
	$data['nome'] = $user->nome;
	$form->populate( $data );

	$this->view->form = $form;
    }
}