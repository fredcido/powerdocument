<?php

/**
 * 
 */
class Admin_ConfiguracaoController extends App_Controller_Padrao
{
	/**
	 * 
	 * @var Model_Configuracao
	 */
	protected $_model;
	
	/**
	 * 
	 * (non-PHPdoc)
	 * @see Zend_Controller_Action::init()
	 */
	public function init ()
	{
		$this->view->noToolBar = true;
		
		$this->_model = new Model_Configuracao();
	}
	
	/**
	 * 
	 * @access 	protected
	 * @param 	string 		$action
	 * @return 	Admin_Form_Configuracao
	 */
	protected function _getForm ( $action )
	{
		$this->_form = new Admin_Form_Configuracao( array( 'action' => $action ) );
		
		return $this->_form;
	}
	
	/**
	 * 
	 * @access public
	 * @return void
	 */
	public function indexAction ()
	{
		$form = $this->_getForm( $this->_helper->url( 'save' ) );
		
		$row = $this->_model->fetchRow();
		
		if ( !empty($row) ) 
			$form->populate( $row->toArray() );
		
		$this->view->form = $form;
	}
	
	/**
	 * 
	 * @access public
	 * @return void
	 */
	public function ipAction ()
	{
		if ( $this->getRequest()->isXmlHttpRequest() ) 
			$this->_helper->layout()->disableLayout();
		
		$model 	= new Model_Ip();
		$rows = $model->fetchAll();
		
		$this->view->rows = $rows;
	}
}