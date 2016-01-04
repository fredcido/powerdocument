<?php

/** 
 * 
 */
class Admin_Form_Ip extends Zend_Form
{
	/**
	 * 
	 * (non-PHPdoc)
	 * @see Zend_Form::init()
	 */
	public function init ()
	{
		$elements 	= array();
		$decorator 	= array( 'ViewHelper' );
		
		$elements[] = $this->createElement('hidden', 'id');
		
		$elements[] = $this->createElement('text', 'ip')
			->setDecorators( $decorator )
			->addFilter('StringTrim')
			->addValidator('Ip')
			->setRequired(true);
			
		$elements[] = $this->createElement('text', 'descricao')
			->setDecorators( $decorator )
			->addFilter('StringTrim')
			->setAttrib('class', 'full-width')
			->setRequired(true);
			
		$this->addElements( $elements );
	}
}