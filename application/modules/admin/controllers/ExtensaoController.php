<?php

/**
 *
 */
class Admin_ExtensaoController extends App_Controller_Padrao
{

    /**
     * @var Model_Extensao
     */
    protected $_model;

    /**
     * @var Model_Mapper_Extensao
     */
    protected $_mapper;

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	$this->_model = new Model_Extensao();
	$this->_mapper = new Model_Mapper_Extensao();
    }

    /**
     * @access 	protected
     * @param 	string $action
     * @return 	Admin_Form_Extensao
     */
    protected function _getForm( $action )
    {
	$this->_form = new Admin_Form_Extensao( array( 'action' => $action ) );

	return $this->_form;
    }
}