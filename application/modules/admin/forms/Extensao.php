<?php

/** 
 * 
 */
class Admin_Form_Extensao extends Zend_Form
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

	    $elements[] = $this->createElement('text', 'descricao')
			    ->setDecorators( $decorator )
			    ->setAttrib('maxlength', 10)
			    ->setAttrib('style', 'width:400px')
			    ->setFilters( array( 'StringTrim', 'StringToLower' ) )
			    ->addValidator( 'Regex', true, array( '/^[a-z0-9]{3,5}$/i' ) )
			    ->setRequired(true);
	    
	    $elements[] = $this->createElement('checkbox', 'liberado')
			->setAttrib( 'class', 'switch' )
			->setValue(1)
			->setDecorators( $decorator );

	    $this->addElements( $elements );
	}
}