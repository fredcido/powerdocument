<?php

class App_View_Helper_Video extends Zend_View_Helper_Abstract
{

    protected $_dom;
    protected $_treeContainer;

    public function video()
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
	$this->_setTreeViewVideos();
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
    protected function _setTreeViewVideos()
    {
	$dom = $this->_getDom();
	$treeContainer = $this->_getTreeContainer();

	// CRIA LI DE VIDEOS
	$liGallery = $dom->createElement( 'li' );
	$liGallery->setAttribute( 'class', 'closed' );

	// LINK DE CONTROLE DOS VIDEOS
	$aGallery = $dom->createElement( 'a' );
	$aGallery->setAttribute( 'class', 'folder' );
	$aGallery->setAttribute('href', 'javascript:searchVideos("A");');

	// SPAN LINK VIDEOS
	$spanAGallery = $dom->createElement( 'span' );

	// TEXT GALERIA
	$textGallery = $dom->createTextNode( 'Vídeos' );

	// Vincula nos filhos
	$spanAGallery->appendChild( $textGallery );
	$aGallery->appendChild( $spanAGallery );

	$liGallery->appendChild( $aGallery );
	
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
	    $a->setAttribute('href', 'javascript:searchVideos( "U",' . $user->id . ',"' . $user->nome . '");');
	    
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
    
    /**
     *
     * @return Zend_Db_Table_Rowset $rows
     */
    protected function _searchUsers()
    {
	// INIT DBs
	$dbUser = App_Model_DbTable_Factory::get('Usuario');
	$dbVideo = App_Model_DbTable_Factory::get('VideoUsuario');
	$dbLogin = App_Model_DbTable_Factory::get('Login');
	
	$select = $dbUser->select()
			 ->distinct()
			 ->setIntegrityCheck( false )
			 ->from( array( 'u' => $dbUser ) )
			 ->join( array( 'l' => $dbLogin ), 'l.id = u.login_id', array('nome') )   
			 ->join( array( 'vu' => $dbVideo ), 'vu.usuario_id = u.id', array() )   
			 ->order( 'l.nome' );
	
	return $dbUser->fetchAll( $select );
    }

}