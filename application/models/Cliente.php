<?php

/**
 *
 */
class Model_Cliente extends App_Model_Abstract
{

    /**
     * 
     * @var Model_DbTable_Cliente
     */
    protected $_dbTable;

    /**
     * 
     * @access 	protected
     * @return 	Model_DbTable_Tag
     */
    protected function _getDbTable()
    {
	if ( is_null( $this->_dbTable ) )
	    $this->_dbTable = new Model_DbTable_Cliente();

	return $this->_dbTable;
    }

    /**
     * 
     * @access 	public
     * @return 	boolean
     */
    public function save()
    {
	try {

	    return parent::_simpleSave();
	} catch ( Exception $e ) {
	    $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );
	    return false;
	}
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function fetchAll()
    {
	$dbCliente = App_Model_DbTable_Factory::get( 'Cliente' );
	$dbCidade = App_Model_DbTable_Factory::get( 'Cidade' );
	$dbEstado = App_Model_DbTable_Factory::get( 'Estado' );
	$dbEstadoCivil = App_Model_DbTable_Factory::get( 'EstadoCivil' );
	
	$select = $dbCliente->select()
			    ->from( array( 'c' => $dbCliente ) )
			    ->setIntegrityCheck( false )
			    ->join(
				array( 'ci' => $dbCidade ),
				'ci.id = c.cidade_id',
				array( 'cidade' => 'nome' )
			    )
			    ->join(
				array( 'uf' => $dbEstado ),
				'uf.id = ci.estado_id',
				array( 'estado' => 'nome', 'sigla' )
			    )
			    ->joinLeft(
				array( 'ec' => $dbEstadoCivil ),
				'ec.id = c.estado_civil_id',
				array( 'estado_civil' => 'nome' )
			    )
			    ->order( array( 'nome' ) );
	
	return $dbCliente->fetchAll( $select );
    }

    /**
     * 
     * @access 	public
     * @param 	array 	$param
     * @return 	array
     */
    public function setStatus( array $param )
    {
	try {

	    $data = array('status' => $param['action']);

	    $where = $this->_dbTable->getAdapter()->quoteInto( 'id IN(?)', $param['data'] );

	    $this->_dbTable->update( $data, $where );

	    return array('result' => true);
	} catch ( Exception $e ) {

	    return array('result' => false);
	}
    }
}