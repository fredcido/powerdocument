<?php

/**
 *
 */
class Admin_RelatorioController extends App_Controller_Padrao
{

    /**
     * @var Model_Relatorio
     */
    protected $_model;

    /**
     * @var Model_Mapper_Relatorio
     */
    protected $_mapper;

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	$this->_model = new Model_Relatorio();
	$this->_mapper = new Model_Mapper_Relatorio();
	
	$this->view->noToolBar = true;
    }

    /**
     * 
     */
    public function indexAction()
    {
	$this->view->form = new Admin_Form_RelatorioAcesso();
	$this->view->form->setAction( $this->_helper->url( 'acesso' ) );
    }
    
     /**
     * 
     */
    public function acessoAction()
    {
	if ( $this->getRequest()->isXmlHttpRequest() )
	    $this->_helper->layout()->disableLayout();
	
	$this->view->rows = $this->_mapper->relatorioAcesso( $this->_getAllParams() );
    }
    
    /**
     * 
     */
    public function acessoHtmlAction()
    {
	$this->_helper->layout()->disableLayout();
	$this->view->printHtml = true;
	$this->view->data = $this->_mapper->relatorioAcesso( $this->_getAllParams() );
    }
    
    /**
     * 
     */
    public function acessoPdfAction()
    {
	$this->_helper->layout()->disableLayout();
	$this->_helper->viewRenderer->setNoRender();
	
	$this->view->data = $this->_mapper->relatorioAcesso( $this->_getAllParams() );
	
	$pdf = new App_Util_DomPdf();
	$pdf->renderHtml( $this->view->render( 'relatorio/acesso-html.phtml') );
	$pdf->savePdf( 'Relatorio-acesso-' . Zend_Date::now()->toString('dd_MM_yyyy_HH_mm') . '.pdf' );
    }
    
    /**
     * 
     */
    public function arquivoHtmlAction()
    {
	$this->_helper->layout()->disableLayout();
	$this->view->printHtml = true;
	$this->view->data = $this->_mapper->relatorioArquivo( $this->_getAllParams() );
    }
    
    /**
     * 
     */
    public function arquivoPdfAction()
    {
	$this->_helper->layout()->disableLayout();
	$this->_helper->viewRenderer->setNoRender();
	
	$this->view->data = $this->_mapper->relatorioArquivo( $this->_getAllParams() );
	
	$pdf = new App_Util_DomPdf();
	$pdf->renderHtml( $this->view->render( 'relatorio/arquivo-html.phtml') );
	$pdf->savePdf( 'Relatorio-arquivo-' . Zend_Date::now() . '.pdf' );
    }
    
    /**
     * 
     */
    public function arquivoAction()
    {
	$this->view->form = new Admin_Form_RelatorioArquivo();
	$this->view->form->setAction( $this->_helper->url( 'arquivo-dados' ) );
    }
    
    
    /**
     * 
     */
    public function arquivoDadosAction()
    {
	if ( $this->getRequest()->isXmlHttpRequest() )
	    $this->_helper->layout()->disableLayout();
	
	$this->view->rows = $this->_mapper->relatorioArquivo( $this->_getAllParams() );
    }
    
    /**
     * 
     */
    public function graficoAction()
    {
    }
    
    /**
     * 
     */
    public function graficoAcoesAction()
    {
	$this->_helper->json( $this->_model->graficoAcoes() );
    }
    
    /**
     * 
     */
    public function graficoExtensoesAction()
    {
	$this->_helper->json( $this->_model->graficoExtensoes() );
    }
    
    /**
     * 
     */
    public function graficoCategoriasAction()
    {
	$this->_helper->json( $this->_model->graficoCategorias() );
    }
    
    /**
     * 
     */
    public function graficoPerfilAction()
    {
	$this->_helper->json( $this->_model->graficoPerfil() );
    }
    
    /**
     * 
     */
    public function graficoBaixadosAction()
    {
	$this->_helper->json( $this->_model->graficoBaixados() );
    }
}