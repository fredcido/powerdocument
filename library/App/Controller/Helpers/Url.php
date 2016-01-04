<?php

class Zend_Controller_Action_Helper_Url extends Zend_Controller_Action_Helper_Abstract
{
    public function url( $action = 'index', $controller = null, $module = null, $params = array() )
    {
        $frontController = Zend_Controller_Front::getInstance();
        $url = $frontController->getBaseUrl();

        if ( empty( $module ) ) {

            $moduleName = $frontController->getRequest()->getModuleName();

            $url .= ( $frontController->getDefaultModule() == $moduleName ) ?
                    '/' :
                    '/' . $moduleName;

        } else $url .= '/' . $module;

        if ( empty( $controller ) ) {

            $controllerName = $frontController->getRequest()->getControllerName();
            $url .= '/' . $controllerName;
        } else $url .= '/' . $controller;


        $url .= '/' . $action;

        foreach ( $params as $key => $value )
            $url .= '/' . $key . '/' . $value;

        return $url;
    }
}