<?php

/** 
 * 
 */
class Admin_Form_Limite extends Zend_Form
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
		
		$elements[] = $this->createElement('hidden', 'id')
				    ->setDecorators( $decorator );
		
		$elements[] = $this->createElement('text', 'maximo')
			->setDecorators( $decorator )
			->addFilter('StringTrim')
			->addValidator('Digits')
			->setRequired(true);
			
		$elements[] = $this->createElement('select', 'unidade_limite')
			->setDecorators( $decorator )
			->addMultiOptions(
				array(
					'K' => 'Kilo Bytes',
					'M' => 'Mega Bytes',
					'G' => 'Giga Bytes'
				)
			)
			->setValue('M')
			->setRequired(true);
			
		$elements[] = $this->createElement('text', 'periodo')
			->setDecorators( $decorator )
			->addFilter('StringTrim')
			->addValidator('Digits')
			->setRequired(true);
			
		$elements[] = $this->createElement('select', 'unidade_periodo')
			->setDecorators( $decorator )
			->addMultiOptions(
				array(
					'D' => 'Dias',
					'S' => 'Semanas',
					'M' => 'Meses'
				)
			)
			->setValue('M')
			->setRequired(true);
			
		$elements[] = $this->createElement('text', 'quantidade')
			->setDecorators( $decorator )
			->addFilter('StringTrim')
			->addValidator('Digits')
			->setRequired(true);
			
		$this->addElements( $elements );
	}
}
