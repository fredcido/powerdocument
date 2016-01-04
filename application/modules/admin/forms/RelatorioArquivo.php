<?php

/** 
 * 
 */
class Admin_Form_RelatorioArquivo extends Zend_Form
{
	/**
	 * (non-PHPdoc)
	 * @see Zend_Form::init()
	 */
	public function init ()
	{
	    $elements = array();
	    $decorator 	= array( 'ViewHelper' );
	    
	    $categoriaModel = new Model_Categoria();
	    $rows = $categoriaModel->fetchAll();
	    
	    $data = array( '' =>  '' );
	    foreach ( $rows as $row )
		$data[$row->id] = $row->nome;
	    		
	    $elements[] = $this->createElement( 'select', 'categoria' )
			    ->setDecorators( $decorator )
			    ->setAttrib('class', 'full-width')
			    ->addMultiOptions( $data );
	    
	    $extensaoModel = new Model_Extensao();
	    $rows = $extensaoModel->fetchAll();
	    
	    $data = array( '' =>  '' );
	    foreach ( $rows as $row )
		$data[$row->id] = $row->descricao;
	    		
	    $elements[] = $this->createElement( 'select', 'extensao' )
			    ->setDecorators( $decorator )
			    ->setAttrib('class', 'full-width')
			    ->addMultiOptions( $data );
	    
	    $arquivoModel = new Model_Arquivo();
	    $rows = $arquivoModel->fetchAll();
	    
	    $data = array( '' =>  '' );
	    foreach ( $rows as $row )
		$data[$row->id] = $row->nome;
	    		
	    $elements[] = $this->createElement( 'select', 'arquivo' )
			    ->setDecorators( $decorator )
			    ->setAttrib('class', 'full-width')
			    ->addMultiOptions( $data );
	    
	    $usuarioModel = new Model_Usuario();
	    $rows = $usuarioModel->fetchAll();
	    
	    $data = array( '' =>  '' );
	    foreach ( $rows as $row )
		$data[$row->id] = $row->nome;
	    		
	    $elements[] = $this->createElement( 'select', 'usuario' )
			    ->setDecorators( $decorator )
			    ->setAttrib('class', 'full-width')
			    ->addMultiOptions( $data );
	    
	    $resultados[''] = '';
	    $resultados['D'] = 'Download';
	    $resultados['U'] = 'Upload';
	    $resultados['E'] = 'Exclusão';
	    $resultados['T'] = 'Edição';
	    $resultados['R'] = 'Restaurado';
	    
	    $elements[] = $this->createElement( 'select', 'acao' )
			    ->setDecorators( $decorator )
			    ->setAttrib('class', 'full-width')
			    ->addMultiOptions( $resultados );
	    
	    $elements[] = $this->createElement('text', 'data_inicial')
			->setDecorators( $decorator )
			->setAttrib('class', 'datamask');
	    
	    $elements[] = $this->createElement('text', 'data_final')
			->setDecorators( $decorator )
			->setAttrib('class', 'datamask');
	  
	    $this->addElements( $elements );
	}
}