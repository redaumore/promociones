<?php

class IndexController extends Zend_Controller_Action
{

    private $user;
    protected $_flashMessenger;
    
    public function init()
    {
        parent::init(); 
  
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger'); 
        $this->view->flash_messages = $this->_flashMessenger->getMessages();         
    }

     private function checkLogin(){
        if(!PAP_Helper_Session::checkLogin())
            $this->_redirect('/auth/login');
        $this->user = $this->_helper->Session->getUserSession();
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
                if($this->user->Status == 'debtor' ){
                    array_push($mainMenu, array('title'=>'Mis Costos', 'url'=>$this->view->url(array('controller'=>'payment', 'action'=>'index'),null, true)));
                }
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
            $form = new PAP_Form_ContactForm();
            $this->view->form = $form;
            if($this->getRequest()->isPost()){
                if($form->isValid($_POST)){
                    $data = $form->getValues();
                    $client = new PAP_Helper_Client();
                    $data['ip'] = $client->getIP();
                    if(PAP_Helper_Session::checkLogin()){
                        $data['email'] = $this->user->getEmail();
                        $data['message_type'] = 'A';
                    }
                    else{
                        $data['message_type'] = 'C';    
                    }
                    $message = new PAP_Model_Message();
                    $message->setEmail($data['email'])
                            ->setIp($data['ip'])
                            ->setIpNumber(ip2long($data['ip']))
                            ->setName($data['name'])
                            ->setMessage($data['message'])
                            ->setMessageType($data['message_type']);
                    $message->save();
                    $this->_helper->FlashMessenger->addMessage('Su mensaje ha sido enviado correctamente.');
                    $this->_redirect('/index/contact');    
                }
            }
            $this->view->messages = $this->_helper->FlashMessenger->getMessages('actions');
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
    
    function conditionsAction(){
         try{
            
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'IndexController->contactadmAction()',$ex, $_SERVER['REQUEST_URI']);
        }    
    }
}