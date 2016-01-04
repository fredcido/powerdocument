<?php

/**
 * 
 */
class App_View_Helper_Geral extends Zend_View_Helper_Abstract
{

    public function geral()
    {
	return $this;
    }

    /**
     *
     * @param string $nivel
     * @return string
     */
    public function descNivel( $nivel )
    {
	switch ( $nivel ) {
	    case 'T':
		return 'Tempor&aacute;rio';
		break;
	    case 'U':
		return 'Usu&aacute;rio';
		break;
	    case 'A':
		return 'Administrativo';
		break;
	    default:
		return 'N&atilde;o definido';
	}
    }
    
    public function detalhaOcorrencia( $ocorrencia ) 
    {
	switch ( $ocorrencia ) {
	    case 'I':
		return 'Inativo';
		break;
	    case 'E':
		return 'Dados incorretos';
		break;
	    case 'S':
		return 'Sucesso';
		break;
	    default:
		return 'N&atilde;o definido';
	}
    }
    
    public function detalhaAcaoLog( $acao ) 
    {
	switch ( $acao ) {
	    case 'U':
		return 'Upload';
		break;
	    case 'D':
		return 'Download';
		break;
	    case 'E':
		return 'Exclus&atilde;o';
		break;
	    case 'T':
		return 'Edi&ccedil;&atilde;o';
		break;
	    case 'R':
		return 'Restaurado';
		break;
	    default:
		return 'N&atilde;o definido';
	}
    }

}