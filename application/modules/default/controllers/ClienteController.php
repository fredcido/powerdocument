<?php

/**
 *
 */
class ClienteController extends App_Controller_Padrao
{

    /**
     * @var Model_Cliente
     */
    protected $_model;

    /**
     * @var Model_Mapper_Cliente
     */
    protected $_mapper;

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	$this->_model = new Model_Cliente();
	$this->_mapper = new Model_Mapper_Cliente();
    }

    /**
     * @access 	protected
     * @param 	string $action
     * @return 	Admin_Form_Categoria
     */
    protected function _getForm( $action )
    {
	$this->_form = new Default_Form_Cliente( array( 'action' => $action ) );

	return $this->_form;
    }
 
    /**
     * 
     */
    public function loadCidadeAction()
    {
	$uf = $this->_getParam( 'id' );
	$data = array();
	if ( !empty( $uf ) ) {
	    
	    $dbCidade = App_Model_DbTable_Factory::get( 'Cidade' );
	    $rows = $dbCidade->fetchAll( array( 'estado_id = ?' => $uf ), array( 'nome' ) );
	    
	    foreach ( $rows as $row )
		$data[] = array( 'id' => $row->id, 'name' => $row->nome );
	}
	
	$this->_helper->json( $data );
    }
    
    /**
     * 
     */
    public function editPostHook()
    {
	$uf = $this->view->data['estado_id'];
	
	$dbCidade = App_Model_DbTable_Factory::get( 'Cidade' );
	$rows = $dbCidade->fetchAll( array( 'estado_id = ?' => $uf ), array( 'nome' ) );
	
	$opt = array();
	foreach ( $rows as $row )
	    $opt[$row->id] = $row->nome;
	
	$this->view->form->getElement( 'cidade_id' )->addMultiOptions( $opt );
    }
}