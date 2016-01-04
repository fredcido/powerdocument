<?php

/**
 *
 */
class Model_Contato extends App_Model_Abstract
{

    /**
     * 
     * @access 	public
     * @return 	boolean
     */
    public function save()
    {
	try {
	    
	    $layoutPath = APPLICATION_PATH . '/modules/admin/views/scripts/email/';

	    $html = new Zend_View();
	    $html->setScriptPath( $layoutPath );
	    $html->addHelperPath( 'App/View/Helpers/', 'App_View_Helper' );
	    
	    foreach ( $this->_data as $key => $value )
		$html->assign( $key, $value );
	    
	    $mail = new Zend_Mail( 'utf-8' );
	    
	     // Busca configuracoes do sistema
	    $configModel = new Model_Configuracao();
	    $configuracao = $configModel->fetchRow();

	    $mail->setBodyHtml( $html->render( 'contato.phtml' ) );
	    $mail->setFrom( $this->_config->email->address, $this->_config->geral->title );
	    $mail->addTo( $configuracao->email_admin );
	    $mail->setSubject( 'Contato - ' . $this->_config->geral->title );

	    $mail->send();
	    return true;
	    
	} catch ( Exception $e ) {

	    $this->_message->addMessage( $this->_config->messages->error, App_Message::ERROR );
	    return false;
	}
    }

}