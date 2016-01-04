<?php

/**
 *
 */
class Model_Ip extends App_Model_Abstract
{

    /**
     * 
     * @var Model_DbTable_Ip
     */
    protected $_dbTable;

    /**
     * 
     * @access 	protected
     * @return 	Model_DbTable_Ip
     */
    protected function _getDbTable()
    {
	if ( is_null( $this->_dbTable ) )
	    $this->_dbTable = new Model_DbTable_Ip();

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

	    $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );
	    return false;
	}
    }

    /**
     * 
     * @access public
     * @return array
     */
    public function delete()
    {
	try {

	    $where = $this->_getDbTable()->getAdapter()->quoteInto( 'id = ?', $this->_data['id'] );

	    $this->_getDbTable()->delete( $where );

	    $status = true;
	} catch ( Exception $e ) {

	    $status = false;
	}

	return array( 'status' => $status );
    }

}