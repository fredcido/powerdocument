<?php

class ErrorController extends Zend_Controller_Action
{
    public function init()
    {
	$this->_helper->layout()->setLayout('auth');
    }

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
	
        if ( !$errors ) {
            $this->_helper->redirector->goToSimple( 'index', 'index' );
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
		
                $this->view->message = 'P&aacute;gina n&atilde;o encontrada';
		$this->view->code = 404;
		$this->view->title = 'Not Found';
		
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Erro na aplica&ccedil;&atilde;o';
		$this->view->code = 500;
		$this->view->title = 'Application Error';
                break;
        }

        // conditionally display exceptions
        if ( $this->getInvokeArg('displayExceptions') == true ) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request   = $errors->request;
    }
    
    public function requestAction()
    {
	$error = $this->_getParam('code', 'error' );
	
	if ( is_int( $error ) )
	    $this->getResponse()->setHttpResponseCode( $error );
	
	$this->_helper->viewRenderer->setRender( 'error' );
	
	$this->view->code = $error;
	$this->view->title = 'Forbidden';
	$this->view->message = 'Voc&ecirc; n&atilde;o pode acessar a p&aacute;gina solicitada.';
	
    }
}