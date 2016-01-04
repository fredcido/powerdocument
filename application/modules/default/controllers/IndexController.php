<?php

/**
 * 
 */
class IndexController extends App_Controller_Padrao
{

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
	$this->view->noToolBar = true;
	
    }

    /**
     * (non-PHPdoc)
     * @see App_Controller_Padrao::indexAction()
     */
    public function indexAction()
    {
    }

}