<?php

/**
 *
 */
class Model_Extensao extends App_Model_Abstract
{

    /**
     * 
     * @var Model_DbTable_Tag
     */
    protected $_dbTable;

    /**
     * 
     * @access 	protected
     * @return 	Model_DbTable_Usuario
     */
    protected function _getDbTable()
    {
	if ( is_null( $this->_dbTable ) )
	    $this->_dbTable = new Model_DbTable_Extensao();

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
     * @return array
     */
    public function listArray()
    {
	$extensaoDb = $this->_getDbTable();
	
	$select = $extensaoDb->select()
			    ->where( 'liberado = ?', 1);
	
	$rows = $extensaoDb->fetchAll( $select );
	
	$data = array();
	foreach ( $rows as $row )
	    $data[] = $row->descricao;
	
	return $data;
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    public function delete( $data )
    {
	$dbExtensao = $this->_getDbTable();
	
	$dbExtensao->getAdapter()->beginTransaction();
	try {
	    
	    if ( empty( $data['id'] ) )
		return array( 'status' => false, 'message' => $this->_config->messages->error );
	  
	    $mapperArquivo = new Model_Mapper_Arquivo();
	    $arquivos = $mapperArquivo->listArquivosByExtensao( $data['id'] );
	    
	    if ( $arquivos->count() > 0 )
		return array( 'status' => false, 'message' => $this->_config->messages->nodelete );
	    
	    $rowExtensao = $this->fetchRow( $data['id'] );
	    $rowExtensao->delete();
	    
	    $dbExtensao->getAdapter()->commit();
	    
	    return array( 'status' => true );
	    
	} catch ( Exception $e ) {
	    
	    $dbExtensao->getAdapter()->rollBack();
	    return array(
		'status'    => false,
		'message'   => $this->_config->messages->error
	    );
	}
    }

}