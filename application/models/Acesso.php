<?php

/**
 *
 */
class Model_Acesso extends App_Model_Abstract
{

    /**
     * 
     * @var Model_DbTable_Tag
     */
    protected $_dbTable;

    /**
     * 
     * @access 	protected
     * @return 	Model_DbTable_Usuario
     */
    protected function _getDbTable()
    {
	if ( is_null( $this->_dbTable ) )
	    $this->_dbTable = new Model_DbTable_Acesso();

	return $this->_dbTable;
    }

    /**
     * 
     * @access 	public
     * @return 	boolean
     */
    public function save()
    {
	try {
	    return parent::_simpleSave();
	} catch ( Exception $e ) {
	    return false;
	}
    }

}