<?php

/**
 * 
 */
class Admin_PerfilController extends App_Controller_Padrao
{

    /**
     * @var Model_Perfil
     */
    protected $_model;
    
    /**
     *
     * @var Model_Mapper_Perfil
     */
    protected $_mapper;

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	$this->_model = new Model_Perfil();
	
	$this->_mapper = new Model_Mapper_Perfil();
    }

    /**
     * @access 	protected
     * @param 	string 		$action
     * @return 	void
     */
    protected function _getForm( $action )
    {
	$this->_form = new Admin_Form_Perfil( array( 'action' => $action ) );

	return $this->_form;
    }
    
    /**
     * 
     */
    public function indexAction()
    {
	$this->view->data = $this->_mapper->listOfPerfis();
    }

    /**
     * 
     */
    public function categoriasAction()
    {
	$this->_helper->layout()->disableLayout();
	
	// Busca categorias Cadastradas
	$modelCategorias = new Model_Categoria();
	$this->view->categorias = $modelCategorias->fetchAll();
	
	// Instancia form de Categoria
	$formCategoria = new Admin_Form_Categoria();
	$formCategoria->setAction( $this->_helper->url('save', 'categoria') );
	
	$this->view->form = $formCategoria;
    }
    
    /**
     * 
     */
    public function editPostHook()
    {
	$this->view->form
	      ->getElement('acoes')
	      ->setValue( $this->_model->listAcoesArray( $this->_getParam( 'id' ) ) );
	
	
	//$this->view->categorias = $this->_mapper->listCategorias( $this->_getParam( 'id' ) );
	$this->view->categorias = $this->_model->listCategoriasArray( $this->_getParam( 'id' ) );
    }
}