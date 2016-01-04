<?php

/** 
 * 
 */
class Model_Mapper_Perfil extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return array
     */
    public function listOfPerfis()
    {
	$perfilDbTable = App_Model_DbTable_Factory::get('Perfil');
	$acaoDbTable = App_Model_DbTable_Factory::get('Acao');
	$perfilAcaoDbTable = App_Model_DbTable_Factory::get('PerfilAcao');
	
	$select = $perfilDbTable->select()
				->setIntegrityCheck( false )
				->from(
				    array( 'p' => $perfilDbTable ),
				    array(
					'id',
					'nome',
					'liberado',
					'dt_cadastro'
				    )
				)
				->joinLeft(
					array( 'pa' => $perfilAcaoDbTable ),
					'pa.perfil_id = p.id',
					array()
				)
				->joinLeft(
					array( 'a' => $acaoDbTable ),
					'a.id = pa.acao_id',
					array( 'acao' => 'descricao')
				)
				->order( 'p.nome' );
	
	$rows = $perfilDbTable->fetchAll( $select )->toArray();
	
	$data = array(
	    'perfil' => array(),
	    'acao'   => array()
	);
	
	foreach ( $rows as $row ) {
	    
	    // Perfil
	    if ( !in_array( $row['id'], array_keys( $data['perfil'] ) ) ) {

		    $data['perfil'][$row['id']] = array(
			    'id' 		=> $row['id'],
			    'nome' 		=> $row['nome'],
			    'liberado' 		=> $row['liberado'],
			    'dt_cadastro' 	=> $row['dt_cadastro']
		    );

	    }

	    // Acoes
	    if ( !empty( $row['acao'] ) ) {

		    $data['acao'][$row['id']][] = array(
			    'titulo' 	=> $row['acao']
		    ); 

	    }
	}
	
	return $data;
    }
    
    /**
     *
     * @param int $id
     * @return Zend_Db_Table_Rowset
     */
    public function listCategorias( $id )
    {
	$categoriaDb = App_Model_DbTable_Factory::get('Categoria');
	$perfilCategoriaDb = App_Model_DbTable_Factory::get('PerfilCategoria');
	
	$select = $categoriaDb->select()
				->setIntegrityCheck( false )
				->from( array( 'c' => $categoriaDb ) )
				->join( array( 'pc' => $perfilCategoriaDb ), 'c.id = pc.categoria_id', array() )
				->where( 'pc.perfil_id = ?', $id )
				->order( 'c.nome' );

	return $categoriaDb->fetchAll( $select );
    }
    
    /**
     *
     * @param int $id
     * @return Zend_Db_Table_Rowset
     */
    public function listAcoes( $id )
    {
	$acaoDb = App_Model_DbTable_Factory::get('Acao');
	$perfilAcaoDb = App_Model_DbTable_Factory::get('PerfilAcao');
	
	$select = $acaoDb->select()
				->setIntegrityCheck( false )
				->from( array( 'a' => $acaoDb ) )
				->join( array( 'pa' => $perfilAcaoDb ), 'a.id = pa.acao_id', array() )
				->where( 'pa.perfil_id = ?', $id )
				->order( 'a.descricao' );
	
	return $acaoDb->fetchAll( $select );
    }
    
    /**
     *
     * @param mixed $liberado
     * @return Zend_Db_Table_Rowset 
     */
    public function listPerfis( $liberado = false)
    {
	$perfilDb = App_Model_DbTable_Factory::get('Perfil');
	
	$select = $perfilDb->select();
	
	if ( false !== $liberado )
	    $select->where( 'liberado = ?', $liberado );
	
	$select->order( 'nome' );
	
	return $perfilDb->fetchAll( $select );
    }
    
    /**
     *
     * @param int $id
     * @return Zend_Db_Table_Rowset
     */
    public function listPerfisByCategorias( $id )
    {
	$perfilDb = App_Model_DbTable_Factory::get('Perfil');
	$perfilCategoriaDb = App_Model_DbTable_Factory::get('PerfilCategoria');
	
	$select = $perfilDb->select()
			   ->setIntegrityCheck( false )
			   ->from( array( 'p' => $perfilDb ) )
			   ->join(
				array( 'pc' => $perfilCategoriaDb ),
				'pc.perfil_id = p.id',
				array()
			   )
			    ->where( 'pc.categoria_id = ?' , $id );
	
	$select->order( 'p.nome' );
	
	return $perfilDb->fetchAll( $select );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function listPerfisWithUsers()
    {
	$dbPerfil = App_Model_DbTable_Factory::get('Perfil');
	$dbUsuario = App_Model_DbTable_Factory::get('Usuario');
	
	$select = $dbPerfil->select()
			    ->setIntegrityCheck( false )
			    ->distinct()
			    ->from( array( 'p' => $dbPerfil ) )
			    ->join(
				array( 'u' => $dbUsuario ),
				'u.perfil_id = p.id',
				array()
			    )
			    ->where( 'p.liberado = ?', 1 )
			    ->where( 'u.liberado = ?', 1 )
			    ->order( 'p.nome' );
	
	return $dbPerfil->fetchAll( $select );
    }
}