<?php

/**
 * 
 */
class Default_Form_Arquivo extends Zend_Form
{

    /**
     * (non-PHPdoc)
     * @see Zend_Form::init()
     */
    public function init()
    {
	$elements = array( );
	$decorator = array( 'ViewHelper' );

	$elements[] = $this->createElement( 'hidden', 'id' )
		->setDecorators( $decorator );

	$elements[] = $this->createElement( 'hidden', 'path' )
		->setDecorators( $decorator );

	$elements[] = $this->createElement( 'hidden', 'loading_hash' )
		->setValue( md5( uniqid( time() ) ) )
		->setDecorators( $decorator );

	$elements[] = $this->createElement( 'hidden', 'categorias' )
		->setDecorators( $decorator )
		->setIsArray( true );

	$elements[] = $this->createElement( 'hidden', 'tags' )
		->setDecorators( $decorator )
		->setIsArray( true );

	$elements[] = $this->createElement( 'text', 'nome' )
		->setDecorators( $decorator )
		->setRequired( true )
		->setAttrib( 'class', 'full-width' );

	$elements[] = $this->createElement( 'text', 'title' )
		->setDecorators( $decorator )
		->setAttrib( 'class', 'full-width' );

	$elements[] = $this->createElement( 'textarea', 'descricao' )
		->setDecorators( $decorator )
		->setAttrib( 'class', 'full-width' )
		->setAttrib( 'rows', 5 );

	$elements[] = $this->createElement( 'textarea', 'description' )
		->setDecorators( $decorator )
		->setAttrib( 'class', 'full-width' )
		->setAttrib( 'rows', 5 );

	$elements[] = $this->createElement( 'textarea', 'keywords' )
		->setDecorators( $decorator )
		->setAttrib( 'class', 'full-width' )
		->setAttrib( 'rows', 5 );

	$mapperPerfil = new Model_Mapper_Perfil();
	$data = $mapperPerfil->listPerfisWithUsers();

	$optionsPerfil = array();
	foreach ( $data as $row )
	    $optionsPerfil[$row->id] = $row->nome;

	$elements[] = $this->createElement( 'multiCheckbox', 'perfil' )
		->setDecorators(
			array(
			    'ViewHelper',
			    array( 'HtmlTag', array( 'tag' => 'li' ) )
			)
		)
		->setSeparator( '</li><li>' )
		->addMultiOptions( $optionsPerfil )
		->setRequired( false );

	$modelConfig = new Model_Configuracao();
	$row = $modelConfig->fetchRow();

	$modelExtensao = new Model_Extensao();
	$extensoes = $modelExtensao->listArray();

	$elements[] = $this->createElement( 'file', 'arquivo' )
		->addValidators(
			array(
			    array( 'Count', false, 1 ),
			    array( 'Size', false, App_Util_ByteSize::convert( $row->upload, $row->medida_upload, 'B' ) ),
			    array( 'Extension', false, implode( ',', $extensoes ) )
			)
		)
		->setValueDisabled( true )
		->setMaxFileSize( App_Util_ByteSize::convert( $row->upload, $row->medida_upload, 'B' ) )
		->setDecorators( array( 'File' ) )
		->setAttrib( 'onChange', "showFileSelected(this.value, 'desc-file');" )
		->setAttrib( 'class', 'input-file' )
		->setAttrib( 'size', 42 );

	$this->addElements( $elements );
    }

}