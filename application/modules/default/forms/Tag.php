<?php

/**
 * 
 */
class Default_Form_Tag extends Zend_Form
{

    /**
     * (non-PHPdoc)
     * @see Zend_Form::init()
     */
    public function init()
    {
	$elements = array();
	$decorator 	= array( 'ViewHelper' );

	$elements[] = $this->createElement( 'hidden', 'id' )
			    ->setDecorators( $decorator );

	$elements[] = $this->createElement( 'text', 'titulo' )
		->setDecorators( $decorator )
		->setAttrib( 'style', 'width:400px' )
		->setRequired( true );

	$this->addElements( $elements );
    }

}