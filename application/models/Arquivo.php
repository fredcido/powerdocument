<?php

/**
 *
 */
class Model_Arquivo extends App_Model_Abstract
{

    /**
     * 
     * @var Model_DbTable_Arquivo
     */
    protected $_dbTable;

    /**
     * 
     * @access 	protected
     * @return 	Model_DbTable_Arquivo
     */
    protected function _getDbTable()
    {
	if ( is_null( $this->_dbTable ) )
	    $this->_dbTable = new Model_DbTable_Arquivo();

	return $this->_dbTable;
    }

    /**
     * 
     * @access 	public
     * @return 	boolean
     */
    public function save()
    {
	$dbAdapter = $this->_getDbTable()->getAdapter();

	$dbAdapter->beginTransaction();

	$dataForm = $this->_data;

	$isUpload = false;

	try {

	    $isUpload = true;
	    $hasFile = false;
	    
	    // Se nao tem categorias definidas
	    if ( empty( $dataForm['categorias'] ) ) {
		$this->_message->addMessage( 'Seleciona ao menos uma categoria', App_Message::ERROR );
		return false;
	    }
	    
	    $adapter = new Zend_File_Transfer_Adapter_Http( array('useByteString' => false) );

	    //Se nenhum arquivo for enviado
	    if ( !$adapter->isUploaded( 'arquivo' ) ) {
		
		if ( empty( $this->_data['id'] ) ) {
		    $this->_message->addMessage( 'Arquivo n&atilde;o foi enviado', App_Message::ERROR );
		    return false;
		}
	    } else {
		
		//Se o arquivo for invalido
		if ( !$adapter->isValid( 'arquivo' ) ) {
		    foreach ( $adapter->getMessages() as $message )
			$this->_message->addMessage( $message, App_Message::ERROR );

		    return false;
		}

		$fileInfo = $adapter->getFileInfo();
		$file = $fileInfo['arquivo'];

		$dirUpload = APPLICATION_PATH . '/../files/';
		$extension = end( explode( '.', $file['name'] ) );
		
		// Se estiver alterando o arquivo
		if ( !empty( $this->_data['id'] ) ) {
		    
		    // Busca dados do arquivo anterior
		    $oldFileRow = $this->fetchRow( $this->_data['id'] );
		    $oldFile = $dirUpload . $oldFileRow->path . '.' . $oldFileRow->extensao;
		    
		    // Remove Arquivo
		    if ( file_exists( $oldFile ) )
			unlink( $oldFile );
		}

		$mapperExtensao = new Model_Mapper_Extensao();

		if ( !($allowed = $mapperExtensao->allowed( $extension )) ) {
		    $this->_message->addMessage( 'Extens&atilde;o n&atilde;o permitida', App_Message::ERROR );
		    return false;
		}

		$fileName = parent::randomName();

		$this->_data['extensao_id'] = $allowed;
		$this->_data['usuario_id'] = Zend_Auth::getInstance()->getIdentity()->id;
		$this->_data['tamanho'] = $file['size'];
		$this->_data['path'] = $fileName;

		$fileName .= '.' . strtolower( $extension );

		$adapter->setDestination( $dirUpload );

		$adapter->addFilter(
			'Rename', array(
			    'target'	=> $dirUpload . DIRECTORY_SEPARATOR . $fileName,
			    'overwrite' => true
			)
		);
		
		$hasFile = true;
	    }

	    $arquivoId = parent::_simpleSave();

	    // Trata categorias vinculadas ao perfil
	    $this->_saveCategorias( $arquivoId, $dataForm );

	    // Trata tags vinculadas ao perfil
	    $this->_saveTags( $arquivoId, $dataForm );
	    
	    // Envia emails de aviso para usuarios dos perfis
	    $this->_sendAvisos( $arquivoId, $dataForm );
	    
	    if ( $hasFile ) {
		
		// Salva historico do arquivo
		$this->saveHistorico( $arquivoId, ( empty( $this->_data['id'] ) ? 'U' : 'T' ) );
	    }

	    $dbAdapter->commit();

	    if ( $hasFile )
		$adapter->receive();

	    return $arquivoId;
	} catch ( Exception $e ) {

	    $dbAdapter->rollBack();
	    $this->_message->addMessage( $this->_config->messages->error );
	    return false;
	}
    }

    /**
     *
     * @param int $arquivoId
     * @param string $acao 
     */
    public function saveHistorico( $arquivoId, $acao = 'D' )
    {
	$dbHistorico = App_Model_DbTable_Factory::get( 'Historico' );

	$row = $dbHistorico->createRow();
	$row->usuario_id = Zend_Auth::getInstance()->getIdentity()->id;
	$row->arquivo_id = $arquivoId;
	$row->acao = $acao;

	$row->save();
    }

    /**
     * 
     * @access 	public
     * @param 	int 	$param
     * @return 	array
     */
    public function listTree( $param )
    {
	switch ( $param['filter'] ) {

	    //Extensao
	    case '0':
		$mapper = new Model_Mapper_Extensao();
		return $mapper->listTree();
		break;

	    //Categoria
	    case '1':
		$mapper = new Model_Mapper_Categoria();
		return $mapper->listTree( ( empty( $param['child'] ) ? null : $param['child'] )  );
		break;

	    //Tag
	    case '2':
		$model = new Model_Tag();
		return $model->fetchAll();
		break;
	}
    }

    /**
     * 
     * @access 	public
     * @param 	array $filter
     * @return 	Zend_Db_Table_Rowset
     */
    public function files( array $filter = null )
    {
	$mapper = new Model_Mapper_Arquivo();

	return $mapper->files( $filter );
    }

    /**
     * 
     * @access 	public
     * @param 	array $param
     * @return 	array
     */
    public function updateStatus( $param )
    {
	try {

	    $row = $this->_dbTable->fetchRow( $this->_dbTable->select()->where( 'id = ?', $param['id'] ) );

	    $where = $this->_dbTable->getAdapter()->quoteInto( 'id = ?', $param['id'] );

	    $status = empty( $row['liberado'] ) ? 1 : 0;

	    $this->saveHistorico( $param['id'], 'T' );

	    $data = array('liberado' => $status);

	    $this->_dbTable->update( $data, $where );

	    return array('result' => true, 'status' => $status);
	} catch ( Exception $e ) {
	    return array('result' => false);
	}
    }

    /**
     *
     * @param int $id 
     */
    protected function _saveCategorias( $id, $dataForm )
    {
	// Busca categorias vinculadas ao perfil
	$categorias = $this->listCategoriasArray( $id );
	// Pega categorias vinculadas na tela
	$categoriaTela = empty( $dataForm['categorias'] ) ? array() : $dataForm['categorias'];

	// Categorias para inserir
	$novasCategorias = array_diff( $categoriaTela, $categorias );
	// Categorias para remover
	$velhasCategorias = array_diff( $categorias, $categoriaTela );

	$dbCategoria = App_Model_DbTable_Factory::get( 'CategoriaArquivo' );

	// Se existir novas categorias para serem inseridas
	if ( !empty( $novasCategorias ) ) {

	    // Insere novas categorias
	    foreach ( $novasCategorias as $categoria ) {

		$rowCategoria = $dbCategoria->createRow();
		$rowCategoria->arquivo_id = $id;
		$rowCategoria->categoria_id = $categoria;
		$rowCategoria->save();
	    }
	}

	// Se existirem categorias para serem removidas
	if ( !empty( $velhasCategorias ) ) {

	    $where = array();
	    $where[] = $dbCategoria->getAdapter()->quoteInto( 'categoria_id IN (?)', $velhasCategorias );
	    $where[] = $dbCategoria->getAdapter()->quoteInto( 'arquivo_id = ?', $id );

	    $dbCategoria->delete( $where );
	}
    }

    /**
     *
     * @param int $id
     * @return array
     */
    public function listCategoriasArray( $id )
    {
	$mapperArquivo = new Model_Mapper_Arquivo();
	$rows = $mapperArquivo->listCategorias( $id );

	$data = array();
	foreach ( $rows as $row )
	    $data[] = $row->id;

	return $data;
    }

    /**
     *
     * @param int $id 
     */
    protected function _saveTags( $id, $dataForm )
    {
	// Busca tags vinculadas ao perfil
	$tags = $this->listTagsArray( $id );
	// Pega tags vinculadas na tela
	$tagsTela = empty( $dataForm['tags'] ) ? array() : $dataForm['tags'];

	// Tags para inserir
	$novasTags = array_diff( $tagsTela, $tags );
	// Tags para remover
	$velhasTags = array_diff( $tags, $tagsTela );

	$dbTag = App_Model_DbTable_Factory::get( 'TagArquivo' );

	// Se existir novas tags para serem inseridas
	if ( !empty( $novasTags ) ) {

	    // Insere novas tags
	    foreach ( $novasTags as $tag ) {

		$rowTag = $dbTag->createRow();
		$rowTag->arquivo_id = $id;
		$rowTag->tag_id = $tag;
		$rowTag->save();
	    }
	}

	// Se existirem tags para serem removidas
	if ( !empty( $velhasTags ) ) {

	    $where = array();
	    $where[] = $dbTag->getAdapter()->quoteInto( 'tag_id IN (?)', $velhasTags );
	    $where[] = $dbTag->getAdapter()->quoteInto( 'arquivo_id = ?', $id );

	    $dbTag->delete( $where );
	}
    }

    /**
     *
     * @param int $id
     * @return array
     */
    public function listTagsArray( $id )
    {
	$mapperArquivo = new Model_Mapper_Arquivo();
	$rows = $mapperArquivo->listTags( $id );

	$data = array();
	foreach ( $rows as $row )
	    $data[] = $row->id;

	return $data;
    }

    /**
     *
     * @return bool
     */
    public function validaDownload()
    {
	try {

	    // Verifica se o usuario realmente tem permissao para fazer download
	    if ( !App_Util_Access::checkAccess( App_Util_Access::DOWNLOAD ) )
		return array('valid' => false, 'message' => 'Usu&aacute;rio n&atilde;o tem permiss&atilde;o para realizar download.');

	    if ( empty( $this->_data['arquivo'] ) )
		return array('valid' => false, 'message' => $this->_config->messages->error);

	    // Busca arquivos para download
	    $arquivo = $this->fetchRow( $this->_data['arquivo'] );
	    if ( empty( $arquivo ) )
		return array('valid' => false, 'message' => 'Arquivo n&atilde;o encontrado.');
	    
	    $path = APPLICATION_PATH . '/../files/' . $arquivo->path . '.' . $arquivo->extensao;
	    if ( !file_exists( $path ) )
		return array('valid' => false, 'message' => 'Arquivo n&atilde;o encontrado.');

	    $download = new stdClass();
	    $download->size = $arquivo->tamanho;
	    $download->total = 1;

	    $modelLimite = new Model_Limite();
	    $validaDownload = $modelLimite->verificaLimiteUsuario( $download );

	    if ( $validaDownload ) {
		return array('valid' => true);
	    } else {
		return array('valid' => false, 'message' => 'Limite de download excedido.');
	    }
	} catch ( Exception $e ) {
	    return array(
		'valid' => false,
		'message' => $this->_config->messages->error
	    );
	}
    }

    /**
     *
     * @return bool
     */
    public function validaDownloadMultiplos()
    {
	try {

	    // Verifica se o usuario realmente tem permissao para fazer download
	    if ( !App_Util_Access::checkAccess( App_Util_Access::DOWNLOAD ) )
		return array('valid' => false, 'message' => 'Usu&aacute;rio n&atilde;o tem permiss&atilde;o para realizar download.');

	    if ( empty( $this->_data['arquivos'] ) )
		return array('valid' => false, 'message' => $this->_config->messages->error);

	    // Busca arquivos para download
	    $dbArquivo = App_Model_DbTable_Factory::get( 'Arquivo' );
	    $rows = $dbArquivo->select()->where( 'id IN(?)', $this->_data['arquivos'] );

	    $download = new stdClass();
	    $download->size = 0;
	    $download->total = 0;

	    foreach ( $rows as $row ) {

		$download->size += $row->tamanho;
		$download->total++;
	    }

	    $modelLimite = new Model_Limite();
	    $validaDownload = $modelLimite->verificaLimiteUsuario( $download );

	    if ( $validaDownload ) {
		return array('valid' => true);
	    } else {
		return array('valid' => false, 'message' => 'Limite de download excedido.');
	    }
	} catch ( Exception $e ) {
	    return array(
		'valid' => false,
		'message' => $this->_config->messages->error
	    );
	}
    }
    
    /**
     *
     * @return array
     */
    public function downloadArquivo()
    {
	$dbArquivo = App_Model_DbTable_Factory::get( 'Arquivo' );

	$dbArquivo->getAdapter()->beginTransaction();
	try {
	    
	    // Verifica se o usuario realmente tem permissao para fazer download
	    if ( !App_Util_Access::checkAccess( App_Util_Access::DOWNLOAD ) )
		return false;

	    if ( empty( $this->_data['arquivo_id'] ) )
		return false;
	    
	    $arquivo = $this->fetchRow( $this->_data['arquivo_id'] );
	    if ( empty( $arquivo ) )
		return false;
	    
	    $this->saveHistorico( $arquivo->id );
	    
	    $path = APPLICATION_PATH . '/../files/' . $arquivo->path . '.' . $arquivo->extensao;
	    if ( !file_exists( $path ) )
		return false;
	    
	    $mimeFinder = new App_Util_Mime();
	    $mime = $mimeFinder->getMimeFile( $arquivo->extensao );
	    
	    $info = array(
		'name' => ucfirst( self::friendName(  $arquivo->nome ) ) . '.' . $arquivo->extensao,
		'path' => $path,
		'type' => $mime
	    );
	    
	    $dbArquivo->getAdapter()->commit();
	    
	    return $info;
	    
	} catch ( Exception $exc ) {
	    
	    $dbArquivo->getAdapter()->rollBack();
	    return false;
	}
    }

    /**
     *
     * @return array
     */
    public function downloadMultiplos()
    {
	$dbArquivo = App_Model_DbTable_Factory::get( 'Arquivo' );

	$dbArquivo->getAdapter()->beginTransaction();
	try {

	    // Verifica se o usuario realmente tem permissao para fazer download
	    if ( !App_Util_Access::checkAccess( App_Util_Access::DOWNLOAD ) )
		return false;

	    if ( empty( $this->_data['arquivos'] ) )
		return false;

	    $tmpPath = APPLICATION_PATH . '/../files/tmp/';
	    if ( !is_dir( $tmpPath ) )
		mkdir( $tmpPath );

	    // Gera caminho para arquivo zipado
	    $zipName = $tmpPath . $this->randomName() . '.zip';
	    
	    // Abre arquivo para ser zipado
	    $zipObj = new ZipArchive();

	    if ( true !== $zipObj->open( $zipName, ZIPARCHIVE::OVERWRITE ) )
		return false;

	    // Busca arquivos para download
	    $select = $dbArquivo->select()
		    ->setIntegrityCheck( false )
		    ->from( array('a' => $dbArquivo), array('nome', 'path', 'id') )
		    ->join(
			    array('e' => App_Model_DbTable_Factory::get( 'Extensao' )), 'a.extensao_id = e.id', array('ext' => 'descricao')
		    )
		    ->where( 'a.id IN(?)', $this->_data['arquivos'] );

	    $rows = $dbArquivo->fetchAll( $select );

	    if ( empty( $rows ) )
		return false;

	    $dirFiles = APPLICATION_PATH . '/../files/';

	    // Insere todas as arquivos no zip
	    foreach ( $rows as $key => $row ) {

		$ext = '.' . $row->ext;

		$filePath = $dirFiles . ucfirst( $row->path ) . $ext;
		
		if ( !file_exists( $filePath ) )
		    continue;
		
		//$zipFileName = self::friendName( $row->nome ) . $ext;
		
		$name = $row->nome;
		if ( mb_detect_encoding( $name ) == 'UTF-8' )
		    $name = iconv( 'UTF-8', 'ASCII', $name );
		
		//$zipFileName = preg_replace( '/[^A-Za-z0-9]/', '_', utf8_decode( $row->nome ) ) . $ext;
		$zipFileName = $name . $ext;
		if ( false !== $zipObj->locateName( $zipFileName, ZIPARCHIVE::FL_NOCASE ) )
		    $zipFileName = str_pad( $key, 2, '0', STR_PAD_LEFT ) . '_' . $zipFileName;

		// Adiciona arquivo no zip
		$zipObj->addFile( $filePath, $zipFileName );

		$this->saveHistorico( $row->id );
	    }

	    $zipObj->close();

	    $dbArquivo->getAdapter()->commit();

	    return array(
		'path' => $zipName,
		'name' => ucfirst( self::friendName( $this->_config->geral->title ) ) . '_' . Zend_Date::now()->toString( 'dd/MM/yyyy_HH:mm') . '.zip'
	    );
	} catch ( Exception $e ) {

	    $dbArquivo->getAdapter()->rollBack();

	    return false;
	}
    }

    /**
     * 
     * @access 	public
     * @param 	int 	$id
     * @return 	object
     */
    public function fetchRow( $id = null )
    {
	$dbArquivo = App_Model_DbTable_Factory::get( 'Arquivo' );

	$select = $dbArquivo->select()
		->setIntegrityCheck( false )
		->from(
			array('a' => $dbArquivo), array('*')
		)
		->join(
		array('e' => App_Model_DbTable_Factory::get( 'extensao' )), 'e.id = a.extensao_id', array('extensao' => 'descricao')
	);

	if ( !is_null( $id ) )
	    $select->where( 'a.id = ?', $id );



	return $dbArquivo->fetchRow( $select );
    }
    
    /**
     *
     * @param App_Model_DbTable_Row_Abstract
     * @return string
     */
    public function getHashView( $arquivo )
    {
	try {
	    
	    $dbView = App_Model_DbTable_Factory::get('View');
	    
	    $hash = $this->randomName();
	    
	    $row = $dbView->createRow();
	    $row->arquivo_id = $arquivo->id;
	    $row->usuario_id = Zend_Auth::getInstance()->getIdentity()->id;
	    $row->hash = $hash;
	    
	    $row->save();
	    
	    return $hash;
	    
	} catch ( Exception $e ) {
	    return '';
	}
    }
    
    /**
     *
     * @param string $hash
     * @return App_Model_DbTable_Row_Abstract
     */
    public function getArquivoView( $hash )
    {
	$this->_getDbTable()->getAdapter()->beginTransaction();
	try {
	    
	    $mapperArquivo = new Model_Mapper_Arquivo();
	    $arquivo = $mapperArquivo->getArquivoView( $hash );
	    
	    if ( empty( $arquivo ) )
		return false;
	    
	    $dbView = App_Model_DbTable_Factory::get('View');
	    $row = $dbView->find( $arquivo->view )->current();
	    $row->liberado = 1;
	    $row->save();
	     
	    $this->_getDbTable()->getAdapter()->commit();
	    
	    return $arquivo;
	    
	} catch ( Exception $e ) {
	    $this->_getDbTable()->getAdapter()->rolllBack();
	    
	    return false;
	}
    }
    
    /**
     *
     * @param int $id
     * @return array
     */
    public function delete( $id )
    {
	$this->_getDbTable()->getAdapter()->beginTransaction();
	try {
	    
	    if ( !App_Util_Access::checkAccess( App_Util_Access::EXCLUIR ) )
		return array('valid' => false, 'message' => 'Usu&aacute;rio n&atilde;o tem permiss&atilde;o para remover arquivo.');
	    
	    $arquivo = $this->fetchRow( $id );
	    if ( empty( $arquivo ) )
		return array('valid' => false, 'message' => 'Arquivo n&atilde;o encontrado.');
	    
	    $where = $this->_getDbTable()->getAdapter()->quoteInto('arquivo_id = ?', $id );
	    
	    // Remove toda ocorrencia do arquivo no historico
	    $dbHistorico = App_Model_DbTable_Factory::get('Historico');
	    $dbHistorico->delete( $where );
	    
	    // Remove toda ocorrencia do arquivo vinculado a categoria
	    $dbCategoriaArquivo = App_Model_DbTable_Factory::get('CategoriaArquivo');
	    $dbCategoriaArquivo->delete( $where );
	    
	    // Remove toda ocorrencia do arquivo vinculado a tags
	    $dbTagArquivo = App_Model_DbTable_Factory::get('TagArquivo');
	    $dbTagArquivo->delete( $where );
	    
	    // Remove toda ocorrencia do arquivo vinculado a views
	    $dbView = App_Model_DbTable_Factory::get('View');
	    $dbView->delete( $where );
	    
	    // Deleta o registro do arquivo do banco
	    $where = $this->_getDbTable()->getAdapter()->quoteInto('id = ?', $id );
	    $this->_getDbTable()->delete( $where );
	    
	    // Remove arquivo fisicamente
	    $filePath = APPLICATION_PATH . '/../files/' . $arquivo->path . '.' . $arquivo->extensao;
	    if ( file_exists( $filePath ) )
		unlink ( $filePath );
	    
	    $this->_getDbTable()->getAdapter()->commit();
	    
	    return array( 'valid' => true );
	    
	} catch ( Exception $e ) {
	    
	    $this->_getDbTable()->getAdapter()->rollBack();
	  
	    return array(
		'valid'	    => false,
		'message'   => $this->_config->messages->error
	    );
	}
    }
    
    /**
     *
     * @param int $arquivoId
     * @param array $data
     * @return bool
     */
    protected function _sendAvisos( $arquivoId, $data )
    {
	try {
	    // Se foi selecionado perfil para aviso
	    if ( empty( $data['perfil'] ) ) 
		return true;

	    $mapperUsuario = new Model_Mapper_Usuario();
	    $usuariosPerfis = $mapperUsuario->listUsuariosToSend( $arquivoId, $data['perfil'] );

	    $emails = array();
	    foreach ( $usuariosPerfis as $usuario )
		$emails[] = $usuario->email;

	    $layoutPath = APPLICATION_PATH . '/modules/admin/views/scripts/email/';

	    // Envia email para o usuarios dos perfis selecionados
	    $html = new Zend_View();
	    $html->setScriptPath( $layoutPath );
	    $html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	    
	    $arquivo = $this->fetchRow( $arquivoId );
	    
	    $mapperArquivo = new Model_Mapper_Arquivo();
	    $categorias = $mapperArquivo->listCategorias( $arquivoId );

	    $html->assign( 'arquivo', $arquivo );
	    $html->assign( 'criacao', empty( $data['id'] ) );
	    $html->assign( 'titulo', $this->_config->geral->title );
	    $html->assign( 'categorias', $categorias );
	    
	    // Busca configuracoes do sistema
	    $configModel = new Model_Configuracao();
	    $configuracao = $configModel->fetchRow();

	    $mail = new Zend_Mail( 'utf-8' );

	    $mail->setBodyHtml( $html->render( 'arquivo.phtml' ) );
	    $mail->setFrom( $this->_config->email->address, $this->_config->geral->title );
	    $mail->addTo( $configuracao->email_admin );
	    $mail->addBcc( $emails );
	    $mail->setSubject( 'Lembrete de arquivo - ' . $this->_config->geral->title );

	    $mail->send();
	    
	    return true;
	
	} catch ( Exception $e ) {
	    return false;
	}
    }
}