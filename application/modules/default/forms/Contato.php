<?php

/**
 * 
 */
class Default_Form_Contato extends Zend_Form
{

    /**
     * (non-PHPdoc)
     * @see Zend_Form::init()
     */
    public function init()
    {
	$elements = array();
	$decorator 	= array( 'ViewHelper' );

	$elements[] = $this->createElement( 'text', 'titulo' )
			    ->setDecorators( $decorator )
			    ->setAttrib('class', 'full-width' )
			    ->setRequired( true );
	
	$elements[] = $this->createElement( 'text', 'nome' )
			    ->setDecorators( $decorator )
			    ->setAttrib('class', 'full-width' )
			    ->setAttrib('readonly', true )
			    ->setRequired( true );
	
	$elements[] = $this->createElement( 'text', 'email' )
			    ->setDecorators( $decorator )
			    ->setAttrib('class', 'full-width' )
			    ->setAttrib('readonly', true )
			    ->addValidator( 'EmailAddress' )
			    ->setRequired( true );
	
	$elements[] = $this->createElement( 'textarea', 'conteudo' )
			    ->setDecorators( $decorator )
			    ->setAttrib( 'class', 'full-width' )
			    ->setAttrib( 'rows', 5 )
			    ->setRequired( true );

	$this->addElements( $elements );
    }

}