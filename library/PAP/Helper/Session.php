<?php
  /**
 * Action Helper for finding days in a month
 */
class PAP_Helper_Session extends Zend_Controller_Action_Helper_Abstract
{
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
        $this->pluginLoader = new Zend_Loader_PluginLoader();
    }
    
    public function getUserSession(){
        $session = new Zend_Session_Namespace('PAP');
        return $session->user;    
    }
    
    public function setUserSession($user){
        $session = new Zend_Session_Namespace('PAP');    
        $session->user = $user;
    }
    
    public function getBranchSession(){
        $session = new Zend_Session_Namespace('PAP');
        return $session->branch;    
    }
    
    public function setBranchSession($branch){
        $session = new Zend_Session_Namespace('PAP');    
        $session->branch = $branch;
    }
    
    public static function checkLogin(){
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            return true;
        }
        return false;
    }
    
}
