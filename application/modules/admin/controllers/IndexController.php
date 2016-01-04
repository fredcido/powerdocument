<?php

class Admin_IndexController extends Zend_Controller_Action
{
    public function init()
    {
	$this->_helper->redirector->goToSimple( 'index', 'index', 'default' );
    }
}
