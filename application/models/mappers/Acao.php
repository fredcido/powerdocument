<?php

/** 
 * 
 */
class Model_Mapper_Acao extends App_Model_Mapper_Abstract
{
    /**
     *
     * @return Zend_Db_Table_Rowset
     */
    public function listAll()
    {
	$acaoDb = App_Model_DbTable_Factory::get('Acao');
	
	$select = $acaoDb->select()->order('descricao');
	
	return $acaoDb->fetchAll( $select );
    }
}