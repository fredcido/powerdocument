<?php

/**
 * 
 */
class Admin_LimiteController extends App_Controller_Padrao
{
    /**
     * 
     * @var Model_Limite
     */
    protected $_model;

    /**
     * 
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
		$this->_model = new Model_Limite();
    }

    /**
     * 
     * @access 	protected
     * @param 	string 		$action
     * @return 	Admin_Form_Limite
     */
    protected function _getForm( $action )
    {
		$this->_form = new Admin_Form_Limite( array( 'action' => $action ) );

		return $this->_form;
    }

    /**
     * @access 	public
     * @return 	void
     */
    public function indexAction()
    {
		$form = $this->_getForm( $this->_helper->url( 'save' ) );

		$this->view->form = $form;
    }
    
    /**
     * 
     * @access public
     * @return void
     */
    public function selectedAction ()
    {
    	if ( $this->getRequest()->isXmlHttpRequest() ) 
			$this->_helper->layout()->disableLayout();
			
    	$id = $this->_getParam('id');
    	
    	if ( !empty($id) ) {
    		$row = $this->_model->fetchRow( $id );
    	
    		$this->view->row = $row;
    	}
    }
    
    /**
     * 
     * @access public
     * @return void
     */
    public function listAction ()
    {
    	if ( $this->getRequest()->isXmlHttpRequest() ) 
			$this->_helper->layout()->disableLayout();
    	
    	$rows = $this->_model->fetchAll();
    	
    	$this->view->rows = $rows;
    }
    
	/**
	 * 
	 * @access public
	 * @return void
	 */
    public function dialogAction ()
    {
    	$this->_helper->layout()->disableLayout();
    }
}
