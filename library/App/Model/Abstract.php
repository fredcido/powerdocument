<?php

/**
 * 
 */
abstract class App_Model_Abstract
{

    /**
     *
     * @var array
     */
    protected $_data;

    /**
     *
     * @var array
     */
    protected $_methodsValidators = array( );

    /**
     *
     * @var bool
     */
    protected $_breakOnFailure = true;

    /**
     *
     * @var App_Message
     */
    protected $_message;

    /**
     *
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     *
     * @var Zend_Config
     */
    protected $_config;

    /**
     *
     * @var Model_DbTable_Imagem
     */
    protected $_dbTableImage;

    /**
     * 
     * @var unknown_type
     */
    protected $_factoryDb;

    /**
     * 
     * @access 	public
     * @return 	void
     */
    public function __construct()
    {
	if ( method_exists( $this, '_getDbTable' ) )
	    $this->_getDbTable();

	$this->_message = new App_Message();

	// Init Session
	$config = Zend_Registry::get( 'config' );

	$this->_session = new Zend_Session_Namespace( $config->geral->appid );

	// Get Config
	$this->_config = Zend_Registry::get( 'config' );

	$this->_factoryDb = new App_Model_DbTable_Factory();
    }

    /**
     * 
     * @access 	public
     * @param 	mixed $data
     * @return 	void
     */
    public function setData( $data )
    {
	$this->_data = $data;
    }

    /**
     * 
     * @access 	public
     * @return 	mixed
     */
    public function getData()
    {
	return $this->_data;
    }

    /**
     * 
     * @access 	public
     * @param 	array 		$validators
     * @param 	boolean 	$breakOnFailure
     * @return 	void
     */
    public function setValidators( array $validators, $breakOnFailure = true )
    {
	$this->_methodsValidators = $validators;

	$this->setBreakOnFailure( $breakOnFailure );
    }

    /**
     * 
     * @access 	public
     * @param 	unknown_type $breakOnFailure
     * @return 	boolean
     */
    public function setBreakOnFailure( $breakOnFailure )
    {
	$this->_breakOnFailure = (bool) $breakOnFailure;
    }

    /**
     * 
     * @access 	public
     * @return 	boolean
     * @throws 	Exception
     */
    public function isValid()
    {
	$check = true;

	foreach ( $this->_methodsValidators as $method ) {

	    if ( method_exists( $this, $method ) ) {

		if ( !call_user_func( array( $this, $method ) ) ) {
		    if ( $this->_breakOnFailure )
			return false;
		    else
			$check = false;
		}
	    } else {

		throw new Exception( 'Method ' . $method . ' is not valid in the context of validation.' );
	    }
	}

	return $check;
    }

    /**
     * 
     * @access 	public
     * @return 	App_Message
     */
    public function getMessage()
    {
	return $this->_message;
    }

    /**
     * 
     * @access 	public
     * @return 	string
     */
    public function randomName()
    {
	return md5( uniqid( time() ) );
    }

    /**
     * 
     * Enter description here ...
     * @param unknown_type $length
     */
    public function randomPassword( $length = 6, $lower = true, $upper = true, $number = true )
    {
	$password = '';

	for ( $i = 0; $i < $length; $i++ ) {

	    $character = array( );

	    switch ( true ) {

		case $lower:
		    $character[] = chr( rand( 97, 122 ) );

		case $upper:
		    $character[] = chr( rand( 65, 90 ) );

		case $number:
		    $character[] = chr( rand( 48, 57 ) );
	    }

	    $password .= $character[rand( 0, count( $character ) - 1 )];
	}

	return $password;
    }

    /**
     * 
     * @access 	protected
     * @param 	array 						$data
     * @param 	App_Model_DbTable_Abstract 	$dbTable
     * @return 	unknown_type
     */
    protected function _cleanData( array $data, App_Model_DbTable_Abstract $dbTable )
    {
	$fields = $dbTable->info( App_Model_DbTable_Abstract::COLS );

	foreach ( $data as $key => $value ) {
	    if ( !in_array( $key, $fields ) )
		unset( $data[$key] );
	}

	return $data;
    }

    /**
     * 
     * @access 	protected
     * @param 	string 		$field
     * @param 	string 		$value
     * @return 	string
     */
    protected function urlAmigavel( $field, $value )
    {
	$url = self::friendName( $value );

	$count = 0;

	do {
	    $data = $this->_dbTable->fetchRow( $this->_dbTable->select()->where( $field . ' = ?', $url ) );

	    if ( !empty( $data ) )
		$url = $url . '-' . ++$count;
	    else
		break;
	} while ( true );

	return $url;
    }

    /**
     *
     * @param string $value
     * @return string 
     */
    public static function friendName( $value )
    {
	$strComCaracter = array( "\"", "\'", '\'', 'r$', '$', '&', '%', '#', '@', ',', '.', '|', '_', '-', '+', '/', '*', ':', ';', '!', '?', '(', ')', '{', '}', '[', ']' );
	$strSemCaracter = array( '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '' );

	$strComComum = array( ' a ', ' e ', ' o ', ' da ', ' de ', ' do ', ' em ' );
	$strSemComum = array( ' ', ' ', ' ', ' ', ' ', ' ', ' ' );

	$url = htmlentities( strtolower( $value ), ENT_NOQUOTES, 'UTF-8' );
	$url = preg_replace( '/&(.)(acute|cedil|circ|ring|tilde|uml);/', '$1', $url );
	$url = str_replace( $strComCaracter, $strSemCaracter, $url );
	$url = str_replace( $strComComum, $strSemComum, $url );
	$url = preg_replace( '/( +)/', '-', trim( $url ) );

	return $url;
    }

    /**
     * @access 	protected
     * @return 	int 		$id
     */
    protected function _simpleSave()
    {
	$this->_data = $this->_emptyToNull( $this->_data );
	
	if ( empty( $this->_data['id'] ) ) {

	    $newRow = $this->_dbTable->createRow();
	    $newRow->setFromArray( $this->_data );

	    $return = $newRow->save();
	} else {

	    $id = $this->_data['id'];

	    unset( $this->_data['id'] );

	    $where = $this->_dbTable->getAdapter()->quoteInto( 'id = ?', $id );
	    
	    // Limpa dados para alteracao
	    $data = $this->_cleanData( $this->_data, $this->_dbTable );

	    $return = $this->_dbTable->update( $data, $where );
	    $return = $return !== false ? $id : false;
	}

	if ( $return ) {

	    $this->_message->addMessage( $this->_config->messages->success, App_Message::SUCCESS );
	    return $return;
	} else {

	    $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );
	    return false;
	}
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    protected function _emptyToNull( $data )
    {
	foreach ( $data as $key => $value )
	    if (  $value === '' )
		$data[$key] = null;
	    
	return $data;
    }

    /**
     *
     * @access 	public
     * @return 	array
     */
    public function fetchAll()
    {
	return $this->_getDbTable()->fetchAll();
    }

    /**
     * 
     * @access 	public
     * @param 	int 	$id
     * @return 	App_Model_DbTable_Row_Abstract
     */
    public function fetchRow( $id = null )
    {
		$select = $this->_getDbTable()->select();
				
		if ( !is_null($id) )
			$select->where( 'id = ?', $id );
		
		return $this->_getDbTable()->fetchRow( $select );
    }

    /**
     * 
     * @access 	protected
     * @param 	array $data
     * @return 	int
     */
    protected function _saveImage( array $data )
    {
	$data = $this->_cleanData( $data, $this->_getDbTableImage() );

	// Salva nova Imagem
	$imageRow = $this->_getDbTableImage()->createRow();
	$imageRow->setFromArray( $data );
	return $imageRow->save();
    }

    /**
     *
     * @access 	public
     * @param 	int $id
     * @return 	Model_DbTable_Imagem
     */
    public function getImage( $id )
    {
	return $this->_getDbTable()->fetchRow(
			$this->_getDbTable()
				->select()
				->where( 'id = ? ', $id )
	);
    }
    
    /**
     * 
     * @access 	public
     * @param 	array 	$param
     * @return 	array
     */
    public function setStatus( array $param )
    {
	try {

	    $data = array('liberado' => $param['action']);

	    $where = $this->_dbTable->getAdapter()->quoteInto( 'id IN(?)', $param['data'] );

	    $this->_dbTable->update( $data, $where );

	    return array('result' => true);
	} catch ( Exception $e ) {

	    return array('result' => false);
	}
    }

}