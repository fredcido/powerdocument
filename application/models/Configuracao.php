<?php

/**
 *
 */
class Model_Configuracao extends App_Model_Abstract
{

    /**
     * 
     * @var Model_DbTable_Configuracao
     */
    protected $_dbTable;

    /**
     * 
     * @access 	protected
     * @return 	Model_DbTable_Configuracao
     */
    protected function _getDbTable()
    {
	if ( is_null( $this->_dbTable ) )
	    $this->_dbTable = new Model_DbTable_Configuracao();

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
	    
	    if ( !$this->trataIpAtual() )
			throw new Exception( 'Erro ao tratar regra para IP.' );
			
		$this->_writeHtaccess();
		
	    return parent::_simpleSave();
	    
	} catch ( Exception $e ) {
	    $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );
	    return false;
	}
    }

    /**
     * 
     * Enter description here ...
     */
    protected function _writeHtaccess ()
    {
    	$htaccess = "php_value memory_limit -1\n"; 
		$htaccess .= "php_value post_max_size " . ($this->_data['upload'] * 2) . $this->_data['medida_upload']. "\n"; 
		$htaccess .= "php_value upload_max_filesize " . $this->_data['upload'] . $this->_data['medida_upload'] . "\n"; 
		$htaccess .= "php_value max_execution_time 1800\n"; 
		$htaccess .= "php_value session.gc_maxlifetime 3600\n\n";
		$htaccess .= "SetEnv APPLICATION_ENV production\n\n";
		$htaccess .= "RewriteEngine On\n\n";
		$htaccess .= "RewriteCond %{REQUEST_FILENAME} -s [OR]\n";
		$htaccess .= "RewriteCond %{REQUEST_FILENAME} -l [OR]\n";
		$htaccess .= "RewriteCond %{REQUEST_FILENAME} -f [OR]\n";
		$htaccess .= "RewriteCond %{REQUEST_FILENAME} -d\n\n";
		$htaccess .= "RewriteRule ^.*$ - [NC,L]\n";
		$htaccess .= "RewriteRule ^.*$ index.php [NC,L]\n";
    	
		file_put_contents( APPLICATION_PATH . '/../.htaccess', $htaccess );
    }
    
    public function trataIpAtual()
    {
	$dbIp = App_Model_DbTable_Factory::get('Ip');
	
	$dbIp->getAdapter()->beginTransaction();
	try {
	    
	    $where = $dbIp->getAdapter()->quoteInto('ip = ?', $_SERVER['REMOTE_ADDR'] );
	    
	    // Se estiver bloqueando, remove IP do host atual
	    if ( 'B' === $this->_data['regra_ip'] ) {
		$dbIp->delete( $where );
	    } else {
		
		$row = $dbIp->fetchRow( $where );
		if ( empty( $row ) ) {
		    
		    $newRow = $dbIp->createRow();
		    $newRow->descricao = 'Máquina atual';
		    $newRow->ip = $_SERVER['REMOTE_ADDR'];
		    $newRow->save();
		}
	    }
	
	    $dbIp->getAdapter()->commit();
	    return true;
	    
	} catch ( Exception $exc ) {
	    
	    $dbIp->getAdapter()->rollBack();
	    
	    return false;
	}
    }

}