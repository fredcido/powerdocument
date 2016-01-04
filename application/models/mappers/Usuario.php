<?php

/** 
 * 
 */
class Model_Mapper_Usuario extends App_Model_Mapper_Abstract
{
    /**
     *
     * @param int $id
     * @return Zend_Db_Table_Rowset
     */
    public function listUsuariosByPerfil( $id )
    {
	$dbUsuario = App_Model_DbTable_Factory::get('Usuario');
	
	$select = $dbUsuario->select()
			    ->where( 'perfil_id = ?', $id );
	
	return $dbUsuario->fetchAll( $select );
    }
    
    /**
     *
     * @param array $ids
     * @return Zend_Db_Table_Rowset
     */
    public function listUsuariosToSend( $arquivo, $perfis )
    {
	$dbUsuario = App_Model_DbTable_Factory::get('Usuario');
	$dbPerfilCategoria = App_Model_DbTable_Factory::get('PerfilCategoria');
	$dbCategoriaArquivo = App_Model_DbTable_Factory::get('CategoriaArquivo');
	$dbArquivo = App_Model_DbTable_Factory::get('Arquivo');
	
	$select = $dbUsuario->select()
			    ->setIntegrityCheck( false )
			    ->from( array( 'u' => $dbUsuario ) )
			    ->join( 
				array( 'pc' => $dbPerfilCategoria ),
				'pc.perfil_id = u.perfil_id',
				array()
			    )
			    ->join(
				array( 'ca' => $dbCategoriaArquivo ),
				'pc.categoria_id = ca.categoria_id',
				array()
			    )
			    ->join(
				array( 'a' => $dbArquivo ),
				'a.id = ca.arquivo_id',
				array()
			    )
			    ->where( 'u.perfil_id IN (?)', $perfis )
			    ->where( 'u.liberado = ?', 1 )
			    ->where( 'a.id = ?', $arquivo )
			    ->group( 'u.id' );
	
	return $dbUsuario->fetchAll( $select );
    }
}