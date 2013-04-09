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
    
    public function menuAction()
    {
        $this->user = $this->_helper->Session->getUserSession();
        if(!isset($this->user)){
            $mainMenu = array(array('title'=>'Inicio', 'url'=>$this->view->url(array(), null, true)),
                          array('title'=>'Anunciantes', 
                                'url'=>$this->view->url(array('controller'=>'auth', 'action'=>'login'),null, true)),
                          array('title'=>'Contactenos',
                                'url'=>$this->view->url(array('controller'=>'index', 'action'=>'contact'),null, true)),
                          array('title'=>'Buscar Promos', 
                                'url'=>$this->view->url(array('controller'=>'promotion', 'action'=>'search'),null, true)),
                          );    
        }
        else{
            if($this->user->Status == ""){
                $this->_helper->Session->setUserSession(null);
                $this->menuAction();
                return;    
            }
            $mainMenu = array(array('title'=>'Inicio', 'url'=>$this->view->url(array(), null, true)));
            if($this->user->Status == 'pending')
                array_push($mainMenu, array('title'=>'Anunciantes', 'url'=>$this->view->url(array('controller'=>'auth', 'action'=>'login'),null, true)));    
            if($this->user->Status == 'validated'){
                array_push($mainMenu, array('title'=>'Salir', 'url'=>$this->view->url(array('controller'=>'auth', 'action'=>'logout'),null, true)));
                array_push($mainMenu, array('title'=>'Mis Datos', 'url'=>$this->view->url(array('controller'=>'branch', 'action'=>'index'),null, true)));   
            }
            if($this->user->Status == 'charged' ){
                array_push($mainMenu, array('title'=>'Salir', 'url'=>$this->view->url(array('controller'=>'auth', 'action'=>'logout'),null, true)));
                array_push($mainMenu, array('title'=>'Mis Datos', 'url'=>$this->view->url(array('controller'=>'branch', 'action'=>'index'),null, true)));   
                array_push($mainMenu, array('title'=>'Mis Categorias', 'url'=>$this->view->url(array('controller'=>'branch', 'action'=>'categories'),null, true)));
            }
            if($this->user->Status == 'active' ){
                array_push($mainMenu, array('title'=>'Salir', 'url'=>$this->view->url(array('controller'=>'auth', 'action'=>'logout'),null, true)));
                array_push($mainMenu, array('title'=>'Mis Datos', 'url'=>$this->view->url(array('controller'=>'branch', 'action'=>'index'),null, true)));   
                array_push($mainMenu, array('title'=>'Mis Categorias', 'url'=>$this->view->url(array('controller'=>'branch', 'action'=>'categories'),null, true)));
                array_push($mainMenu, array('title'=>'Mis Promos', 'url'=>$this->view->url(array('controller'=>'promotion', 'action'=>'index'),null, true)));
                array_push($mainMenu, array('title'=>'Mis Costos', 'url'=>$this->view->url(array('controller'=>'promotion', 'action'=>'index'),null, true)));
            }
            array_push($mainMenu, array('title'=>'Contactenos', 'url'=>$this->view->url(array('controller'=>'index', 'action'=>'contact'),null, true)));
            array_push($mainMenu, array('title'=>'Buscar Promos', 'url'=>$this->view->url(array('controller'=>'promotion', 'action'=>'search'),null, true)));
            
            
            
                          
                              
        }
        $this->view->menu = $mainMenu;
        $this->_helper->viewRenderer->setResponseSegment('menu');
    }
    
    function contactAction(){
        
    }
}

