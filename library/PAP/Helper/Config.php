<?php
// include auto-loader class
require_once 'Zend/Loader/Autoloader.php';
  
class PAP_Helper_Config extends Zend_Controller_Action_Helper_Abstract{

    /**
     * @var Zend_Loader_PluginLoader
     */
    public $pluginLoader;
 
    /**
     * Constructor: initialize plugin loader
     * 
     * @return void
     */
    public function __construct()
    {
        // register auto-loader
        $loader = Zend_Loader_Autoloader::getInstance();    

        $this->pluginLoader = new Zend_Loader_PluginLoader();
    }
    
    public function getLastPeriod(){
        // read XML config file
        $config = new Zend_Config_Xml(APPLICATION_PATH.'/configs/config.xml', 'payments');
        return $config->last_period;
    }
    
    public function setLastPeriod($periodcode){
        $config = array('payments'=>array('last_period'=>$periodcode));
        $writer = new Zend_Config_Writer_Xml();
        $writer->write(APPLICATION_PATH.'/configs/config.xml', new Zend_Config($config));
    }
    
    public function getMPConfig(){
        $result = array();
        // read XML config file
        $config = new Zend_Config_Xml(APPLICATION_PATH.'/configs/config.xml', 'payments');
        $result['mp_client_id'] = $config->mp_client_id;
        $result['mp_client_secret'] = $config->mp_client_secret;
        $result['mp_url_token'] = $config->url_token;
        return $result;
    }
    
    public function getCategoryUpdate(){
        $config = new Zend_Config_Xml(APPLICATION_PATH.'/configs/config.xml', 'updates');
        return new DateTime($config->category);    
    }
}