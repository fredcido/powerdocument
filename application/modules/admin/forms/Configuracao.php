<?php

/** 
 * 
 */
class Admin_Form_Configuracao extends Zend_Form
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
		
		$elements[] = $this->createElement('hidden', 'limite_id')
				    ->setDecorators( $decorator );
		
		$elements[] = $this->createElement('text', 'dias_temporario')
			->setDecorators( $decorator )
			->addFilter('StringTrim')
			->addFilter('Digits')
			->setValue('7')
			->setRequired(true);

		$elements[] = $this->createElement('select', 'acao_limite')
			->setDecorators( $decorator )
			->addMultiOptions(
				array(
					'B' => 'Bloquear e enviar email',
					'E' => 'Enviar email'
				)
			)
			->setValue('E')
			->setRequired(true);
			
		$elements[] = $this->createElement('text', 'email_admin')
			->setDecorators( $decorator )
			->addFilter('StringTrim')
			->addValidator('EmailAddress')
			->setAttrib('class', 'full-width')
			->setRequired(true);
			
		$elements[] = $this->createElement('text', 'upload')
			->setDecorators( $decorator )
			->addFilter('StringTrim')
			->addFilter('Digits')
			->setValue('10')
			->setRequired(true);
			
		$elements[] = $this->createElement('select', 'medida_upload')
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
			
		$elements[] = $this->createElement('select', 'regra_ip')
			->setDecorators( $decorator )
			->addMultiOptions(
				array(
					'L' => 'Liberar',
					'B' => 'Bloquear'
				)
			)
			->setValue('L')
			->setRequired(true);
			
		$this->addElements( $elements );
	}
}