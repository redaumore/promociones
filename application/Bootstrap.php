<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _init(){
        $this->_initLayout();
    }

    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
        
        // initialize and register translation object
        /*$translate = new Zend_Translate('array',
        APPLICATION_PATH.'/configs/languages/',
        null,
        array('scan' => Zend_Translate::LOCALE_FILENAME));
        
        $registry = Zend_Registry::getInstance();
        $registry->set('Zend_Translate', $translate);
        $translate->setLocale('es_AR');
        */
    }
    
    protected function _initLayout()
    {
        require_once 'Zend/Loader.php';
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('PAP_');
        
        $layout = Zend_Layout::startMvc();
        $layout->setLayoutPath(APPLICATION_PATH . '/layouts/scripts/');
        
        $this->bootstrap('FrontController');
        $frontController = $this->getResource('FrontController');
        $frontController->registerPlugin(new PAP_Controller_Plugin_ViewSetup());
        $frontController->registerPlugin(new PAP_Controller_Plugin_ActionSetup(), 98);
        //$frontController->registerPlugin(new PAP_Helper_Session());
        return $layout;
    }
    
    protected function _initAutoload() {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'PAP_',
            'basePath'  => dirname(__FILE__),
        ));

        return $autoloader;
    }
    
    protected function _initView()
    {
        // Initialize view
        $view = new Zend_View();
        $view->doctype('XHTML1_STRICT');
        $view->headTitle('Promos al paso - Backend');
        $view->skin = 'default';
        $view->setEncoding('UTF-8');
        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
        
        // Return it, so that it can be stored by the bootstrap
        return $view;
    }
    
    protected function _initHelper(){
        Zend_Controller_Action_HelperBroker::addPrefix('PAP_Helper');
    }
    
    protected function _initNavigation(){
        $this->bootstrap("layout");
        $layout = $this->getResource("layout");
        $view = $layout->getView();
        $config = new Zend_Config_Xml(APPLICATION_PATH . "/configs/navigation.xml", "nav");
        $navigation = new Zend_Navigation($config);
        $view->navigation($navigation);
    }
    
    protected function _initLog(){
        if($this->hasPluginResource("log")){
            $r = $this->getPluginResource("log");
            $log = $r->getLog();
            Zend_Registry::set("log", $log);
        }
        $db = $this->getPluginResource("db");
        $columnMapping = array('priority' => 'priority',
            'priorityname' => 'priorityname', 
            'message' => 'message',
            'timestamp' => 'timestamp', 
            'user' => 'user',    
            'params' => 'params',
            'context' => 'context',
            );
        $writer = new Zend_Log_Writer_Db($db->getDbAdapter(), 'log', $columnMapping);
        $logger = new Zend_Log($writer);
        Zend_Registry::set("logDB", $logger);
    }
    
    protected function _initCache(){
        $frontend= array(
            'lifetime' => 7200,
            'automatic_serialization' => true
            );

        $backend= array(
            'cache_dir' => PUBLIC_PATH.'/tmp/',
            );

        $cache = Zend_Cache::factory('core',
            'File',
            $frontend,
            $backend
            );

        Zend_Registry::set('cache',$cache);
    }
}

