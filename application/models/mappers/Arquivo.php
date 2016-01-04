<?php

/** 
 * 
 */
class Model_Mapper_Arquivo extends App_Model_Mapper_Abstract
{
	/**
	 * 
	 * @access 	public
	 * @param 	array $filter
	 * @return 	Zend_Db_Table_Rowset
	 */
	public function files ( array $filter = null )
	{
		$dbArquivo = App_Model_DbTable_Factory::get( 'arquivo' );
		
		$bind = array();
		
		$select = $dbArquivo->select()
			->setIntegrityCheck(false)
			->from(
				array('a' => $dbArquivo),
				array('id', 'nome', 'tamanho', 'liberado', 'dt_cadastro')
			)
			->join(
				array('e' => App_Model_DbTable_Factory::get('extensao')),
				'e.id = a.extensao_id',
				array('extensao' => 'descricao')
			)
			->join(
				array('u' => App_Model_DbTable_Factory::get('usuario')),
				'u.id = a.usuario_id',
				array('usuario' => 'nome')
			)
			->join(
				array('c' => App_Model_DbTable_Factory::get('categoria_arquivo')),
				'c.arquivo_id = a.id',
				array()
			)
			->join(
				array('p' => App_Model_DbTable_Factory::get('perfil')),
				'p.id = u.perfil_id',
				array('perfil' => 'p.nome')
			)
			->join(
				array('pc' => App_Model_DbTable_Factory::get('PerfilCategoria')),
				'c.categoria_id = pc.categoria_id',
				array()
			)
			->where( 'pc.perfil_id = ?', Zend_Auth::getInstance()->getIdentity()->perfil_id );
			
		//Join
		/*
		if ( !empty($filter['data']['categoria']) ) {
			$select->joinLeft(
				array('c' => App_Model_DbTable_Factory::get('categoria_arquivo')),
				'c.arquivo_id = a.id',
				array()
			);
		}
		 */
		
		if ( !empty($filter['data']['tag']) ) {
			$select->joinLeft(
				array('t' => App_Model_DbTable_Factory::get('tag_arquivo')),
				't.arquivo_id = a.id',
				array()
			);
		}
		
		//Where
		if ( !empty($filter['data']['extensao']) )
			$select->where('a.extensao_id IN(?)', $filter['data']['extensao']);
			
		if ( !empty($filter['data']['categoria']) ) 
			$select->where('c.categoria_id IN(?)', $filter['data']['categoria']);

		if ( !empty($filter['data']['tag']) )
			$select->where('t.tag_id IN(?)', $filter['data']['tag']);

		$select->where('a.liberado = :liberado');
		$bind[':liberado'] = empty($filter) ? 1 : $filter['data']['status'];
		
		if ( !empty($filter['data']['form']['txt-usuario']) ) {
			$select->where('u.nome LIKE :usuario');
			$bind[':usuario'] = '%'.$filter['data']['form']['txt-usuario'].'%';
		}
		
		if ( !empty($filter['data']['form']['txt-arquivo']) ) {
			$select->where("a.nome LIKE :arquivo");
			$bind[':arquivo'] = '%'.$filter['data']['form']['txt-arquivo'].'%';
		}
		
		$select->group( 'a.id' );
		$select->order( 'a.dt_cadastro DESC' )->bind( $bind );
	
		
		$rows = $dbArquivo->fetchAll( $select );
		
		//foreach ( $rows as $key => $row )
		//	$rows[$key]->dt_cadastro = new Zend_Date($row->dt_cadastro);
		
		return $rows;
	}
    
	/**
	 *
	 * @param int $id
	 * @return Zend_Db_Table_Rowset
	 */
	public function listCategorias( $id )
	{
	    $categoriaDb = App_Model_DbTable_Factory::get('Categoria');
	    $dbArquivoCategoria = App_Model_DbTable_Factory::get('CategoriaArquivo');

	    $select = $categoriaDb->select()
				    ->setIntegrityCheck( false )
				    ->from( array( 'c' => $categoriaDb ) )
				    ->join( array( 'ac' => $dbArquivoCategoria ), 'c.id = ac.categoria_id', array() )
				    ->where( 'ac.arquivo_id = ?', $id )
				    ->order( 'c.nome' );

	    return $categoriaDb->fetchAll( $select );
	}
	
	/**
	 *
	 * @param int $id
	 * @return Zend_Db_Table_Rowset
	 */
	public function listTags( $id )
	{
	    $dbTag = App_Model_DbTable_Factory::get('Tag');
	    $dbTagArquivo = App_Model_DbTable_Factory::get('TagArquivo');

	    $select = $dbTag->select()
			    ->setIntegrityCheck( false )
			    ->from( array( 't' => $dbTag ) )
			    ->join( array( 'ta' => $dbTagArquivo ), 't.id = ta.tag_id', array() )
			    ->where( 'ta.arquivo_id = ?', $id )
			    ->order( 't.titulo' );

	    return $dbTag->fetchAll( $select );
	}
	
	/**
	 *
	 * @param string $hash
	 * @return App_Model_DbTable_Row_Abstract
	 */
	public function getArquivoView( $hash )
	{
	    $dbArquivo = App_Model_DbTable_Factory::get('Arquivo');
	    $dbView = App_Model_DbTable_Factory::get('View');
	    $dbExtensao = App_Model_DbTable_Factory::get('Extensao');
	    
	    $select = $dbArquivo->select()
				->setIntegrityCheck( false )		
				->from( array( 'a' => $dbArquivo ) )
				->join( array( 'v' => $dbView ), 'v.arquivo_id = a.id', array( 'view' => 'id' ) )
				->join( array( 'e' => $dbExtensao ), 'e.id = a.extensao_id', array( 'extensao' => 'descricao' ) )
				->where( 'v.hash = ?', $hash )
				->where( 'v.liberado = ?', 0 )
				->where( 'v.dt_cadastro >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)' );
	    
	    return $dbArquivo->fetchRow( $select );
	}   
	
	/**
	 *
	 * @param int $id
	 * @return Zend_Db_Table_Rowset
	 */
	public function listArquivosByCategoria( $id )
	{
	    $dbArquivo = App_Model_DbTable_Factory::get('Arquivo');
	    $dbArquivoCategoria = App_Model_DbTable_Factory::get('CategoriaArquivo');
	    
	    $select = $dbArquivo->select()
				->setIntegrityCheck( false )
				->from( array( 'a' => $dbArquivo ) )
				->join( array( 'ca' => $dbArquivoCategoria ), 'a.id = ca.arquivo_id', array() )
				->where( 'ca.categoria_id = ?', $id );
	    
	    return $dbArquivo->fetchAll( $select );
	}
	
	/**
	 *
	 * @param int $id
	 * @return Zend_Db_Table_Rowset
	 */
	public function listArquivosByTag( $id )
	{
	    $dbArquivo = App_Model_DbTable_Factory::get('Arquivo');
	    $dbTagArquivo = App_Model_DbTable_Factory::get('TagArquivo');
	    
	    $select = $dbArquivo->select()
				->setIntegrityCheck( false )
				->from( array( 'a' => $dbArquivo ) )
				->join( array( 'ta' => $dbTagArquivo ), 'a.id = ta.arquivo_id', array() )
				->where( 'ta.tag_id = ?', $id );
	    
	    return $dbArquivo->fetchAll( $select );
	}
	
	/**
	 *
	 * @param int $id
	 * @return Zend_Db_Table_Rowset 
	 */
	public function listArquivosByExtensao( $id )
	{
	    $dbArquivo = App_Model_DbTable_Factory::get('Arquivo');
	    
	    $select = $dbArquivo->select()
				->where( 'extensao_id = ?', $id );
	    
	    return $dbArquivo->fetchAll( $select );
	}
	
	/**
	 *
	 * @param int $id
	 * @return Zend_Db_Table_Rowset 
	 */
	public function listArquivosByUsuario( $id )
	{
	    $dbArquivo = App_Model_DbTable_Factory::get('Arquivo');
	    
	    $select = $dbArquivo->select()
				->where( 'usuario_id = ?', $id );
	    
	    return $dbArquivo->fetchAll( $select );
	}
	
	/**
	 *
	 * @param string $md5
	 * @return App_Model_DbTable_Row_Abstract
	 */
	public function searchFileMd5( $md5 )
	{
	    $dbArquivo = App_Model_DbTable_Factory::get('Arquivo');
	    $dbCategoriaArquivo = App_Model_DbTable_Factory::get('CategoriaArquivo');
	    
	    $select = $dbArquivo->select()
				->distinct()
				->setIntegrityCheck( false )
				->from( array( 'a' => $dbArquivo ) )
				->join(
				    array( 'ca' => $dbCategoriaArquivo ),
				    'ca.arquivo_id = a.id',
				    array()
				)
				->where( 'MD5(id) = ?', $md5 )	
				->where( 'categoria_id IN (?)', App_Util_Session::get()->categorias );
	    
	    return $dbArquivo->fetchRow( $select );
	}
}