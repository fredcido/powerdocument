<?php

/** 
 * 
 */
class Default_Form_Cliente extends Zend_Form
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

	    $elements[] = $this->createElement('text', 'nome')
			    ->setDecorators( $decorator )
			    ->setAttrib('maxlength', 250)
			    ->setAttrib('class', 'full-width')
			    ->setRequired(true);
	    
	    $elements[] = $this->createElement('text', 'cpf_cnpj')
			    ->setDecorators( $decorator )
			    ->setAttrib('maxlength', 30)
			    ->setAttrib('class', 'full-width');
	    
	    $elements[] = $this->createElement('text', 'email')
			    ->setDecorators( $decorator )
			    ->setAttrib('maxlength', 150)
			    ->addValidator( 'EmailAddress' )
			    ->setRequired( true )
			    ->setAttrib('class', 'full-width');
	    
	    $elements[] = $this->createElement ('text', 'telefone' )
			    ->setDecorators( $decorator )
			    ->setAttrib('maxlength', 50)
			    ->setRequired( true )
			    ->setAttrib('class', 'full-width');
	    
	    $elements[] = $this->createElement ('text', 'atividade' )
			    ->setDecorators( $decorator )
			    ->setAttrib('maxlength', 300)
			    ->setRequired( true )
			    ->setAttrib('class', 'full-width');
	    
	    $elements[] = $this->createElement ('text', 'endereco' )
			    ->setDecorators( $decorator )
			    ->setAttrib('maxlength', 300)
			    ->setRequired( true )
			    ->setAttrib('class', 'full-width');
	    
	    $optSexo[''] = '';
	    $optSexo['M'] = 'Masculino';
	    $optSexo['F'] = 'Feminino';
	    
	    $elements[] = $this->createElement( 'select', 'sexo' )
			    ->setDecorators( $decorator )
			    ->setAttrib('class', 'full-width')
			    ->setRequired( true )
			    ->addMultiOptions( $optSexo );
	    
	    $dbEstado = App_Model_DbTable_Factory::get( 'Estado' );
	    $rows = $dbEstado->fetchAll( array(), array( 'nome' ));
	    
	    $optUf[''] = '';
	    foreach ( $rows as $row )
		$optUf[$row->id] = $row->sigla . ' - ' . $row->nome;
	    		
	    $elements[] = $this->createElement( 'select', 'estado_id' )
			    ->setDecorators( $decorator )
			    ->setAttrib('class', 'full-width')
			    ->setAttrib('onchange', 'loadCombo( "/cliente/load-cidade/id/" + this.value, "#cidade_id" )')
			    ->setRequired( true )
			    ->addMultiOptions( $optUf );
	    
	    $elements[] = $this->createElement( 'select', 'cidade_id' )
			    ->setDecorators( $decorator )
			    ->setAttrib('class', 'full-width')
			    ->setRequired( true )
			    ->setRegisterInArrayValidator( false );
	    
	    $dbEstadoCivil = App_Model_DbTable_Factory::get( 'EstadoCivil' );
	    $rows = $dbEstadoCivil->fetchAll( array(), array( 'nome' ));
	    
	    $optEstadoCivil[''] = '';
	    foreach ( $rows as $row )
		$optEstadoCivil[$row->id] = $row->nome;
	    		
	    $elements[] = $this->createElement( 'select', 'estado_civil_id' )
			    ->setDecorators( $decorator )
			    ->setAttrib('class', 'full-width')
			    ->addMultiOptions( $optEstadoCivil );

	    $this->addElements( $elements );
	}
}