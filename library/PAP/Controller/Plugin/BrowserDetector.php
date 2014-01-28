<?php
  class PAP_Controller_Plugin_BrowserDetector extends Zend_Controller_Plugin_Abstract
  {
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        try{
            $u_agent = $_SERVER['HTTP_USER_AGENT'];
            if(isset($_SERVER['REDIRECT_URL']))
                $qs = $_SERVER['REDIRECT_URL']."";
            else 
                $qs = "";
            $method1 = strpos(strtolower($qs), 'backendajax');
            $method2 = strpos(strtolower($qs), 'services');
            if(!($method1 === false && $method2 === false))
                return;
            $detector = new PAP_Helper_MobileDetect();
            
            if($detector->isMobile() && !$detector->isTablet() ){
                if($_SERVER['HTTP_HOST'] == 'promosalpaso.local')
                    Zend_Controller_Front::getInstance()->getResponse()->setRedirect('http://localhost:8080/m.web.promosalpaso');
                else
                    Zend_Controller_Front::getInstance()->getResponse()->setRedirect('http://mobile.'.$_SERVER['HTTP_HOST']);
                return;    
            }

            if(preg_match('/MSIE/i', $u_agent)) {
                if(!preg_match('/MSIE 10/i', $u_agent)){
                    $pathInfo= $request->getPathInfo();
                    if(!($pathInfo == "/index/browsernotsuported" || $pathInfo =! "/Backendajax/getfeatured")){
                        Zend_Controller_Front::getInstance()->getResponse()->setRedirect('http://promosalpaso.local/not-supported.html');     
                    }
                }
            }    
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PAP_Controller_Plugin_BrowserDetector->preDispatch()',$ex, $_SERVER['REQUEST_URI']);        
        }    
        
    }      
  }
?>
