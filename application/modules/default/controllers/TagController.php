<?php

/**
 * 
 */
class TagController extends App_Controller_Padrao
{

    /**
     * @var Model_Tag
     */
    protected $_model;

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	$this->_model = new Model_Tag();
    }

    /**
     * @access 	protected
     * @param 	string 	$action
     * @return 	Default_Form_Tag
     */
    protected function _getForm( $action )
    {
	$this->_form = new Default_Form_Tag( array( 'action' => $action ) );

	return $this->_form;
    }
}