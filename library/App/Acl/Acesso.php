<?php

class App_Acl_Acesso extends Zend_Acl
{
    protected $_permissoesConfig;
    
    public function __construct()
    {
        $this->_permissoesConfig = new Zend_Config_Xml( APPLICATION_PATH . '/configs/permissao.xml' );
        $this->_trataPermissoes();
    }

    protected function _trataPermissoes()
    {
        $this->deny();

        $this->addResource( 'default/index' );
        $this->addResource( 'default/error' );
        $this->addResource( 'default/auth' );

        $usr_id = Zend_Auth::getInstance()->getIdentity()->usuario_id;
        $perfil = Zend_Auth::getInstance()->getIdentity()->usuario_tipo;

        $this->allow( $usr_id, 'default/index' );
        $this->allow( $usr_id, 'default/error' );
        $this->allow( $usr_id, 'default/auth' );

        $this->addRole( new Zend_Acl_Role( $usr_id ) );

        $permissoes = $this->_permissoesConfig->toArray();
        foreach ( $permissoes as $modulo ) {

            foreach ( $modulo as $controller => $permissao ) {

                $resource = $modulo . '/' . $controller;
                $this->addResource( new Zend_Acl_Resource( $resource ) );

                if ( in_array( $perfil, (array)$permissao ) )
                    $this->allow( $usr_id, $resource );
            }
        }
    }
}