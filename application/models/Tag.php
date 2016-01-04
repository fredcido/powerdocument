<?php

/**
 *
 */
class Model_Tag extends App_Model_Abstract
{

    /**
     * 
     * @var Model_DbTable_Tag
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
	    $this->_dbTable = new Model_DbTable_Tag();

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

	    if ( empty( $this->_data['id'] ) )
		$this->_data['url'] = parent::urlAmigavel( 'url', $this->_data['titulo'] );

	    return parent::_simpleSave();
	} catch ( Exception $e ) {

	    $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );
	    return false;
	}
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    public function delete( $data )
    {
	$dbTag = $this->_getDbTable();
	
	$dbTag->getAdapter()->beginTransaction();
	try {
	    
	    if ( empty( $data['id'] ) )
		return array( 'status' => false, 'message' => $this->_config->messages->error );
	  
	    $mapperArquivo = new Model_Mapper_Arquivo();
	    $arquivos = $mapperArquivo->listArquivosByTag( $data['id'] );
	    
	    if ( $arquivos->count() > 0 )
		return array( 'status' => false, 'message' => $this->_config->messages->nodelete );
	    
	    $rowTag = $this->fetchRow( $data['id'] );
	    $rowTag->delete();
	    
	    $dbTag->getAdapter()->commit();
	    
	    return array( 'status' => true );
	    
	} catch ( Exception $e ) {
	    
	    $dbTag->getAdapter()->rollBack();
	    return array(
		'status'    => false,
		'message'   => $this->_config->messages->error
	    );
	}
    }

}