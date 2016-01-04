<?php

/** 
 * 
 */
class Admin_Form_Usuario extends Zend_Form
{
	/**
	 * (non-PHPdoc)
	 * @see Zend_Form::init()
	 */
	public function init ()
	{
		$elements = array();
		$decorator = array( 'ViewHelper' );
		
		$elements[] = $this->createElement('hidden', 'id')
				   ->setDecorators( $decorator );
		
		$elements[] = $this->createElement('hidden', 'limite_id')
				   ->setDecorators( $decorator );
		
		$elements[] = $this->createElement('text', 'nome')
			->setDecorators( $decorator )
			->addFilter('StringTrim')
			->setAttrib('class', 'full-width')
			->setAttrib('maxlength', '100')
			->setRequired(true);
			
		$elements[] = $this->createElement('text', 'email')
			->setDecorators( $decorator )
			->addFilter('StringTrim')
			->addValidator( 'EmailAddress' )
			->setAttrib('class', 'full-width')
			->setAttrib('maxlength', '100')
			->setRequired(true);
		
		$passwordConfirmation = new App_Validate_PasswordConfirm();
		
		$elements[] = $this->createElement('password', 'senha')
			->setDecorators( $decorator )
			->setAttrib('maxlength', 50)
			->addValidator('StringLength', false, array(6, 20) )
			->setAttrib('style', 'width: 170px')
			->addValidator( $passwordConfirmation )
			->setRequired(true);
		
		$elements[] = $this->createElement('password', 'confirma_senha')
			->setDecorators( $decorator )
			->setAttrib('maxlength', 50)
			->setAttrib('style', 'width: 170px')
			->setRequired( true );
		
		 $elements[] = $this->createElement('checkbox', 'liberado')
				    ->setAttrib( 'class', 'switch' )
				    ->setValue(1)
				    ->setDecorators( $decorator );
	
		$optNivel['U'] = 'Usuário';
		$optNivel['T'] = 'Temporário';
		$optNivel['A'] = 'Administrativo';
		
		$elements[] = $this->createElement('select', 'nivel')
				    ->setDecorators( $decorator )
				    ->addMultiOptions( $optNivel )
				    ->setRequired( true );
			
		$optCidade = array();
		
		$perfilMapper = new Model_Mapper_Perfil();
		$data = $perfilMapper->listPerfis();
		
		$optPerfil[''] = '';
		foreach ( $data as $row )
		    $optPerfil[$row->id] = $row->nome;
		
		$elements[] = $this->createElement('select', 'perfil_id')
			->setDecorators( $decorator )
			->addMultiOptions( $optPerfil )
			->setAttrib('class', 'full-width')
			->setRequired( true );
			
		$this->addElements( $elements );
	}
	
	/**
	 *
	 * @param array $data
	 * @return bool 
	 */
	public function isValid( $data )
	{
	    if ( !empty( $data['id'] ) ) {
		
		$this->getElement('senha')->setRequired( false );
		$this->getElement('confirma_senha')->setRequired( false );
	    }
	    
	    return parent::isValid( $data );
	}
}