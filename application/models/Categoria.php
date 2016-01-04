<?php

/**
 *
 */
class Model_Categoria extends App_Model_Abstract
{

    /**
     * 
     * @var Model_DbTable_Categoria
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
	    $this->_dbTable = new Model_DbTable_Categoria();

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
     * @return ArrayObject
     */
    public function listTreeCategorias()
    {
	$dbCategoria = $this->_getDbTable();
        
        $select = $dbCategoria->select()->order( 'nome' );
        $rows = $dbCategoria->fetchAll( $select )->toArray();
        
        $dataFinal = new ArrayObject( array() );
        
        $categorias = array();
        foreach ( $rows as $row )
            $categorias[$row['id']] = $row;
                
        foreach ( $categorias as $row )
            $this->_addItemCategoria( $row, $dataFinal, $categorias );
        
        return $dataFinal;
    }
    
    /**
     *
     * @param array $row
     * @param ArrayObject $collection
     * @param array $source 
     */
    protected function _addItemCategoria( $row, $collection, $source )
    {
        if ( empty( $row['categoria_id'] ) ) {
            
            if ( !array_key_exists( $row['id'], $collection ) ) {
                
                $collection[$row['id']] = $row;
                
                if ( !array_key_exists( 'children', $collection[$row['id']] ) )
                    $collection[$row['id']]['children'] = new ArrayObject( array() );
            }
        } else if ( !$this->_searchParent( $row, $collection ) ) {
            
            $parent = $source[$row['categoria_id']];
            $parent['children'] = new ArrayObject( array() );
            $parent['children'][$row['id']] = new ArrayObject( $row );
                        
            $this->_addItemCategoria( $parent, $collection, $source );
        }
    }
    
    /**
     *
     * @param array $row
     * @param ArrayObject $collection
     * @return bool
     */
    protected function _searchParent( $row, $collection )
    {
        foreach ( $collection as $parent ) {
            if ( $parent['id'] == $row['categoria_id'] ) {
                if ( !array_key_exists( 'children', $parent ) )
                    $parent['children'] = new ArrayObject( array() );
                
                if ( !array_key_exists( $row['id'], $parent['children'] ) )
                        $parent['children'][$row['id']] = new ArrayObject( $row );
                
                return true;
                
            } else if ( array_key_exists( 'children', $parent ) && $this->_searchParent( $row, $parent['children'] ) )
                return true;
        }
        
        return false;
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    public function delete( $data )
    {
	$dbCategoria = $this->_getDbTable();
	
	$dbCategoria->getAdapter()->beginTransaction();
	try {
	    
	    if ( empty( $data['id'] ) )
		return array( 'status' => false, 'message' => $this->_config->messages->error );
	    
	    $mapperCategoria = new Model_Mapper_Categoria();
	    $categorias = $mapperCategoria->listCategoriasByCategorias( $data['id'] );
	    
	    if ( $categorias->count() > 0 )
		return array( 'status' => false, 'message' => $this->_config->messages->nodelete );
	    
	    $mapperPerfil = new Model_Mapper_Perfil();
	    $perfis = $mapperPerfil->listPerfisByCategorias( $data['id'] );
	    
	    if ( $perfis->count() > 0 )
		return array( 'status' => false, 'message' => $this->_config->messages->nodelete );
	    
	    $mapperArquivo = new Model_Mapper_Arquivo();
	    $arquivos = $mapperArquivo->listArquivosByCategoria( $data['id'] );
	    
	    if ( $arquivos->count() > 0 )
		return array( 'status' => false, 'message' => $this->_config->messages->nodelete );
	    
	    $rowCategoria = $this->fetchRow( $data['id'] );
	    $rowCategoria->delete();
	    
	    $dbCategoria->getAdapter()->commit();
	    
	    return array( 'status' => true );
	    
	} catch ( Exception $e ) {
	    
	    $dbCategoria->getAdapter()->rollBack();
	    return array(
		'status'    => false,
		'message'   => $this->_config->messages->error
	    );
	}
    }
    
    /**
     * 
     * @param array $data
     * @return array
     */
    public function reorder( $data )
    {
	$dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
	$dbAdapter->beginTransaction();
	try {
	    
	    $dbCategoria = App_Model_DbTable_Factory::get( 'Categoria' );
	    $categoria = $dbCategoria->fetchRow( array( 'id = ?' => $data['id'] ) );
	    $categoria->categoria_id = $data['parent'] == '#' ? null : $data['parent'];
	    $categoria->save();
	    
	    $dbAdapter->commit();
	    return array( 'status' => true );
	    
	} catch ( Exception $e ) {
	    
	    $dbAdapter->rollBack();
	    return array( 'status' => false );
	}
    }
}