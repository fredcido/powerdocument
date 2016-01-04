<?php

/**
 * 
 */
class ArquivoController extends App_Controller_Padrao
{

    /**
     * 
     * @var Model_Arquivo
     */
    protected $_model;

    /**
     *
     * @var Model_Mapper_Arquivo
     */
    protected $_mapper;

    /**
     * 
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	$this->view->noToolBar = true;

	$this->_model = new Model_Arquivo();

	$this->_mapper = new Model_Mapper_Arquivo();

	$this->_configUpload();
    }

    /**
     * 
     * Enter description here ...
     */
    protected function _configUpload()
    {
	$model = new Model_Configuracao();

	$row = $model->fetchRow();

	ini_set( 'upload_max_filesize', $row->upload . $row->medida_upload );
    }

    /**
     * @access 	protected
     * @param 	string 		$action
     * @return 	Default_Form_Arquivo
     */
    protected function _getForm( $action )
    {
	$this->_form = new Default_Form_Arquivo( array( 'action' => $action ) );

	return $this->_form;
    }

    /**
     * 
     * @access 	public
     * @return 	void
     */
    public function indexAction()
    {
	if ( false != ( $id = $this->_getParam( 'id', false ) ) ) {
	    
	    $arquivo = $this->_mapper->searchFileMd5( $id );
	    if ( !empty( $arquivo ) )
		$this->view->open_file = $arquivo->id;
	}
    }

    public function percentualAction()
    {
	if ( $this->getRequest()->isXmlHttpRequest() )
	    $this->_helper->layout()->disableLayout();

	$modelLimite = new Model_Limite();

	$this->view->percentual = $modelLimite->buscaPorcentagemUsuario();
    }

    /**
     * 
     * @access public
     * @return void
     */
    public function treeAction()
    {        
        $post = $this->getRequest()->getPost();
        
	$rows = $this->_model->listTree( $post );
                
	$view = array( 'tree-extensao', 'tree-categoria', 'tree-tag' );

	$this->_helper->layout()->disableLayout();

	$this->view->rows = $rows;

	$this->render( $view[$post['filter']] );
    }

    /**
     * 
     * @access public
     * @return void
     */
    public function viewAction()
    {
	if ( $this->getRequest()->isXmlHttpRequest() )
	    $this->_helper->layout()->disableLayout();

	$post = $this->getRequest()->getPost();

	$this->view->rows = $this->_model->files( $post );
	
	$this->_helper->viewRenderer->setRender( $this->_getParam( 'viewType', 'view-list' ) );
    }

    /**
     * (non-PHPdoc)
     * @see App_Controller_Padrao::statusAction()
     */
    public function statusAction()
    {
	$post = $this->getRequest()->getPost();

	$result = $this->_model->updateStatus( $post );

	echo $this->_helper->json( $result );
    }

    /**
     * (non-PHPdoc)
     * @see App_Controller_Padrao::saveAction()
     */
    public function saveAction()
    {
	$this->_helper->layout->disableLayout();
	$this->_helper->viewRenderer->setRender( 'iframe' );

	$form = $this->_getForm( $this->_helper->url( 'save' ) );

	if ( $this->getRequest()->isPost() ) {
	    
	    if ( $form->isValid( $this->getRequest()->getPost() ) ) {

		$this->_model->setData( $form->getValues() );

		$return = $this->_model->save();

		$message = $this->_model->getMessage()->toArray();

		$result = array(
		    'status' => (bool) $return,
		    'id' => $return,
		    'description' => $message
		);
	    } else {

		$message = new App_Message();
		$message->addMessage( $this->_config->messages->warning, App_Message::WARNING );
		
		$result = array(
		    'status' => false,
		    'description' => $message->toArray(),
		    'errors' => $form->getMessages()
		);
	    }
	    
	    $result['data'] = $form->getValues();
	}

	//$this->view->result = json_encode( $result );
	$this->_helper->json( $result );
    }

    /**
     * 
     */
    public function validaDownloadAction()
    {
	$this->_model->setData( $this->getRequest()->getPost() );
	$retorno = $this->_model->validaDownload();

	$this->_helper->json( $retorno );
    }

    /**
     * 
     */
    public function downloadsAction()
    {
	$this->_model->setData( $this->getRequest()->getPost() );
	$filename = $this->_model->downloadArquivo();

	if ( !empty( $filename ) ) {

	    header( 'Content-Description: File Transfer' );
	    header( 'Content-Type: ' . $filename['type'] );
	    header( 'Content-Disposition: attachment; filename=' . $filename['name'] );
	    header( 'Content-Transfer-Encoding: binary' );
	    header( 'Expires: 0' );
	    header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	    header( 'Pragma: public' );
	    header( 'Content-Length: ' . filesize( $filename['path'] ) );

	    $fp = fopen( $filename['path'], 'r' );

	    while ( !feof( $fp ) ) {
		echo fread( $fp, filesize( $filename['path'] ) );
		flush();
	    }

	    fclose( $fp );
	    exit;
	}

	$this->_helper->redirector->goToSimple( 'index' );
    }

    /**
     * 
     */
    public function validaDownloadMultiplosAction()
    {
	$this->_model->setData( $this->getRequest()->getPost() );
	$retorno = $this->_model->validaDownloadMultiplos();

	$this->_helper->json( $retorno );
    }

    /**
     * 
     */
    public function multiplosDownloadsAction()
    {
	$this->_model->setData( $this->getRequest()->getPost() );
	$filename = $this->_model->downloadMultiplos();

	if ( !empty( $filename ) ) {

	    header( 'Content-Description: File Transfer' );
	    header( 'Content-Type: application/octet-stream' );
	    header( 'Content-Disposition: attachment; filename=' . $filename['name'] );
	    header( 'Content-Transfer-Encoding: binary' );
	    header( 'Expires: 0' );
	    header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	    header( 'Pragma: public' );
	    header( 'Content-Length: ' . filesize( $filename['path'] ) );

	    $fp = fopen( $filename['path'], 'r' );

	    while ( !feof( $fp ) ) {
		echo fread( $fp, filesize( $filename['path'] ) );
		flush();
	    }

	    fclose( $fp );

	    unlink( $filename['path'] );
	    exit;
	}

	$this->_helper->redirector->goToSimple( 'index' );
    }

    /**
     * 
     */
    public function categoriasAction()
    {
	$this->_helper->layout()->disableLayout();

	// Busca categorias Cadastradas
	$modelCategorias = new Model_Categoria();
	$this->view->categorias = $modelCategorias->fetchAll();
    }

    /**
     * 
     */
    public function tagsAction()
    {
	$this->_helper->layout()->disableLayout();

	// Busca tags Cadastradas
	$modelTags = new Model_Tag();
	$this->view->tags = $modelTags->fetchAll();

	// Instancia form de Tag
	$formTag = new Default_Form_Tag();
	$formTag->setAction( $this->_helper->url( 'save', 'tag' ) );

	$this->view->form = $formTag;
    }

    public function propertyAction()
    {
	$this->_helper->layout()->disableLayout();

	$id = $this->_getParam( 'id' );

	$this->view->row = $this->_model->fetchRow( $id );
    }
 
    /**
     * 
     */
    public function editPostHook()
    {	
	$this->view->categorias = $this->_model->listCategoriasArray( $this->_getParam( 'id' ) );
	$this->view->tags = $this->_mapper->listTags( $this->_getParam( 'id' ) );
    }

    /**
     * Exibe imagem
     */
    public function imageAction()
    {
	$this->_helper->viewRenderer->setNoRender();
	$this->_helper->layout->disableLayout();

	$id = $this->_getParam( 'id' );

	$row = $this->_model->fetchRow( $id );

	$url = APPLICATION_PATH . '/../files/' . $row->path . '.' . $row->extensao;

	$phpThumb = PhpThumb_PhpThumbFactory::create( $url );

	$phpThumb->adaptiveResize( 550, 350 );
	$phpThumb->show();
    }
    
    /**
     * 
     */
    public function deleteAction()
    {
	$retorno = $this->_model->delete( $this->_getParam('id') );
	$this->_helper->json( $retorno );
    }
}