<?php

/** 
 * 
 */
class Model_Mapper_Categoria extends App_Model_Mapper_Abstract
{
	/**
	 * 
	 * @access public
	 * @return Zend_Db_Table_Rowset 
	 */
	public function listTree ( $categoria_id = null )
	{
		$dbCategoria = App_Model_DbTable_Factory::get( 'categoria' );

		$bind = array();
		$bind[':liberado'] = 1;
                
                $subSelect = $dbCategoria->select()
                        ->setIntegrityCheck(false)
                        ->from(
                                $dbCategoria,
                                array(new Zend_Db_Expr('COUNT(1)'))
                        )
                        ->join(
				array( 'pc' => App_Model_DbTable_Factory::get('perfil_categoria') ),
				'pc.categoria_id = categoria.id',
				array()
			)
                        ->where('categoria.liberado = :liberado')
                        ->where('categoria.categoria_id = c.id'); 
		
		$select = $dbCategoria->select()
			->setIntegrityCheck(false)
			->from(
				array('c' => $dbCategoria),
				array('id', 'nome', 'child' => new Zend_Db_Expr('(' . $subSelect . ')'))
			);
			
		$select->join(
				array( 'pc' => App_Model_DbTable_Factory::get('perfil_categoria') ),
				'pc.categoria_id = c.id',
				array()
			)
			->join(
				array( 'p' => App_Model_DbTable_Factory::get('perfil') ),
				'p.id = pc.perfil_id',
				array()
			)
			->where('p.id = :perfil_id')
			->where('p.liberado = :liberado');
                
                if ( !empty($categoria_id) ) {
                
                    $select->where('c.categoria_id = :categoria_id');
                    
                    $bind[':categoria_id'] = $categoria_id;
                    
                } else $select->where('c.categoria_id IS NULL');
                    
		$bind[':perfil_id'] = Zend_Auth::getInstance()->getIdentity()->perfil_id;
			
		$select->where('c.liberado = :liberado')->bind( $bind )->order( 'nome' );
			
		return $dbCategoria->fetchAll( $select );
	}
	
	/**
	 *
	 * @param int $id
	 * @return Zend_Db_Table_Rowset
	 */
	public function listCategoriasByCategorias( $id )
	{
	    $dbCategoria = App_Model_DbTable_Factory::get('Categoria');
	    
	    $select = $dbCategoria->select()->where( 'categoria_id = ?', $id );		
	    
	    return $dbCategoria->fetchAll( $select );
	}   
}