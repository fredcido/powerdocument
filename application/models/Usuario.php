<?php

/**
 *
 */
class Model_Usuario extends App_Model_Abstract
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
	    $this->_dbTable = new Model_DbTable_Usuario();

	return $this->_dbTable;
    }

    /**
     * 
     * @access 	public
     * @return 	boolean
     */

    /**
     * 
     * @access 	public
     * @return 	boolean
     */
    public function save()
    {
	try {

	    // Verifica se o email ja foi cadastrado
	    $verificaEmail = $this->vericaEmailUsuario( $this->_data );
	    if ( !empty( $verificaEmail ) ) {

		$this->_message->addMessage( 'E-mail j&aacute; cadastrado.', App_Message::ERROR );
		return false;
	    }

	    if ( empty( $this->_data['senha'] ) )
		unset( $this->_data['senha'] );
	    else
		$this->_data['senha'] = sha1( $this->_data['senha'] );

	    unset( $this->_data['confirma_senha'] );
	    
	    if ( empty( $this->_data['limite_id'] ) )
		unset( $this->_data['limite_id'] );

	    $lastId = parent::_simpleSave();

	    return $lastId;
	} catch ( Exception $e ) {

	    $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );
	    return false;
	}
    }

    /**
     *
     * @param array $data
     * @return App_Model_DbTable_Row_Abstract
     */
    public function vericaEmailUsuario( $data )
    {
	// Verifica se email ja e cadastrado
	$usuarioDb = $this->_getDbTable();
	$select = $usuarioDb->select()->where( 'email = ?', $data['email'] );

	if ( !empty( $data['id'] ) )
	    $select->where( 'id <> ?', $data['id'] );

	return $usuarioDb->fetchRow( $select );
    }

    /**
     * 
     * @access 	public
     * @return 	boolean
     */
    public function login( $hash = true )
    {
	$valid = false;
	
	$auth = Zend_Auth::getInstance();

	try {

	    $authAdapter = new Zend_Auth_Adapter_DbTable(
			    Zend_Db_Table_Abstract::getDefaultAdapter(),
			    'usuario',
			    'email',
			    'senha'
	    );

	    $authAdapter->setIdentity( $this->_data['email'] );
	    $authAdapter->setCredential( $hash ? sha1( $this->_data['senha'] ) : $this->_data['senha'] );

	    $result = $auth->authenticate( $authAdapter );

	    if ( $result->isValid() ) {

		$resultSet = $authAdapter->getResultRowObject( null, 'senha' );

		// Status do usuario
		if ( '1' === $resultSet->liberado ) {
		    
		    if ( 'T' === $resultSet->nivel ) {
			
			$dataCadastro = new Zend_Date( $resultSet->dt_cadastro );
			
			$modelConfiguracao = new Model_Configuracao();
			$configuracao = $modelConfiguracao->fetchRow();
			
			if ( empty( $configuracao ) ) {
			 
			    $this->_message->addMessage( 'Sistema sem configura&ccedil;&atilde;o.', App_Message::ERROR );
			    return false;
			}
			
			if ( $dataCadastro->addDayOfYear( $configuracao->dias_temporario )->isEarlier( Zend_Date::now() ) ) {
			    
			    App_Util_Access::clear();
			    $this->_insertLogAcesso( 'I' );
			    
			    $this->_message->addMessage( 'Usu&aacute;rio tempor&aacute;rio expirado.', App_Message::ERROR );
			    return false;
			}
		    }

		    $auth->getStorage()->write( $resultSet );

		    // Cria cookie para lembrar usuario
		    $this->_cookieLogin( $this->_data['keep-logged'] );
		    
		    // Popula permissoes do usuario
		    if ( !$this->populaPermissoes() ) {
			
			$this->_message->addMessage( 'Erro ao popular permiss&otilde;es.', App_Message::ERROR );
			return false;
		    }
		    
		    // Insere log de sucesso para login
		    $this->_insertLogAcesso( 'S' );

		    $valid = true;
		    
		} else {
		    
		    $this->_message->addMessage( 'Usu&aacute;rio inativo.', App_Message::ERROR );
		    
		    // Insere log de acesso para usuario inativo
		    $this->_insertLogAcesso( 'I' );
		    App_Util_Access::clear();
		}
		
	    } else {
		
		// Insere log de acesso para erro na autenticacao
		$this->_insertLogAcesso( 'E' );
	    }

	    return $valid;
	} catch ( Exception $e ) {
	    
	    App_Util_Access::clear();
	    $this->_message->addMessage( 'Erro ao efetuar login.', App_Message::ERROR );
	    return $valid;
	}
    }
    
    /**
     *
     * @return bool
     */
    public function populaPermissoes()
    {
	$auth = Zend_Auth::getInstance();
	
	if ( !$auth->hasIdentity() ) {
	 
	    App_Util_Access::clear();
	    return false;
	}
	
	$perfilModel = new Model_Perfil();
	
	// Busca permissoes para perfil do usuario
	$permissoes = $perfilModel->listAcoesArray( $auth->getIdentity()->perfil_id );
	$this->_session->permissoes = $permissoes;
	
	// Lista categorias vinculadas ao perfil
	$categorias = $perfilModel->listCategoriasArray( $auth->getIdentity()->perfil_id );
	$this->_session->categorias = $categorias;
	
	// Busca perfil vinculado ao usuario
	$perfil =  $perfilModel->fetchRow( $auth->getIdentity()->perfil_id );
	if ( empty( $perfil ) )
	    return false;
	
	$this->_session->perfil = $perfil;
	
	$modelLimite = new Model_Limite();
	$this->_session->limite = $modelLimite->limiteUsuario( $auth->getIdentity()->id );
			
	return true;
    }
    
    /**
     *
     * @param string $resultado 
     */
    protected function _insertLogAcesso( $resultado )
    {
	$acessoDb = App_Model_DbTable_Factory::get( 'Acesso' );
	
	$acessoRow = $acessoDb->createRow();
	
	$acessoRow->email = $this->_data['email'];
	$acessoRow->resultado = $resultado;
		
	$acessoRow->save();
    }

    /**
     *
     * @access 	protected
     * @param	mixed $keepLoogedIn
     * @return 	boolean
     */
    protected function _cookieLogin( $keepLoogedIn )
    {
	try {

	    // gera nome do cookie a ser salvo
	    $cookie_name = $this->_config->geral->cookie;

	    // Se usuário nao quiser que mantenha logado, expira cookie
	    if ( empty( $keepLoogedIn ) ) {

		setcookie( $cookie_name, false );
		return true;
	    }

	    $auth = Zend_Auth::getInstance()->getIdentity();

	    $usuarioDb = new Model_DbTable_Usuario();

	    // Gera valor do cookie a ser salvo
	    $uniqid = $this->randomName();

	    $data['keeplogged'] = $uniqid;
	    $where = $usuarioDb->getAdapter()->quoteInto( 'id = ?', $auth->id );

	    // Salva hash na base
	    $usuarioDb->update( $data, $where );

	    // Define o cookie com expiração de 30 dias
	    setcookie( $cookie_name, $uniqid, ( time() + 60 * 60 * 24 * 30 ), '/' );

	    return true;
	} catch ( Exception $e ) {

	    return false;
	}
    }
    
    /**
     * Solicitacao de nova senha
     * 
     * @access 	public
     * @return 	boolean
     */
    public function checkEmailPasswordRecovery()
    {
	$usuarioDb = $this->_getDbTable();

	//Hash para confirmar solicitacao
	$data = array( 'hash' => $this->randomName() );

	//Clausula WHERE
	$where = array(
	    $usuarioDb->getAdapter()->quoteInto( 'email = ?', $this->_data['email'] ),
	    $usuarioDb->getAdapter()->quoteInto( 'liberado = ?', 1 )
	);

	if ( $usuarioDb->update( $data, $where ) ) {
	    
	    $layoutPath = APPLICATION_PATH . '/modules/admin/views/scripts/email/';
	   	    
	    // Envia email para o administrador do sistema
	    $html = new Zend_View();
	    $html->setScriptPath( $layoutPath );
	    
	    $url = Zend_Controller_Action_HelperBroker::getStaticHelper( 'url' );
	    
	    $html->assign( 'config', $this->_config );
	    $html->assign( 'link', 'http://' . $_SERVER['SERVER_NAME'] . $url->direct( 'password', 'auth', 'default' ) . '/hash/' . $data['hash'] );
	  
	    $mail = new Zend_Mail();

	    $mail->setBodyHtml( $html->render( 'reset-senha.phtml' ) );
	    $mail->setFrom( $this->_config->email->address, $this->_config->email->name );
	    $mail->addTo( $this->_data['email'] );
	    $mail->setSubject( 'Esqueceu sua senha - ' . $this->_config->geral->title );

	    $mail->send();

	    return true;
	} else
	    return false;
    }

    /**
     * Confirmacao de solicitacao de nova senha
     *  
     * @access 	public
     * @return 	boolean
     */
    public function passwordRecovery()
    {
	try {

	    $usuarioDb = $this->_getDbTable();

	    $usuarioDb->getAdapter()->beginTransaction();

	    $row = $usuarioDb->fetchRow( $usuarioDb->select()->where( 'hash = ?', $this->_data ) );
	    
	    if ( empty( $row ) )
		return false;

	    $senha = $this->randomPassword();

	    $row->hash = $this->randomName();
	    $row->senha = sha1( $senha );

	    $row->save();

	    $layoutPath = APPLICATION_PATH . '/modules/admin/views/scripts/email/';
	   	    
	    // Envia email para o administrador do sistema
	    $html = new Zend_View();
	    $html->setScriptPath( $layoutPath );
	    
	    $html->assign( 'config', $this->_config );
	    $html->assign( 'senha', $senha );

	    $mail = new Zend_Mail();

	    $mail->setBodyHtml( $html->render( 'nova-senha.phtml' ) );
	    $mail->setFrom( $this->_config->email->address, $this->_config->email->name );
	    $mail->addTo( $row->email, $row->nome );
	    $mail->setSubject( 'Nova Senha - ' . $this->_config->geral->title );

	    $mail->send();

	    $usuarioDb->getAdapter()->commit();

	    return true;
	} catch ( Exception $e ) {

	    $usuarioDb->getAdapter()->rollBack();

	    return false;
	}
    }
    
    /**
     *
     * @param array $data
     * @return array 
     */
    public function delete( $data )
    {
	$dbUsuario = $this->_getDbTable();
	
	$dbUsuario->getAdapter()->beginTransaction();
	try {
	    
	    if ( empty( $data['id'] ) )
		return array( 'status' => false, 'message' => $this->_config->messages->error );
	    
	    $mapperArquivo = new Model_Mapper_Arquivo();
	    $arquivos = $mapperArquivo->listArquivosByUsuario( $data['id'] );
	    
	    if ( $arquivos->count() > 0 )
		return array( 'status' => false, 'message' => $this->_config->messages->nodelete );
	    
	    $mapperRelatorio = new Model_Mapper_Relatorio();	    
	    $downloads = $mapperRelatorio->relatorioArquivo( array( 'usuario' => $data['id'] ) );
	    	    
	    if ( $downloads->count() > 0 )
		return array( 'status' => false, 'message' => $this->_config->messages->nodelete );
	    
	    $rowUsuario = $this->fetchRow( $data['id'] );
	    $rowUsuario->delete();
	    
	    $dbUsuario->getAdapter()->commit();
	    
	    return array( 'status' => true );
	    
	} catch ( Exception $e ) {
	    
	    $dbUsuario->getAdapter()->rollBack();
	    return array(
		'status'    => false,
		'message'   => $this->_config->messages->error
	    );
	}
    }

}