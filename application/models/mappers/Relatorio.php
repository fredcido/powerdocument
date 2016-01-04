<?php

/** 
 * 
 */
class Model_Mapper_Relatorio extends App_Model_Mapper_Abstract
{
    
    /**
     *
     * @param array $filtro
     * @return Zend_Db_Table_Rowset_Abstract 
     */
    public function relatorioAcesso( $filtro = array() )
    {
	$acessoDb = App_Model_DbTable_Factory::get( 'Acesso' );
	$usuarioDb = App_Model_DbTable_Factory::get('Usuario');
	
	$select = $acessoDb->select()
			    ->setIntegrityCheck( false )
			    ->from( 
				array( 'a' => $acessoDb ),
				array( 
				    'email',
				    'resultado',
				    'data'
				)
			    )
			    ->joinLeft( array( 'u' => $usuarioDb ), 'u.email = a.email', array( 'usuario' => 'id' ) );
	
	
	if ( !empty( $filtro['usuario'] ) )
	    $select->where( 'u.id = ?', $filtro['usuario'] );
	
	if ( !empty( $filtro['resultado'] ) )
	    $select->where( 'a.resultado = ?', $filtro['resultado'] );
	
	if ( !empty( $filtro['data_inicial'] ) && Zend_Date::isDate( $filtro['data_inicial'] ) )
	    $select->where( 'DATE(a.data) >= ?', new Zend_Db_Expr( "STR_TO_DATE('" . $filtro['data_inicial'] . "', '%d/%m/%Y')" ) );
	
	if ( !empty( $filtro['data_final'] ) && Zend_Date::isDate( $filtro['data_final'] )  )
	    $select->where( 'DATE(a.data) <= ?', new Zend_Db_Expr( "STR_TO_DATE('" . $filtro['data_final'] . "', '%d/%m/%Y')" ) );
	
	
	$select->order( 'a.data DESC' );
	
	return $acessoDb->fetchAll( $select );
    }
    
    
    /**
     *
     * @param array $filtro
     * @return Zend_Db_Table_Rowset_Abstract 
     */
    public function relatorioArquivo( $filtro = array() )
    {
	$historicoDb = App_Model_DbTable_Factory::get( 'Historico' );
	$usuarioDb = App_Model_DbTable_Factory::get('Usuario');
	$arquivoDb = App_Model_DbTable_Factory::get('Arquivo');
	$extensaoDb = App_Model_DbTable_Factory::get('Extensao');
	
	$select = $historicoDb->select()
			    ->setIntegrityCheck( false )
			    ->from( 
				array( 'h' => $historicoDb ),
				array( 
				    'data',
				    'acao'
				)
			    )
			    ->join( 
				array( 'u' => $usuarioDb ), 
				'u.id = h.usuario_id', 
				array( 'usuario' => 'id', 'nome' ) 
			    )
			    ->join( 
				array( 'a' => $arquivoDb ), 
				'a.id = h.arquivo_id', 
				array( 'arquivo' => 'nome' )
			    )
			    ->join( 
				array( 'e' => $extensaoDb ), 
				'e.id = a.extensao_id', 
				array( 'extensao' => 'descricao' )
			    );
	
	
	if ( !empty( $filtro['usuario'] ) )
	    $select->where( 'h.usuario_id = ?', $filtro['usuario'] );
	
	if ( !empty( $filtro['acao'] ) )
	    $select->where( 'h.acao = ?', $filtro['acao'] );
	
	if ( !empty( $filtro['extensao'] ) )
	    $select->where( 'a.extensao_id = ?', $filtro['extensao'] );
	
	if ( !empty( $filtro['arquivo'] ) )
	    $select->where( 'h.arquivo_id = ?', $filtro['arquivo'] );
	
	if ( !empty( $filtro['data_inicial'] ) && Zend_Date::isDate( $filtro['data_inicial'] ) )
	    $select->where( 'DATE(h.data) >= ?', new Zend_Db_Expr( "STR_TO_DATE('" . $filtro['data_inicial'] . "', '%d/%m/%Y')" ) );
	
	if ( !empty( $filtro['data_final'] ) && Zend_Date::isDate( $filtro['data_final'] )  )
	    $select->where( 'DATE(h.data) <= ?', new Zend_Db_Expr( "STR_TO_DATE('" . $filtro['data_final'] . "', '%d/%m/%Y')" ) );
	
	$select->order( 'h.data DESC' );
	
	return $historicoDb->fetchAll( $select );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function contabilizaAcoesHora()
    {
	$historicoDb = App_Model_DbTable_Factory::get('Historico');

	$selectDownload = $historicoDb->select()
				    ->from( 
					array( 'd' => $historicoDb ), 
					array( new Zend_Db_Expr('COUNT(1)') ) 
				    )
				->where( 'd.acao = ?', 'D')
				->where( 'DATE_FORMAT(d.data, "%H") = hora' );

	$selectUpload = $historicoDb->select()
				    ->from( 
					array( 'u' => $historicoDb ), 
					array( new Zend_Db_Expr('COUNT(1)') )
				    )
				->where( 'u.acao = ?', 'U')
				->where( 'DATE_FORMAT(u.data, "%H") = hora' );


	$select = $historicoDb->select()
			    ->from(
				    array( 'h' => $historicoDb ),
				    array( 
					'horario'   => new Zend_Db_Expr( 'DATE_FORMAT( h.data, "%H:00")' ),
					'hora'	    => new Zend_Db_Expr( 'DATE_FORMAT( h.data, "%H")' ),
					'downloads' => new Zend_Db_Expr( '(' . $selectDownload . ')' ),
					'uploads'   => new Zend_Db_Expr( '(' . $selectUpload . ')' ),
				    )
			    )
			    ->where( 'MONTH(h.data) = MONTH(NOW())')
			    ->where( 'YEAR(h.data) = YEAR(NOW())')
			    ->group( 'hora' )
			    ->order( 'hora' );
	
	return $historicoDb->fetchAll( $select );
    }
    
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function contabilizaExtensoes()
    {
	$extensaoDb = App_Model_DbTable_Factory::get('Extensao');
	$arquivoDb = App_Model_DbTable_Factory::get('Arquivo');
	$historicoDb = App_Model_DbTable_Factory::get('Historico');
	
	$selectDownload = $extensaoDb->select()
				    ->setIntegrityCheck( false )
				    ->from( 
					array( 'a' => $arquivoDb ),
					array( new Zend_Db_Expr( 'COUNT(1)' ) )
				    )
				    ->join( 
					array( 'h' => $historicoDb ),
					'h.arquivo_id = a.id',
					array()
				    )
				    ->where( 'h.acao = ?', 'D' )
				    ->where( 'MONTH(h.data) = MONTH(NOW())' )
				    ->where( 'YEAR(h.data) = YEAR(NOW())' )
				    ->where( 'a.extensao_id = ext' );
	
	$selectUpload = $extensaoDb->select()
				    ->setIntegrityCheck( false )
				    ->from( 
					array( 'a' => $arquivoDb ),
					array( new Zend_Db_Expr( 'COUNT(1)' ) )
				    )
				    ->join( 
					array( 'h' => $historicoDb ),
					'h.arquivo_id = a.id',
					array()
				    )
				    ->where( 'h.acao = ?', 'U' )
				    ->where( 'MONTH(h.data) = MONTH(NOW())' )
				    ->where( 'YEAR(h.data) = YEAR(NOW())' )
				    ->where( 'a.extensao_id = ext' );

	$select  = $extensaoDb->select()
			      ->from(
				  array( 'e' => $extensaoDb ),
				  array(
				      'descricao',
				      'ext'	   => 'id',
				      'downloads'  => new Zend_Db_Expr( '(' . $selectDownload . ')' ),
				      'uploads'	   => new Zend_Db_Expr( '(' . $selectUpload . ')' )
				  )
				)
				->having( 'downloads > ?', 0 )
				->orHaving( 'uploads > ?', 0 );
	
	return $extensaoDb->fetchAll( $select );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function contabilizaCategoras()
    {
	$categoriaDb = App_Model_DbTable_Factory::get('Categoria');
	$categoriaArquivoDb = App_Model_DbTable_Factory::get('CategoriaArquivo');
	$arquivoDb = App_Model_DbTable_Factory::get('Arquivo');
	$historicoDb = App_Model_DbTable_Factory::get('Historico');
	
	$selectDownload = $categoriaDb->select()
				    ->setIntegrityCheck( false )
				    ->from( 
					array( 'a' => $arquivoDb ),
					array( new Zend_Db_Expr( 'COUNT(1)' ) )
				    )
				    ->join( 
					array( 'h' => $historicoDb ),
					'h.arquivo_id = a.id',
					array()
				    )
				    ->join(
					array( 'ca' => $categoriaArquivoDb ),
					'ca.arquivo_id = a.id',
					array()
				    )
				    ->where( 'h.acao = ?', 'D' )
				    ->where( 'MONTH(h.data) = MONTH(NOW())' )
				    ->where( 'YEAR(h.data) = YEAR(NOW())' )
				    ->where( 'ca.categoria_id = cat' );
	
	$selectUpload = $categoriaDb->select()
				    ->setIntegrityCheck( false )
				    ->from( 
					array( 'a' => $arquivoDb ),
					array( new Zend_Db_Expr( 'COUNT(1)' ) )
				    )
				    ->join( 
					array( 'h' => $historicoDb ),
					'h.arquivo_id = a.id',
					array()
				    )
				    ->join(
					array( 'ca' => $categoriaArquivoDb ),
					'ca.arquivo_id = a.id',
					array()
				    )
				    ->where( 'h.acao = ?', 'U' )
				    ->where( 'MONTH(h.data) = MONTH(NOW())' )
				    ->where( 'YEAR(h.data) = YEAR(NOW())' )
				    ->where( 'ca.categoria_id = cat' );

	$select  = $categoriaDb->select()
			      ->from(
				  array( 'c' => $categoriaDb ),
				  array(
				      'nome',
				      'cat'	   => 'id',
				      'downloads'  => new Zend_Db_Expr( '(' . $selectDownload . ')' ),
				      'uploads'	   => new Zend_Db_Expr( '(' . $selectUpload . ')' )
				  )
				)
				->having( 'downloads > ?', 0 )
				->orHaving( 'uploads > ?', 0 );
	
	return $categoriaDb->fetchAll( $select );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function contabilizaPerfil()
    {
	$perfilDb = App_Model_DbTable_Factory::get('Perfil');
	$usuarioDb = App_Model_DbTable_Factory::get('Usuario');
	$historicoDb = App_Model_DbTable_Factory::get('Historico');
	
	$selectDownload = $perfilDb->select()
				    ->setIntegrityCheck( false )
				    ->from( 
					array( 'h' => $historicoDb ),
					array( new Zend_Db_Expr( 'COUNT(1)' ) )
				    )
				    ->join( 
					array( 'u' => $usuarioDb ),
					'u.id = h.usuario_id',
					array()
				    )
				    ->where( 'h.acao = ?', 'D' )
				    ->where( 'MONTH(h.data) = MONTH(NOW())' )
				    ->where( 'YEAR(h.data) = YEAR(NOW())' )
				    ->where( 'u.perfil_id = perfil' );
	
	$selectUpload = $perfilDb->select()
				    ->setIntegrityCheck( false )
				    ->from( 
					array( 'h' => $historicoDb ),
					array( new Zend_Db_Expr( 'COUNT(1)' ) )
				    )
				    ->join( 
					array( 'u' => $usuarioDb ),
					'u.id = h.usuario_id',
					array()
				    )
				    ->where( 'h.acao = ?', 'U' )
				    ->where( 'MONTH(h.data) = MONTH(NOW())' )
				    ->where( 'YEAR(h.data) = YEAR(NOW())' )
				    ->where( 'u.perfil_id = perfil' );

	$select  = $perfilDb->select()
			      ->from(
				  array( 'p' => $perfilDb ),
				  array(
				      'nome',
				      'perfil'	   => 'id',
				      'downloads'  => new Zend_Db_Expr( '(' . $selectDownload . ')' ),
				      'uploads'	   => new Zend_Db_Expr( '(' . $selectUpload . ')' )
				  )
				)
				->having( 'downloads > ?', 0 )
				->orHaving( 'uploads > ?', 0 );
	
	return $perfilDb->fetchAll( $select );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function contabilizaBaixados()
    {
	$historicoDb = App_Model_DbTable_Factory::get('Historico');
	$arquivoDb = App_Model_DbTable_Factory::get('Arquivo');
	
	$select  = $historicoDb->select()
			    ->setIntegrityCheck( false )
			    ->from(
			      array( 'h' => $historicoDb ),
			      array( 'total'  => new Zend_Db_Expr( 'COUNT(1)' ) )
			    )
			    ->join( array( 'a' => $arquivoDb ), 'h.arquivo_id = a.id', array( 'nome' ) )
			    ->where( 'h.acao = ?', 'D')
			    ->group( 'h.arquivo_id' )
			    ->order( 'total DESC' )
			    ->limit( 20 );
	
	return $historicoDb->fetchAll( $select );
    }
}