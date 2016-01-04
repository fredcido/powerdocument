<?php

/**
 *
 * @category 	App
 * @package 	App_Model
 * @subpackage 	DbTable
 */
abstract class App_Model_DbTable_Abstract extends Zend_Db_Table_Abstract
{
	/**
	 *
	 * @var string
	 */
	protected $_rowClass = 'App_Model_DbTable_Row_Abstract';

	/**
	 * 
	 * @access 	public
	 * @return 	string
	 */
	public function __toString()
	{
		return $this->_name;
	}
}