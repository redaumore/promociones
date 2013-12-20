<?php
  class PAP_Controller_Plugin_BrowserDetector extends Zend_Controller_Plugin_Abstract
  {
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        try{
            $u_agent = $_SERVER['HTTP_USER_AGENT'];

            if(preg_match('/MSIE/i', $u_agent)) {
                //if(!preg_match('/MSIE 10/i', $u_agent)){
                    $pathInfo= $request->getPathInfo();
                    if(!($pathInfo == "/index/browsernotsuported" || $pathInfo =! "/Backendajax/getfeatured")){
                        Zend_Controller_Front::getInstance()->getResponse()->setRedirect('http://promosalpaso.local/index/browsernotsuported');     
                    }
                //}
            }    
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PAP_Controller_Plugin_BrowserDetector->preDispatch()',$ex, $_SERVER['REQUEST_URI']);        
        }    
        
    }      
  }
?>
