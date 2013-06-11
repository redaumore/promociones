<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        // insert sidebar to the response object
    //esto nunca funciono--->    $this->getResponse()->insert('header', $this->view->render('/index/header.phtml'));
    }

    public function indexAction()
    {
    }
    
    public function menuAction(){
        try{
            $this->user = $this->_helper->Session->getUserSession();
            $mainMenu = array();
            if(!isset($this->user)){
                
            }
            else{
                if($this->user->Status == ""){
                    $this->_helper->Session->setUserSession(null);
                    $this->menuAction();
                    return;    
                }
                //if($this->user->Status == 'pending')
                    //array_push($mainMenu, array('title'=>'Anunciantes', 'url'=>$this->view->url(array('controller'=>'auth', 'action'=>'login'),null, true)));    
                if($this->user->Status == 'validated'){
                    array_push($mainMenu, array('title'=>'Mis Datos', 'url'=>$this->view->url(array('controller'=>'branch', 'action'=>'new'),null, true)));   
                }
                if($this->user->Status == 'charged' ){
                    array_push($mainMenu, array('title'=>'Mis Datos', 'url'=>$this->view->url(array('controller'=>'branch', 'action'=>'index'),null, true)));   
                    array_push($mainMenu, array('title'=>'Mis Categorias', 'url'=>$this->view->url(array('controller'=>'branch', 'action'=>'categories'),null, true)));
                }
                if($this->user->Status == 'active' ){
                    array_push($mainMenu, array('title'=>'Mis Datos', 'url'=>$this->view->url(array('controller'=>'branch', 'action'=>'index'),null, true)));   
                    array_push($mainMenu, array('title'=>'Mis Categorias', 'url'=>$this->view->url(array('controller'=>'branch', 'action'=>'categories'),null, true)));
                    array_push($mainMenu, array('title'=>'Mis Promos', 'url'=>$this->view->url(array('controller'=>'promotion', 'action'=>'index'),null, true)));
                    array_push($mainMenu, array('title'=>'Mis Costos', 'url'=>$this->view->url(array('controller'=>'payment', 'action'=>'index'),null, true)));
                }
                array_push($mainMenu, array('title'=>'Mensajes', 'url'=>$this->view->url(array('controller'=>'index', 'action'=>'contactadm'),null, true)));
                array_push($mainMenu, array('title'=>'Ayuda', 'url'=>$this->view->url(array('controller'=>'index', 'action'=>'help'),null, true)));
            }
            $this->view->menu = $mainMenu;
            $this->_helper->viewRenderer->setResponseSegment('menu');
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'IndexController->menuAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
    
    function contactAction(){
        try{
            
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'IndexController->contactAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
    
    function helpAction(){
         try{
            
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'IndexController->helpAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
    
    function contactadmAction(){
         try{
            
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'IndexController->contactadmAction()',$ex, $_SERVER['REQUEST_URI']);
        }    
    }
    
}

