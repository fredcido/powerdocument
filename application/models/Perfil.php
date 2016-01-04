<?php

/**
 *
 */
class Model_Perfil extends App_Model_Abstract
{

    /**
     * 
     * @var Model_DbTable_Perfil
     */
    protected $_dbTable;
    
    /**
     *
     * @var Model_Mapper_Perfil
     */
    protected $_mapper;
    

    /**
     * 
     */
    public function __construct()
    {
	parent::__construct();
	
	$this->_mapper = new Model_Mapper_Perfil();
    }
    
    /**
     * 
     * @access 	protected
     * @return 	Model_DbTable_Perfil
     */
    protected function _getDbTable()
    {
	if ( is_null( $this->_dbTable ) )
	    $this->_dbTable = new Model_DbTable_Perfil();

	return $this->_dbTable;
    }

    /**
     * 
     * @access 	public
     * @return 	boolean
     */
    public function save()
    {
	$perfilDb = $this->_getDbTable();
	
	$adapter = $perfilDb->getAdapter();
	
	$adapter->beginTransaction();
	try {
	    
	    $dataForm = $this->_data;
	    
	    if ( empty( $this->_data['limite_id'] ) )
		unset( $this->_data['limite_id'] );
	    	    
	    $perfil_id = parent::_simpleSave();
	    
	    // Trata acoes vinculadas ao perfil
	    $this->_saveAcoes( $perfil_id, $dataForm );
	    
	    // Trata categorias vinculadas ao perfil
	    $this->_saveCategorias( $perfil_id, $dataForm );
	    
	    $adapter->commit();
	    	    
	    return $perfil_id;
	    
	} catch ( Exception $e ) {
	    
	    $adapter->rollBack();
	    	    
	    $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );
	    return false;
	}
    }
    
    /**
     *
     * @param int $perfilId 
     */
    protected function _saveAcoes( $perfil_id, $dataForm )
    {
	// Busca acoes vinculadas ao perfil
	$perfilAcao = $this->listAcoesArray( $perfil_id );
	// Pega acoes vinculadas na tela
	$acaoTela = empty( $dataForm['acoes'] ) ? array() : $dataForm['acoes'];

	// Acoes para inserir
	$novasAcoes = array_diff( $acaoTela, $perfilAcao );
	// Acoes para remover
	$velhasAcoes = array_diff( $perfilAcao, $acaoTela );

	$perfilAcaoDb = App_Model_DbTable_Factory::get('PerfilAcao');

	// Se existir novas acoes para serem inseridas
	if ( !empty( $novasAcoes ) ) {

	    // Insere novas acoes
	    foreach ( $novasAcoes as $acao ) {

		$perfilAcaoRow = $perfilAcaoDb->createRow();
		$perfilAcaoRow->perfil_id = $perfil_id;
		$perfilAcaoRow->acao_id = $acao;
		$perfilAcaoRow->save();
	    }
	}

	// Se existirem acoes para serem removidas
	if ( !empty( $velhasAcoes ) ) {

	    $where = array();
	    $where[] = $perfilAcaoDb->getAdapter()->quoteInto( 'acao_id IN (?)', $velhasAcoes);
	    $where[] = $perfilAcaoDb->getAdapter()->quoteInto( 'perfil_id = ?', $perfil_id );

	    $perfilAcaoDb->delete( $where );
	}
    }
    
    /**
     *
     * @param int $perfilId 
     */
    protected function _saveCategorias( $perfil_id, $dataForm )
    {
	// Busca categorias vinculadas ao perfil
	$perfilCategorias = $this->listCategoriasArray( $perfil_id );
	// Pega categorias vinculadas na tela
	$categoriaTela = empty( $dataForm['categorias'] ) ? array() : $dataForm['categorias'];

	// Categorias para inserir
	$novasCategorias = array_diff( $categoriaTela, $perfilCategorias );
	// Categorias para remover
	$velhasCategorias = array_diff( $perfilCategorias, $categoriaTela );

	$perfilCategoriaDb = App_Model_DbTable_Factory::get('PerfilCategoria');

	// Se existir novas categorias para serem inseridas
	if ( !empty( $novasCategorias ) ) {

	    // Insere novas categorias
	    foreach ( $novasCategorias as $categoria ) {

		$perfilCatgoriaRow = $perfilCategoriaDb->createRow();
		$perfilCatgoriaRow->perfil_id = $perfil_id;
		$perfilCatgoriaRow->categoria_id = $categoria;
		$perfilCatgoriaRow->save();
	    }
	}

	// Se existirem categorias para serem removidas
	if ( !empty( $velhasCategorias ) ) {

	    $where = array();
	    $where[] = $perfilCategoriaDb->getAdapter()->quoteInto( 'categoria_id IN (?)', $velhasCategorias);
	    $where[] = $perfilCategoriaDb->getAdapter()->quoteInto( 'perfil_id = ?', $perfil_id );

	    $perfilCategoriaDb->delete( $where );
	}
    }
    
    
    /**
     *
     * @param int $id
     * @return array
     */
    public function listAcoesArray( $id )
    {
	$rows = $this->_mapper->listAcoes( $id );
	
	$data = array();
	foreach ( $rows as $row )
	    $data[] = $row->id;
	
	return $data;
    }
    
    /**
     *
     * @param int $id
     * @return array
     */
    public function listCategoriasArray( $id )
    {
	$rows = $this->_mapper->listCategorias( $id );
	
	$data = array();
	foreach ( $rows as $row )
	    $data[] = $row->id;
	
	return $data;
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    public function delete( $data )
    {
	$dbPerfil = $this->_getDbTable();
	
	$dbPerfil->getAdapter()->beginTransaction();
	try {
	    
	    if ( empty( $data['id'] ) )
		return array( 'status' => false, 'message' => $this->_config->messages->error );
	  
            // Validando usuarios vinculados a perfil
	    $mapperUsuario = new Model_Mapper_Usuario();
	    $usuarios = $mapperUsuario->listUsuariosByPerfil( $data['id'] );
	    
	    if ( $usuarios->count() > 0 )
		return array( 'status' => false, 'message' => $this->_config->messages->nodelete );
            
            // validando acoes vinculadas a perfil
            $acoes = $this->listAcoesArray( $data['id'] );
            
            if ( !empty( $acoes ) )
		return array( 'status' => false, 'message' => $this->_config->messages->nodelete );
            
            // Validando categorias vinculadas a perfil
            $categorias = $this->listCategoriasArray( $data['id'] );
            
            if ( !empty( $categorias ) )
                return array( 'status' => false, 'message' => $this->_config->messages->nodelete );
                
	    
	    $rowPerfil = $this->fetchRow( $data['id'] );
	    $rowPerfil->delete();
	    
	    $dbPerfil->getAdapter()->commit();
	    
	    return array( 'status' => true );
	    
	} catch ( Exception $e ) {
	    
	    $dbPerfil->getAdapter()->rollBack();
	    return array(
		'status'    => false,
		'message'   => $this->_config->messages->error
	    );
	}
    }
}