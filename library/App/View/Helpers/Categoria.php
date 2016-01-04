<?php

/**
 * 
 */
class App_View_Helper_Categoria extends Zend_View_Helper_Abstract
{

    /**
     * @var DOMDocument
     */
    protected $_dom;
    
    /**
     *
     * @var array
     */
    protected $_data;

    /**
     * @var array
     */
    protected $_categorias = array();

    /**
     *
     * @var string
     */
    protected $_element = 'radio';
    
    /**
     *
     * @var string
     */
    protected $_name = 'categorias';
    
    /**
     *
     * @var array
     */
    protected $_elements = array( 'radio', 'checkbox' );
    
    /**
     *
     * @var bool
     */
    protected $_checkChildren = true;
    
    /**
     *
     * @var array
     */
    protected $_checkValues = array();
    
    /**
     *
     * @var bool
     */
    protected $_stopHierarchy = false;

    /**
     *
     * @return App_View_Helper_Categoria 
     */
    public function categoria()
    {
	$this->_getTreeCategorias();

	return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function renderToReorder()
    {
	$this->_data = $this->_getTreeCategorias();
	
	$this->_dom = new DOMDocument();
	
	$ulMain = $this->_dom->createElement('UL');
        $ulMain->setAttribute('id', 'categorias-list');
	
	foreach ( $this->_data as $row )
	    $ulMain->appendChild ( $this->_createLiReorder( $row ) );
        
        $this->_dom->appendChild( $ulMain );
	
	return $this->_dom->saveHTML();
    }
    
    /**
     *
     * @param array $data
     * @return DOMElement 
     */
    protected function _createLiReorder( $data )
    {   
	$li = $this->_dom->createElement('LI');
	$li->appendChild( $this->_dom->createTextNode( $data['nome'] ) );
	$li->setAttribute( 'id', $data['id'] );
	$li->setAttribute( 'class', 'jstree-open' );
        
	if ( array_key_exists( 'children', $data ) && count( $data['children'] ) > 0 ) {
	    
	    $ul = $this->_dom->createElement('UL');
            
	    foreach ( $data['children'] as $row )
                $ul->appendChild( $this->_createLiReorder( $row ) );
	    
	    $li->appendChild( $ul );
	    
	}
	
	return $li;
    }
    
    /**
     *
     * @param string
     * @return App_View_Helper_Categoria 
     */
    public function setElement( $element )
    {
	if ( !in_array( $element, $this->_elements ) )
	    throw new Exception( 'Element ' . $element . ' not accepted.');
	
	$this->_element = $element;
	
	return $this;
    }
    
    /**
     *
     * @param Array $values
     * @return App_View_Helper_Categoria 
     */
    public function setValues( $values )
    {
	$this->_checkValues = (array)$values;
	
	return $this;
    }
    
    /**
     *
     * @param string $name 
     */
    public function setName( $name )
    {
        $this->_name = $name;
        
        return $this;
    }
    
    /**
     *
     * @param bool $flag 
     */
    public function setCheckChildren( $flag )
    {
	$this->_checkChildren = (bool)$flag;
        
        return $this;
    }
    
    /**
     *
     * @param int $flagId
     * @return App_View_Helper_Categoria 
     */
    public function setStopHierarchy( $flagId )
    {
        $this->_stopHierarchy = $flagId;
        
        return $this;
    }

    /**
     * @access 	public
     * @return 	string
     */
    public function __toString()
    {
	return $this->render();
    }
    
    /**
     *
     * @return string
     */
    public function render()
    {
	$this->_dom = new DOMDocument();
	
	$this->_data = $this->_getTreeCategorias();
                	
	$this->_createHtml();
        
	return $this->_dom->saveHTML();
    }
    
    /**
     * 
     */
    protected function _createHtml()
    {
	$ulMain = $this->_dom->createElement('UL');
	$ulMain->setAttribute('class', 'collapsible-list with-bg' );
        $ulMain->setAttribute('id', 'categorias-list');
	
	foreach ( $this->_data as $row )
                $ulMain->appendChild ( $this->_createLiElement( $row, ( !empty( $this->_stopHierarchy ) && $row['id'] == $this->_stopHierarchy ) ) );
        
        $this->_dom->appendChild( $ulMain );
    }
    
    /**
     *
     * @param array $data
     * @return DOMElement 
     */
    protected function _createLiElement( $data, $disabled = false )
    {   
        if ( !empty( $this->_stopHierarchy ) && $data['id'] == $this->_stopHierarchy )
            $disabled = true;
        
	$li = $this->_dom->createElement('LI');
        
	$span = $this->_dom->createElement('SPAN');
	$span->appendChild( $this->_createInputElement( $data, $disabled ) );
        
	if ( array_key_exists( 'children', $data ) && count( $data['children'] ) > 0 ) {
	    
	    $b = $this->_dom->createElement('B');
	    $b->setAttribute('class', 'toggle');
	    
	    $li->setAttribute('class', 'closed' );
	    $li->appendChild( $b );
	    $li->appendChild( $span );
	    
	    $ul = $this->_dom->createElement('UL');
            
	    foreach ( $data['children'] as $row ) {
                $ul->appendChild( $this->_createLiElement( $row, $disabled ) );
            }
	    
	    $li->appendChild( $ul );
	    
	} else {
	    
	    $li->setAttribute('class', 'with-icon' );
	    $li->appendChild( $span );
	}
	
	return $li;
    }
    
    /**
     *
     * @param array $data
     * @return DOMElement 
     */
    protected function _createInputElement( $data, $disabled )
    {
	$label = $this->_dom->createElement('LABEL');
	$label->setAttribute('for', $this->_name . '-' . $data['id'] );
	
	$input = $this->_dom->createElement('INPUT');
	$input->setAttribute('type', $this->_element );
	$input->setAttribute('name', $this->_name . ( 'checkbox' == $this->_element ? '[]' : '' ) );
	$input->setAttribute('id', $this->_name . '-' . $data['id'] );
	$input->setAttribute('value', $data['id'] );
        
        if ( $disabled )
            $input->setAttribute ( 'disabled', 'disabled' );
	
	if ( in_array( $data['id'], $this->_checkValues ) )
	    $input->setAttribute('checked', 'checked');
	
	if ( 'checkbox' == $this->_element && $this->_checkChildren )
	    $input->setAttribute('onClick', 'checkChildren(this);event.stopPropagation();');
        else
            $input->setAttribute('onClick', 'event.stopPropagation()');
	
	$label->appendChild( $input );
        
        $text = $this->_dom->createTextNode( ' ' . $data['nome'] );
        $label->appendChild( $text );
	
	return $label;
    }
    
    /**
     *
     * @return array
     */
    protected function _getTreeCategorias()
    {
	$modelCategoria = new Model_Categoria();
	return $modelCategoria->listTreeCategorias();
    }

}