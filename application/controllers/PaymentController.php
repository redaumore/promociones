<?php
class PaymentController extends Zend_Controller_Action
{
     public function indexAction(){
        $this->checkLogin();
        $form = new PAP_Form_PaymentForm();
        $this->view->form = $form;
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                $data = $form->getValues();
                $user = $this->_helper->Session->getUserSession();
                switch($data['reportType']){
                    case 'actual':
                        $payments = $this->getCurrentPeriod($user);
                        break;    
                    case 'pendientes':
                        break;
                    case 'rango':
                        break;
                    case 'ultimos':
                        //devulve los últimos 6 períodos
                        $payments = $this->getLastPeyments($user);
                        break;
                } 
                
                
                //$payments = 
            }
        }
        else{
               
        }
    }
    
    /***
    * Devuelve un array con los últimos n payments, trayendo por defecto los últimos 6
    * 
    * @param mixed $user_id
    * @param mixed $periods
    */
    public function getLastPeyments($user, $numperiods = 6){
        $date = date('Y-m-d');
        $periods = PAP_Model_Period::getPeriodsOffset($date, $numperiods);
        $payments = PAP_Model_Payment::getPayments($user, $period);            
    }
    
    private function getCurrentPeriod($user){
        $dates = array(array(date('Y-m-d')),array(date('Y-m-d')));
        $periods = PAP_Model_Period::getPeriods($dates);
        $payments = PAP_Model_Payment::getPayments($user, $period);
    }
    private function checkLogin(){
        $this->user = $this->_helper->Session->getUserSession();
        if(!isset($this->user))
            $this->_redirect('/auth/login');    
    }
}