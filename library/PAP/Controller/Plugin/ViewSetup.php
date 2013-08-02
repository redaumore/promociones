<?php
class PAP_Controller_Plugin_ViewSetup extends Zend_Controller_Plugin_Abstract
{
    /**
    * @var Zend_View
    */
    protected $_view;
    
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->init();
        $view = $viewRenderer->view;
        $this->_view = $view;
        $view->originalModule = $request->getModuleName();
        $view->originalController = $request->getControllerName();
        $view->originalAction = $request->getActionName();
        $view->doctype('XHTML1_STRICT');
        $prefix = 'PAP_View_Helper';
        $dir = dirname(__FILE__). '/../../View/Helper';
        $view->addHelperPath($dir, $prefix);
        $view->headMeta()->setName('Content-Type', 'text/html;charset=utf-8');
        //$view->headLink()->appendStylesheet($view->baseUrl().'/css/site.css');
    }
}