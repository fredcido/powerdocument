<?php

/**
 * 
 *
 */
abstract class App_Model_Mapper_Abstract
{
	/**
	 * 
	 * @var unknown_type
	 */
	protected $_factoryDb;

	/**
	 * 
	 * Enter description here ...
	 */
	public function __construct ()
	{
		$this->_factoryDb = new App_Model_DbTable_Factory();
	}
}