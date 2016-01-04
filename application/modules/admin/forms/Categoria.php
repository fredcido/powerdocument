<?php

/** 
 * 
 */
class Admin_Form_Categoria extends Zend_Form
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
            
            $elements[] = $this->createElement('hidden', 'categoria_id')
				->setDecorators( $decorator );

	    $elements[] = $this->createElement('text', 'nome')
			    ->setDecorators( $decorator )
			    ->setAttrib('maxlength', 70)
			    ->setAttrib('class', 'full-width')
			    ->setRequired(true);
	    
	    $elements[] = $this->createElement('checkbox', 'liberado')
			->setAttrib( 'class', 'switch' )
			->setValue(1)
			->setDecorators( $decorator );

	    $this->addElements( $elements );
	}
}