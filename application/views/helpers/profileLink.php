<?php
/**
 * ProfileLink helper
 *
 * Call as $this->profileLink() in your layout script
 */
class Zend_View_Helper_ProfileLink extends Zend_View_Helper_Abstract {


     /**
     * View instance
     *
     * @var  Zend_View_Interface
     */
    public $view;


    public function profileLink()  {
        $html = '<a id="tab_ingreso" href="'. $this->view->baseUrl('/auth/login').'">Ingreso Anunciantes&nbsp;<i class="icon-user"></i></a>';
        try{
            $baseUrl = $this->view->baseUrl();

            $auth = Zend_Auth::getInstance();

            if ($auth->hasIdentity()) { 
                // here have to make amendments to what you have 
                // in your identity.
                /*$identity = $auth->getIdentity();                        
                $fname = $identity->property->nickname; 
                $url = $this->view->baseUrl('/user');
                $fnameLink = "<a href=\"$url\"/>$fname</a>";
                $html = $fnameLink . ' <span>|</span> <a href="'.$baseUrl.'/logout">'
                    . $this->view->translate('Logout') . '</a>' ;*/
                $html = '<a id="tab_ingreso" href="'.$this->view->baseUrl('/auth/logout').'">Hola '.$auth->getIdentity()->name.'. | Salir&nbsp;&nbsp;<i class="icon-off"></i></a>';
            }   

            return $html;
        }
        catch(Exception $ex){
            Zend_Auth::getInstance()->clearIdentity();
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'ProfileLink->profileLink',$e, $_SERVER['REQUEST_URI']);
            return $html;
        }
    }


     /**
     * Get Zend_View instance
     *
     * @param Zend_View_Interface $view
     */
    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

}