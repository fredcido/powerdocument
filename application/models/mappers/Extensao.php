<?php

/** 
 * 
 */
class Model_Mapper_Extensao extends App_Model_Mapper_Abstract
{
	/**
	 * 
	 * @access public
	 * @return Zend_Db_Table_Rowset 
	 */
	public function listTree ()
	{
		$dbTable = App_Model_DbTable_Factory::get( 'extensao' );
		
		$select = $dbTable->select()
			->where('liberado = :liberado')
			->bind(array(':liberado' => 1))
                        ->order( 'descricao' );
			
		return $dbTable->fetchAll( $select );
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $extension
	 */
	public function allowed ( $extension )
	{
		$dbTable = App_Model_DbTable_Factory::get( 'extensao' );
		
		$select = $dbTable->select()
			->where('liberado = :liberado')
			->where('descricao = :descricao')
			->bind(
				array(
					':liberado' 	=> 1,
					':descricao' 	=> $extension
				)
			);
			
		$row = $dbTable->fetchRow( $select );
		
		return empty($row) ? false : $row->id;
	}
}