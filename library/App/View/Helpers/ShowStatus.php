<?php

/**
 * 
 */
class App_View_Helper_ShowStatus extends Zend_View_Helper_Abstract
{
	/**
	 * 
	 * @access 	public
	 * @param 	int 	$status
	 * @param 	string 	$approved
	 * @param 	string 	$pending
	 * @return 	void
	 */
	public function showStatus ( $status, $approved = 'Aprovado', $pending = 'Pendente' )
	{
	    $dom = new DOMDocument();

	    $small = $dom->createElement( 'small' );
	    $small = $dom->appendChild( $small );
	    
	    $img = $dom->createElement( 'img' );
	    
	    $img->setAttribute( 'width', '16' );
	    $img->setAttribute( 'height', '16' );
	    $img->setAttribute( 'class', 'picto' );
	    
	    $view = Zend_Controller_Front::getInstance()->getParam( 'bootstrap' )->getResource( 'view' );

	    if ( $status ) {
	    	
	    	$file = $view->baseUrl('public/images/icons/fugue/status.png');
	    	
	    	$img->setAttribute( 'src', $file );
	    	
	    	$small->appendChild( $img );

			$text = $dom->createTextNode( $approved );
			$text = $small->appendChild( $text );

	    } else {

	    	$file = $view->baseUrl('public/images/icons/fugue/status-busy.png');
	    	
	    	$img->setAttribute( 'src', $file );
	    	
	    	$small->appendChild( $img );
	    	
			$text = $dom->createTextNode( $pending );
			$text = $small->appendChild( $text );

	    }

	    return $dom->saveHTML();
	}
}

?>