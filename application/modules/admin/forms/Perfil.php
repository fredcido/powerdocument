<?php

/** 
 * 
 */
class Admin_Form_Perfil extends Zend_Form
{
	/**
	 * (non-PHPdoc)
	 * @see Zend_Form::init()
	 */
	public function init ()
	{
	    $elements = array();
	    $decorator 	= array( 'ViewHelper' );
	    		
	    $elements[] = $this->createElement('hidden', 'id')
				->setDecorators( $decorator );
			
	    $elements[] = $this->createElement('hidden', 'limite_id')
				->setDecorators( $decorator );
	    
	    $elements[] = $this->createElement('hidden', 'categorias')
				->setDecorators( $decorator )
                                ->setIsArray( true );

	    $elements[] = $this->createElement('text', 'nome')
			    ->setDecorators( $decorator )
			    ->setAttrib('maxlength', 70)
			    ->setAttrib('class', 'full-width')
			    ->setRequired(true);
	    
	    $elements[] = $this->createElement('textarea', 'descricao')
			->setDecorators( $decorator )
			->setRequired( false )
			->setAttrib('class', 'full-width')
			->setAttrib('rows', 5)
			->setAttrib('cols', 40);
	    
	    $mapperAcao = new Model_Mapper_Acao();
	    $data = $mapperAcao->listAll();
	    
	    $options = array();
	    foreach ( $data as $row )
		$options[$row->id] = $row->descricao;
	    
	    $elements[] = $this->createElement('multiCheckbox', 'acoes')
			->setDecorators( 
			    array( 
				'ViewHelper',
				array( 'HtmlTag', array( 'tag' => 'li' ) )
			    ) 
			)
			->setSeparator('</li><li>')
			->addMultiOptions( $options )
			->setRequired( false );
	    
	    $elements[] = $this->createElement('checkbox', 'liberado')
			->setAttrib( 'class', 'switch' )
			->setValue(1)
			->setDecorators( $decorator );

	    $this->addElements( $elements );
	}
}