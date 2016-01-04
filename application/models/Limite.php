<?php

/**
 *
 */
class Model_Limite extends App_Model_Abstract
{

    /**
     * 
     * @var Model_DbTable_Limite
     */
    protected $_dbTable;

    /**
     * 
     * @access 	protected
     * @return 	Model_DbTable_Limite
     */
    protected function _getDbTable()
    {
		if ( is_null( $this->_dbTable ) )
		    $this->_dbTable = new Model_DbTable_Limite();
	
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
			
			$bind = array();
			
			foreach ( $this->_data as $key => $value ) {
				if ( 'id' === $key )
					continue;
					
				$bind[':'.$key] = $value;
			}
			
			$select = $this->_dbTable->select()
				->where('maximo = :maximo')
				->where('unidade_limite = :unidade_limite')
				->where('periodo = :periodo')
				->where('unidade_periodo = :unidade_periodo')
				->where('quantidade = :quantidade')
				->bind( $bind );
				
			$row = $this->_dbTable->fetchRow( $select );
			
			if ( !empty($row) ) {
				
				$this->_message->addMessage( 'Valores para limite j&aacute; cadastrados', App_Message::WARNING );
				
				return false;
				
			} else return parent::_simpleSave();
				
		} catch ( Exception $e ) {
		    
		    $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );
		    return false;
		}
    }
    
    /**
     *
     * @param int $id
     * @return App_Model_DbTable_Row_Abstract
     */
    public function limiteUsuario( $id )
    {
	$limiteDb = $this->_getDbTable();
	
	$camposLimite = array(
			    'maximo',
			    'unidade_limite',
			    'periodo',
			    'unidade_periodo',
			    'quantidade'
			);
	
	// Monta Select para Usuario
	$camposLimite['prioridade'] = new Zend_Db_Expr('0');
	
	$selectUser = $limiteDb->select()
				->setIntegrityCheck( false )
				->from( 
				    array( 'l' => $limiteDb ),
				    $camposLimite
				)
				->join( 
				    array( 'u' => App_Model_DbTable_Factory::get('Usuario') ),
				    'u.limite_id = l.id',
				    array() 
				)
				->where( 'u.id = :user_id');
	
	// Monta Select para Perfil
	$camposLimite['prioridade'] = new Zend_Db_Expr('1');
	
	$selectPerfil = $limiteDb->select()
				->setIntegrityCheck( false )
				->from( 
				    array( 'l' => $limiteDb ),
				    $camposLimite
				)
				->join( 
				    array( 'p' => App_Model_DbTable_Factory::get('Perfil') ),
				    'p.limite_id = l.id',
				    array() 
				)
				->join( 
				    array( 'u' => App_Model_DbTable_Factory::get('Usuario') ),
				    'u.perfil_id = p.id',
				    array() 
				)
				->where( 'u.id = :user_id');
	
	// Monta Select para Configuracao
	$camposLimite['prioridade'] = new Zend_Db_Expr('2');
	
	$selectConfig = $limiteDb->select()
				->setIntegrityCheck( false )
				->from( 
				    array( 'l' => $limiteDb ),
				    $camposLimite
				)
				->join( 
				    array( 'c' => App_Model_DbTable_Factory::get('Configuracao') ),
				    'c.limite_id = l.id',
				    array() 
				);
	
	$select = $limiteDb->select()
			   ->union( array( $selectUser, $selectPerfil, $selectConfig ) )
			   ->limit( 1 )
			   ->order( 'prioridade' )
			   ->bind( array( ':user_id' => $id ) );

	return $limiteDb->fetchRow( $select );
    }
    
    /**
     *
     * @return bool
     */
    public function verificaLimiteUsuario( $arquivo )
    {
	try {
	    
	    $user = Zend_Auth::getInstance()->getIdentity();
	    $limite = $this->_session->limite;

	    // Busca quantidades atuais do usuario
	    $mapper = new Model_Mapper_Limite();
	    $quantidades = $mapper->verificaLimiteUsuario( $user, $limite );
	    
	    $quantidades->total += empty( $arquivo->total ) ? 1 : $arquivo->total;
	    $quantidades->size += $arquivo->size;
	    
	    $sizeBytes = App_Util_ByteSize::convert( $limite->maximo, $limite->unidade_limite, 'B' );
	    
	    // Verifica se o download pode ser executado
	    if ( $quantidades->total <= $limite->quantidade && 
		 $quantidades->size <= $sizeBytes )
		return true;

	    
	    // Busca configuracoes do sistema
	    $configModel = new Model_Configuracao();
	    $configuracao = $configModel->fetchRow();
	    
	    $layoutPath = APPLICATION_PATH . '/modules/admin/views/scripts/email/';
		    
	    // Envia email para o administrador do sistema
	    $html = new Zend_View();
	    $html->setScriptPath( $layoutPath );
	    $html->addHelperPath('App/View/Helpers/', 'App_View_Helper');
	    	    	    
	    $html->assign( 'limites', $limite );
	    $html->assign( 'user', Zend_Auth::getInstance()->getIdentity() );
	    $html->assign( 'atual', $quantidades );
	    $html->assign( 'perfil', $this->_session->perfil );
	    $html->assign( 'titulo', $this->_config->geral->title );
	    
	    $mail = new Zend_Mail();
	    
	    $mail->setBodyHtml( $html->render( 'aviso.phtml' ) );
	    $mail->setFrom( $this->_config->email->address, $this->_config->geral->title );
	    $mail->addTo( $configuracao->email_admin );
	    $mail->setSubject( 'Aviso de limite - ' . $this->_config->geral->title );
	   
	    $mail->send();
	    
	    return !( 'B' === $configuracao->acao_limite );
	
	} catch ( Exception $e ) {
	    return false;
	}
    }
    
    /**
     *
     * @return string
     */
    public function buscaPorcentagemUsuario()
    {
	$porcentagem = '100%';
	
	try {
	    
	    $user = Zend_Auth::getInstance()->getIdentity();
	    $limite = $this->_session->limite;

	    // Busca quantidades atuais do usuario
	    $mapper = new Model_Mapper_Limite();
	    $quantidades = $mapper->verificaLimiteUsuario( $user, $limite );
	    
	    if ( $quantidades->total >= $limite->quantidade )
		    return $porcentagem;
	    
	    // Converte limite para bytes
	    $limiteTamanhoBytes = App_Util_ByteSize::convert( $limite->maximo, $limite->unidade_limite, 'B' );
	    
	    if ( $quantidades->size >= $limiteTamanhoBytes )
		    return $porcentagem;
	    
	    // Calcula porcentual de quantidade
	    $quantidades->total = ( $quantidades->total * 100 ) / $limite->quantidade;
	    
	    // Calcula porcentual de tamanho
	    $quantidades->size =  ( $quantidades->size * 100 ) / $limiteTamanhoBytes;
	   
	    $porcentagem = round( ( $quantidades->total + $quantidades->size ) / 2, 2 );
	    
	    return $porcentagem . '%';
	    
	} catch ( Exception $e ) {
	    $porcentagem = '100%';
	}
	
	return $porcentagem;
    }

}