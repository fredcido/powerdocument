<?php

/** 
 * 
 */
class Admin_Form_RelatorioAcesso extends Zend_Form
{
	/**
	 * (non-PHPdoc)
	 * @see Zend_Form::init()
	 */
	public function init ()
	{
	    $elements = array();
	    $decorator 	= array( 'ViewHelper' );
	    
	    $usuarioModel = new Model_Usuario();
	    $rows = $usuarioModel->fetchAll();
	    
	    $data[''] = '';
	    foreach ( $rows as $row )
		$data[$row->id] = $row->nome;
	    		
	    $elements[] = $this->createElement( 'select', 'usuario' )
			    ->setDecorators( $decorator )
			    ->setAttrib('class', 'full-width')
			    ->addMultiOptions( $data );
	    
	    $resultados[''] = '';
	    $resultados['I'] = 'Inativo';
	    $resultados['S'] = 'Sucesso';
	    $resultados['E'] = 'Erro';
	    
	    $elements[] = $this->createElement( 'select', 'resultado' )
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