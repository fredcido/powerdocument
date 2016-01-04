<?php

class App_View_Helper_Image extends Zend_View_Helper_Abstract
{

    protected $_dom;
    protected $_treeContainer;

    public function image()
    {
	return $this;
    }

    /**
     *
     * @return DOMDocument
     */
    protected function _getDom()
    {
	if ( null == $this->_dom )
	    $this->_dom = new DOMDocument();

	return $this->_dom;
    }

    /**
     *
     * @return string
     */
    public function treeView()
    {
	$this->_setTreeViewGalleries();
	$this->_setTreeViewUsers();
	
	return $this->_getDom()->saveHTML();
    }

    /**
     *
     * @return DOMElement $container
     */
    protected function _getTreeContainer()
    {
	if ( null == $this->_treeContainer ) {

	    $dom = $this->_getDom();
	    
	    $this->_treeContainer = $dom->createElement( 'ul' );
	    $this->_treeContainer->setAttribute('id', 'tree-container');
	    
	    $dom->appendChild( $this->_treeContainer );
	}

	return $this->_treeContainer;
    }

    /**
     * 
     */
    protected function _setTreeViewGalleries()
    {
	$dom = $this->_getDom();
	$treeContainer = $this->_getTreeContainer();

	// CRIA LI DE GALERIA
	$liGallery = $dom->createElement( 'li' );
	$liGallery->setAttribute( 'class', 'closed' );

	// SPAN DE CONTROLE DA LI DA GALERIA
	$spanGallery = $dom->createElement( 'span' );
	$spanGallery->setAttribute( 'class', 'toggle' );

	// LINK DE CONTROLE DA GALERIA
	$aGallery = $dom->createElement( 'a' );
	$aGallery->setAttribute( 'class', 'folder' );
	$aGallery->setAttribute( 'href', 'javascript:;' );

	// SPAN LINK GALERIA
	$spanAGallery = $dom->createElement( 'span' );

	// TEXT GALERIA
	$textGallery = $dom->createTextNode( 'Galerias' );

	// Vincula nos filhos
	$spanAGallery->appendChild( $textGallery );
	$aGallery->appendChild( $spanAGallery );

	$liGallery->appendChild( $spanGallery );
	$liGallery->appendChild( $aGallery );
	
	$ulContainer = $dom->createElement('ul');
	$ulContainer->setAttribute('id', 'tree-container-gallery');
	
	// Busca galerias cadastradas
	$galleries = $this->_searchGalleries();
	
	foreach ( $galleries as $gallery ) {
	    
	    $li = $dom->createElement('li');
	    
	    $a = $dom->createElement('a');
	    $a->setAttribute('class', 'folder-image');
	    $a->setAttribute('href', 'javascript:searchImages( ' . $gallery->id . ', "' . $gallery->nome . '", "G");');
	    
	    $span = $dom->createElement('span');
	    $text = $dom->createTextNode( $gallery->nome );
	    
	    $span->appendChild( $text );
	    $a->appendChild( $span );
	    $li->appendChild( $a );
	    $ulContainer->appendChild( $li );
	}
	
	$liGallery->appendChild( $ulContainer );
	$treeContainer->appendChild( $liGallery );
    }
    /**
     * 
     */
    protected function _setTreeViewUsers()
    {
	$dom = $this->_getDom();
	$treeContainer = $this->_getTreeContainer();

	// CRIA LI DE USUARIO
	$liUser = $dom->createElement( 'li' );
	$liUser->setAttribute( 'class', 'closed' );

	// SPAN DE CONTROLE DA LI DA USUARIO
	$spanUser = $dom->createElement( 'span' );
	$spanUser->setAttribute( 'class', 'toggle' );

	// LINK DE CONTROLE DA USUARIO
	$aUser = $dom->createElement( 'a' );
	$aUser->setAttribute( 'class', 'folder' );
	$aUser->setAttribute( 'href', 'javascript:;' );

	// SPAN LINK USUARIO
	$spanAUser = $dom->createElement( 'span' );

	// TEXT USER
	$textUser = $dom->createTextNode( 'Usuários' );

	// Vincula nos filhos
	$spanAUser->appendChild( $textUser );
	$aUser->appendChild( $spanAUser );

	$liUser->appendChild( $spanUser );
	$liUser->appendChild( $aUser );
	
	$ulContainer = $dom->createElement('ul');
	$ulContainer->setAttribute('id', 'tree-container-user');
	
	// Busca usuarios cadastrados
	$users = $this->_searchUsers();
	
	foreach ( $users as $user ) {
	    
	    $li = $dom->createElement('li');
	    
	    $a = $dom->createElement('a');
	    $a->setAttribute('class', 'folder-image');
	    $a->setAttribute('href', 'javascript:searchImages( ' . $user->id . ',"' . $user->nome . '", "U");');
	    
	    $span = $dom->createElement('span');
	    $text = $dom->createTextNode( $user->nome );
	    
	    $span->appendChild( $text );
	    $a->appendChild( $span );
	    $li->appendChild( $a );
	    $ulContainer->appendChild( $li );
	}
	
	$liUser->appendChild( $ulContainer );
	$treeContainer->appendChild( $liUser );
    }
    
    public function getSize()
    {
	$imageDb = App_Model_DbTable_Factory::get('Imagem');
	
	$select = $imageDb->select()
			  ->from( $imageDb , array( 'size' => new Zend_Db_Expr('SUM(size)') ));
	
	$row = $imageDb->fetchRow( $select );
	
	return $this->view->bitCalculator( $row->size );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset $rows
     */
    protected function _searchGalleries()
    {
	// INIT DB DA GALERIA
	$dbGallery = App_Model_DbTable_Factory::get('galeria');
	
	$select = $dbGallery->select()
			    ->setIntegrityCheck( false )
			    ->order( 'nome' );
	
	return $dbGallery->fetchAll( $select );
    }
    
    /**
     *
     * @return Zend_Db_Table_Rowset $rows
     */
    protected function _searchUsers()
    {
	// INIT DBs
	$dbUser = App_Model_DbTable_Factory::get('Usuario');
	$dbImage = App_Model_DbTable_Factory::get('ImagemUsuario');
	$dbLogin = App_Model_DbTable_Factory::get('Login');
	
	$select = $dbUser->select()
			 ->distinct()
			 ->setIntegrityCheck( false )
			 ->from( array( 'u' => $dbUser ) )
			 ->join( array( 'l' => $dbLogin ), 'l.id = u.login_id', array('nome') )   
			 ->join( array( 'iu' => $dbImage ), 'iu.usuario_id = u.id', array() )   
			 ->order( 'l.nome' );
	
	return $dbUser->fetchAll( $select );
    }

}