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
        // action body
    }
    
    public function menuAction()
    {
        $mainMenu = array(array('title'=>'Home', 'url'=>$this->view->url(array(), null, true)),
                          array('title'=>'Mis Datos', 
                                'url'=>$this->view->url(array('controller'=>'branch', 'action'=>'index'),null, true)),
                          array('title'=>'Mis Categorias', 
                                'url'=>$this->view->url(array('controller'=>'branch', 'action'=>'categories'),null, true)),
                          array('title'=>'Mis Promos', 
                                'url'=>$this->view->url(array('controller'=>'promotion', 'action'=>'index'),null, true)),
                          array('title'=>'Contactenos',
                                'url'=>$this->view->url(array('controller'=>'index', 'action'=>'contact'),null, true)),
                          );
        $this->view->menu = $mainMenu;
        $this->_helper->viewRenderer->setResponseSegment('menu');
    }
}

