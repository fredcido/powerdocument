<?php

/**
 * 
 */
class Admin_IpController extends App_Controller_Padrao
{

    /**
     * 
     * @var Model_Ip
     */
    protected $_model;

    /**
     * 
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	$this->_model = new Model_Ip();
    }

    /**
     * 
     * @access 	protected
     * @param 	string 		$action
     * @return 	void
     */
    protected function _getForm( $action )
    {
	$this->_form = new Admin_Form_Ip( array( 'action' => $action ) );

	return $this->_form;
    }

    /**
     * 
     * @access public
     * @return void
     */
    public function formAction()
    {
	$this->_helper->viewRenderer->setRender( 'form' );
	$this->_helper->layout()->disableLayout();

	$form = $this->_getForm( $this->_helper->url( 'save' ) );

	$this->view->form = $form;
    }

    /**
     * 
     * @access public
     * @return void
     */
    public function deleteAction()
    {
	$this->_model->setData( $this->_getAllParams() );

	$result = $this->_model->delete();

	$this->_helper->json( $result );
    }
}