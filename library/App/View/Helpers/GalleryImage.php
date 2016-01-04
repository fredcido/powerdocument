<?php

/**
 * 
 */
class App_View_Helper_GalleryImage extends Zend_View_Helper_Abstract
{
	/**
	 * @var unknown_type
	 */
	protected $_dom;

	/**
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * @access 	public 
	 * @param 	array 	$data
	 * @param 	string 	$title
	 * @param 	array 	$style
	 * @return 	object 	App_View_Helper_GalleryImage
	 */
	public function galleryImage ( $data, $title = null, array $style = null )
	{
		$this->_data = $data;
		
		$this->_dom = new DOMDocument();
		
		//product_gallery
		$product_gallery = $this->_dom->createElement( 'div' );
		$product_gallery = $this->_dom->appendChild( $product_gallery );
		$product_gallery->setAttribute( 'id', 'product_gallery' );
		
		if ( !is_null($style) )
			$this->setStyle( $product_gallery, $style );
			
		if ( !is_null($title) )
			$this->setTitle( $product_gallery, 'h3', $title );
			
		//modules
		$modules = $this->_dom->createElement( 'div' );
		$modules = $product_gallery->appendChild( $modules );
		$modules->setAttribute( 'class', 'modules' );
		
		//module
		$module = $this->_dom->createElement( 'div' );
		$module = $modules->appendChild( $module );
		$module->setAttribute( 'class', 'module' );
		
		//module_top
		$module_top = $this->_dom->createElement( 'div' );
		$module_top = $module->appendChild( $module_top );
		$module_top->setAttribute( 'class', 'module_top' );
		
		$this->setSubTitle( $this->setTitle( $module_top, 'h5', $this->getCount() ) );
		
		//module_bottom
		$module_bottom = $this->_dom->createElement( 'div' );
		$module_bottom = $module->appendChild( $module_bottom );
		$module_bottom->setAttribute( 'class', 'module_bottom' );
		
		//gallery
		$gallery = $this->_dom->createElement( 'div' );
		$gallery = $module_bottom->appendChild( $gallery );
		$gallery->setAttribute( 'class', 'gallery' );
		
		//gallery_inner
		$gallery_inner = $this->_dom->createElement( 'div' );
		$gallery_inner = $gallery->appendChild( $gallery_inner );
		$gallery_inner->setAttribute( 'class', 'gallery_inner' );
		
		$this->populateGallery( $gallery_inner );
			
		return $this;
	}
	
	/**
	 * @access 	protected
	 * @param 	DOMElement 	$element
	 * @param 	array 		$style
	 * @return 	void
	 */
	protected function setStyle ( DOMElement $element, array $style )
	{
		$css = '';
		
		foreach ( $style as $key => $value ) 
			$css .= $key . ':' . $value . ';';
		
		$element->setAttribute( 'style', $css );
	}
	
	/**
	 * @access 	protected 
	 * @param 	DOMElement 	$element
	 * @param 	string 		$tag
	 * @param 	string 		$title
	 * @return 	void
	 */
	protected function setTitle ( DOMElement $element, $tag, $title )
	{
		$h = $this->_dom->createElement( $tag );
		$h = $element->appendChild( $h );
		
		$text = $this->_dom->createTextNode( $title );
		$text = $h->appendChild( $text );
		
		return $h;
	}
	
	/**
	 * 
	 * @access 	protected
	 * @param 	DOMElement $element
	 * @return 	void
	 */
	protected function setSubTitle ( DOMElement $element )
	{
		$span = $this->_dom->createElement( 'span' );
		$span = $element->appendChild( $span );
		
		$string = ' | ' . $this->getSize();
		
		$text = $this->_dom->createTextNode( $string );
		$text = $span->appendChild( $text );
	}
	
	/**
	 * 
	 * @access 	protected
	 * @param 	string 		$label
	 * @return 	string
	 */
	protected function getCount ( $label = 'foto' )
	{
		$count = count( $this->_data );
		
		if ( !empty( $count ) ) {
		
			$text = $count . ' ' . $label;
		
			if ( $count > 1 ) $text .= 's';
			
		} else $text = 'nenhuma foto';
			
		return $text;
	}
	
	/**
	 * @access 	protected
	 * @return 	mixed
	 */
	protected function getSize ()
	{
		$size = 0;
		
		foreach ( $this->_data as $value )
			$size += $value['size'];

		if ( !empty($size) ) {
			
			$size = $this->view->bitCalculator( $size );
		
			return $size;
		}
	}
	
	/**
	 * 
	 * @access 	protected
	 * @param 	DOMElement $element
	 * @return 	void
	 */
	protected function populateGallery ( DOMElement $element )
	{
		foreach ( $this->_data as $value ) {
			
			$dl = $this->_dom->createElement( 'dl' );
			$dl = $element->appendChild( $dl );
			$dl->setAttribute( 'class', 'product' );
			
			$dt = $this->_dom->createElement( 'dt' );
			$dt = $dl->appendChild( $dt );
			
			$strong = $this->_dom->createElement( 'strong' );
			$strong = $dt->appendChild( $strong );
			
			if ( !empty($value['mimetype']) ) {
				$text = $this->_dom->createTextNode( $value['mimetype'] );
				$text = $strong->appendChild( $text );
			}
			
			$a = $this->_dom->createElement( 'a' );
			$a = $dt->appendChild( $a );
			$a->setAttribute( 'href', 'javascript:;' );
			
			$img = $this->_dom->createElement( 'img' );
			$img = $a->appendChild( $img );
			$img->setAttribute( 'width', '97' );
			$img->setAttribute( 'height', '82' );
			$img->setAttribute( 'alt', '' );
			$img->setAttribute( 'src', $value['image'] );
			
			$dd = $this->_dom->createElement( 'dd' );
			$dd = $dl->appendChild( $dd );
			
			$em = $this->_dom->createElement( 'em' );
			$em = $dd->appendChild( $em );
			
			$text = $this->_dom->createTextNode( $this->view->bitCalculator($value['size']) );
			$text = $em->appendChild( $text );
			
			if ( !empty($value['action']) ) {
			
				$ul = $this->_dom->createElement( 'ul' );
				$ul = $dd->appendChild( $ul );
				
				$li = $this->_dom->createElement( 'li' );
				$li = $ul->appendChild( $li );
				
				//edit
				if ( !empty($value['action']['edit']) ) {
					
					$a = $this->_dom->createElement( 'a' );
					$a = $li->appendChild( $a );
					$a->setAttribute( 'class', 'edit_product' );
					$a->setAttribute( 'href', $value['action']['edit'] );
					
					$text = $this->_dom->createTextNode( 'Edit' );
					$text = $a->appendChild( $text );
					
				}
				
				//delete
				if ( !empty($value['action']['delete']) ) {
					
					$a = $this->_dom->createElement( 'a' );
					$a = $li->appendChild( $a );
					$a->setAttribute( 'class', 'delete_product' );
					$a->setAttribute( 'href', $value['action']['delete'] );
					
					$text = $this->_dom->createTextNode( 'Delete' );
					$text = $a->appendChild( $text );
					
				}
			}
			
		}
	}
	
	/**
	 * @access 	public
	 * @return 	string
	 */
	public function __toString ()
	{
		return $this->_dom->saveHTML();
	}
}

?>