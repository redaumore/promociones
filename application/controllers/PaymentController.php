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
                        //true: corta en el día de hoy
                        $payments = $this->getCurrentPeriod($user, true);
                        break;    
                    case 'pendientes':
                        break;
                    case 'rango':
                        break;
                    case 'ultimos':
                        //devulve los últimos 6 períodos
                        $payments = $this->getLastPayments($user);
                        break;
                } 
                echo var_dump($payments);
            }
        }
        else{
               
        }
    }
    
    
    public function createchargesAction(){
        $config = new PAP_Helper_Config();
        $lastPeriod = $config->getLastPeriod();
        $currentPeriod = $this->getCurrentPeriodCode();
        if(!($lastPeriod == $currentPeriod)){
            if($lastPeriod <> ""){
                $promos = PAP_Model_Promotion::getPromotionsByPeriod($lastPeriod);
                    
            }
            $config->setLastPeriod($currentPeriod);
            
                    
        }  
    }
    /***
    * Devuelve un array con los últimos n payments, trayendo por defecto los últimos 6
    * 
    * @param mixed $user_id
    * @param mixed $periods
    */
    public function getLastPayments($user, $numperiods = 6){
        $date = date('Y-m-d');
        $periods = PAP_Model_Period::getPeriodsOffset($date, $numperiods);
        $payments = PAP_Model_Payment::getPayments($user, $periods);
        return $payments;
    }
    
    private function getCurrentPeriod($user, $untiltoday){
        $dates = array(array(date('Y-m-d')),array(date('Y-m-d')));
        $periods = PAP_Model_Period::getPeriods($dates);
        if($untiltoday){
            $per = $periods[0];
            $per->setTo(date('Y-m-d'));
            $periods[0] = $per;
        }
        $payments = PAP_Model_Payment::getPayments($user, $periods);
        return $payments;
    }
    
    public function getCurrentPeriodCode(){
        $code = "";
        $date = getdate();
        ($date['mday']<16)?$code = '1':$code = '2';
        $month_mini = array("","ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "DIC");
        $code =$code.$month_mini[$date['mon']];
        $code =$code.substr($date['year'], -2);
        return $code;
    }
    
    private function checkLogin(){
        $this->user = $this->_helper->Session->getUserSession();
        if(!isset($this->user))
            $this->_redirect('/auth/login');    
    }
}