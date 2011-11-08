<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDocType()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }
    
    protected function _initView()
    {
            // Initialize view
            $view = new Zend_View();
 
            $view->headLink()->headLink( array( 'rel' => 'shortcut icon',
                    'href' => '/images/favicon.ico',
                    'type' => 'image/x-icon' ));

            // Add it to the ViewRenderer
            $viewRenderer =
                Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
            $viewRenderer->setView($view);
 
            // Return it, so that it can be stored by the bootstrap
            return $view;
    } 
    
    protected function _initDatabases()
    {
        $this->bootstrap('db');
        $db = $this->getResource('db');
        
        Zend_Registry::set('db', $db);
    }
    
}

