<?php

/** 
 * 
 */
class App_View_Helper_ShowIconDocument extends Zend_View_Helper_Abstract
{
	/**
	 * 
	 * @var array
	 */
	protected $_class = array(
		'doc' 	=> 'document-word',
		'docx' 	=> 'document-word',
		'odt' 	=> 'document-word',
		'rtf' 	=> 'document-word',
	
		'txt' 	=> 'document-text',
            
                'xls'   => 'document-excel',
                'xlsx'  => 'document-excel',
            
                'mp3'   => 'document-music',
                'wma'   => 'document-music',
                'aac'   => 'document-music',
                'ogg'   => 'document-music',
                'ac3'   => 'document-music',
                'wav'   => 'document-music',
	
		'ppt' 	=> 'document-powerpoint',
		'pptx' 	=> 'document-powerpoint',
		'pps' 	=> 'document-powerpoint',

		'jpg' 	=> 'document-image',
		'jpeg' 	=> 'document-image',
		'gif' 	=> 'document-image',
		'png' 	=> 'document-image',
		'bmp' 	=> 'document-image',
	
		'pdf' 	=> 'document-pdf',
	    
		'rar' 	=> 'document-zip',
		'zip' 	=> 'document-zip'
	);
	
	/**
	 * 
	 * @var array
	 */
	protected $_icons = array(
		'doc' 	=> 'word.png',
		'docx' 	=> 'word.png',
		'odt' 	=> 'word.png',
		'rtf' 	=> 'word.png',
            
                'xls'   => 'excel.png',
                'xlsx'  => 'excel.png',
	
		'txt' 	=> 'text.png',
	
		'ppt' 	=> 'powerpoint.png',
		'pptx' 	=> 'powerpoint.png',
		'pps' 	=> 'powerpoint.png',
	
		'jpg' 	=> 'jpg.png',
		'jpeg' 	=> 'jpg.png',
		'gif' 	=> 'gif.png',
		'png' 	=> 'png.png',
		'bmp' 	=> 'bmp.png',
            
                'mp3'   => 'aac.png',
                'wma'   => 'aac.png',
                'aac'   => 'aac.png',
                'ogg'   => 'aac.png',
                'ac3'   => 'aac.png',
                'wav'   => 'aac.png',
	
		'pdf' 	=> 'pdf.png', 	
		
		'rar' 	=> 'jar.png', 	
		'zip' 	=> 'jar.png' 	
	);
	
	/**
	 * 
	 * @access public
	 * @return App_View_Helper_ShowIconDocument
	 */
	public function showIconDocument ()
	{
		return $this;
	}
	
	/**
	 * 
	 * @access 	public
	 * @param 	string $extension
	 * @return 	string
	 */
	public function css ( $extension )
	{
		return in_array( $extension, array_keys($this->_class) ) ? $this->_class[$extension] : 'document';
	}
		
	/**
	 * 
	 * @access 	public
	 * @param 	string $extension
	 * @return 	string
	 */
	public function ico ( $extension )
	{
		return (in_array( $extension, array_keys($this->_icons) ) ? $this->_icons[$extension] : 'default.png');
	}
}