<?php

/**
 *
 */
class Model_Historico extends App_Model_Abstract
{

    /**
     * 
     * @var Model_DbTable_Historico
     */
    protected $_dbTable;

    /**
     * 
     * @access 	protected
     * @return 	Model_DbTable_Historico
     */
    protected function _getDbTable()
    {
	if ( is_null( $this->_dbTable ) )
	    $this->_dbTable = new Model_DbTable_Historico();

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