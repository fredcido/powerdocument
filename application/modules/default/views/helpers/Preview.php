<?php

/**
 *  
 */
class Zend_View_Helper_Preview extends Zend_View_Helper_Abstract
{

    /**
     * 
     * @var array
     */
    protected $_allowedGoogle = array(
	'doc',
	'docx',
	'xls',
	'xlsx',
	'ppt',
	'pptx',
	'pdf',
	'pages',
	'ai',
	'psd',
	'tiff',
	'dxf',
	'svg',
	'eps',
	'ps',
	'ttf',
	'xps'
	    //'zip',
	    //'rar'
    );

    /**
     * 
     * @var array
     */
    protected $_allowedDefault = array(
	'jpg',
	'jpeg',
	'png',
	'gif'
    );

    /**
     * 
     * @access public
     * @return Zend_View_Helper_Preview
     */
    public function preview()
    {
	return $this;
    }

    /**
     * 
     * @param 	string $extension
     * @return 	bool
     */
    public function isValid( $extension )
    {
	return ( $this->isValidGoogle( $extension ) || $this->isValidDefault( $extension ) );
    }

    /**
     * 
     * @param 	string $extension
     * @return 	bool
     */
    public function isValidGoogle( $extension )
    {
	return in_array( $extension, $this->_allowedGoogle );
    }

    /**
     * 
     * @param 	string $extension
     * @return 	bool
     */
    public function isValidDefault( $extension )
    {
	return in_array( $extension, $this->_allowedDefault );
    }

    /**
     * 
     * Enter description here ...
     * @param unknown_type $filename
     * @param unknown_type $extension
     */
    public function view( $row )
    {
	$dom = new DOMDocument();

	switch ( true ) {

	    case $this->isValidGoogle( $row->extensao ):

		$iframe = $dom->createElement( 'iframe' );
		$iframe = $dom->appendChild( $iframe );

		$modelArquivo = new Model_Arquivo();

		$params = array(
		    'hash' => $modelArquivo->getHashView( $row )
		);

		$url = 'http://' . $_SERVER['SERVER_NAME'] . $this->view->path( 'view-file', 'auth', 'default', $params );

		$src = 'http://docs.google.com/viewer?embedded=true&url=' . $url . '/custom.' . $row->extensao;

		$style = 'width:550px; height:350px; display:block';

		$iframe->setAttribute( 'id', 'iGoogle' );
		$iframe->setAttribute( 'src', $src );
		$iframe->setAttribute( 'style', $style );
		$iframe->setAttribute( 'frameborder', 0 );

		break;

	    case $this->isValidDefault( $row->extensao ):

		$img = $dom->createElement( 'img' );
		$img = $dom->appendChild( $img );

		$param = array( 'id' => $row->id, 'hash' => md5( uniqid( time() ) ) );

		$img->setAttribute( 'src', $this->view->path( 'image', null, null, $param ) );

		break;
	}

	return $dom->saveHTML();
    }

}