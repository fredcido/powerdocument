<?php

/**
 * 
 */
class Admin_UsuarioController extends App_Controller_Padrao
{

    /**
     * 
     * @var unknown_type
     */
    protected $_model;

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	$this->_model = new Model_Usuario();
    }

    /**
     * @access 	protected
     * @param 	string $action
     * @return 	Default_Form_Usuario
     */
    protected function _getForm( $action )
    {
	$this->_form = new Admin_Form_Usuario( array( 'action' => $action ) );

	return $this->_form;
    }
    
    /**
     * @access public
     * @return void
     */
    public function editPostHook()
    {
	$this->view->form->getElement('email')->setAttrib( 'readOnly', true );
	$this->view->form->getElement('senha')->setRequired( false );
	$this->view->form->getElement('confirma_senha')->setRequired( false );
    }
}