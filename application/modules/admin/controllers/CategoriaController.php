<?php

/**
 *
 */
class Admin_CategoriaController extends App_Controller_Padrao
{

    /**
     * @var Model_Categoria
     */
    protected $_model;

    /**
     * @var Model_Mapper_Categoria
     */
    protected $_mapper;

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	$this->_model = new Model_Categoria();
	$this->_mapper = new Model_Mapper_Categoria();
	
	$this->view->toolbars = array(
	    array(
		'id'	=> 'novo',
		'label' => 'Novo',
		'image' => 'public/images/icons/fugue/document.png',
	    ),
	    array(
		'id'	=> 'btn-tree',
		'label' => 'Organizar',
		'image' => 'public/images/icons/fugue/globe-network.png',
	    )
	);
    }

    /**
     * @access 	protected
     * @param 	string $action
     * @return 	Admin_Form_Categoria
     */
    protected function _getForm( $action )
    {
	$this->_form = new Admin_Form_Categoria( array( 'action' => $action ) );

	return $this->_form;
    }
    
    /**
     * 
     */
    public function treeAction()
    {
	$this->_helper->layout()->disableLayout();
    }
    
    /**
     * 
     */
    public function reorderAction()
    {
	$result = $this->_model->reorder( $this->_getAllParams() );
	$this->_helper->json( $result );
    }
	    
}