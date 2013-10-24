<?php
class PaymentController extends Zend_Controller_Action
{
    public function init(){
      $form = new PAP_Form_PaymentForm();
      $this->view->form = $form;      
     }
    
    private function checkLogin(){
        if(!PAP_Helper_Session::checkLogin())
            $this->_redirect('/auth/login');
        $this->user = $this->_helper->Session->getUserSession();
    }
     
    public function indexAction(){
        try{
            $this->checkLogin();
            $form = new PAP_Form_PaymentForm();
            $this->view->form = $form; 
            $user = $this->_helper->Session->getUserSession();
            $paymentMethods = $user->getPaymentMethods();
            if($this->getRequest()->isPost()){
                //$this->_helper->viewRenderer->setNoRender();
                //$this->_helper->getHelper('layout')->disableLayout();
                if($form->isValid($_POST)){
                    $data = $this->getParam('reportType');
                    switch($data){
                        case 'actual':
                            //true: corta en el día de hoy
                            $payments = $this->getCurrentPeriod($user, true);
                            break;    
                        case 'pendientes':
                            $payments = $this->getPendingPayments($user);
                            break;
                        case 'ultimos':
                            //devulve los últimos 6 períodos
                            $payments = $this->getLastPayments($user);
                            break;
                    }
                    
                    $data = array();
                    $data['payments'] = $payments;
                    $data['payment_methods'] = $paymentMethods; 
                    $control = $form->getElement('data');
                    $valor = json_encode($data);
                    $control->setValue($valor);
                    //echo $this->_helper->json($payments);
                }
            }
            else{
                $payments = $this->getPendingPayments($user);
                if(count($payments) > 0){
                    $paymentMethods = $user->getPaymentMethods();
                    $data = array();
                    $data['payments'] = $payments;
                    $data['payment_methods'] = $paymentMethods; 
                    $control = $form->getElement('data');
                    $valor = json_encode($data);
                    $control->setValue($valor);    
                }
            }
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PaymentController->indexAction()',$ex, $_SERVER['REQUEST_URI']);
        }
     }
     
    public function successAction(){
    try{
         $paidCharges = $_GET['external_reference'];
         $status = $_GET['collection_status'];
         $status = $this->getStatusChar($status);
         $collection_id = $_GET['collection_id'];
         $entity = $_GET['payment_type'];
         $paidCharges = explode(',', $paidCharges);
         foreach($paidCharges as $charge_id){
            $charge = new PAP_Model_Charge();
            $charge->loadById($charge_id);
            $charge->setStatus($status);
            $charge->save();
            $payment = new PAP_Model_Payment();
            $payment->setAmount($charge->getAmount())
                ->setControl($collection_id)
                ->setChargeId($charge->getId())
                ->setMethodId('MP')
                ->setPaymentDate(date('Y-m-d H:i:s'))
                ->setEntity($entity);
            $payment->save();
            $user = new PAP_Model_User();
            $user->loadById($charge->getId());
            $user->refreshStatus();
         }
    }
    catch(Exception $e){
        PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PaymentController->successAction',$e, $_SERVER['REQUEST_URI']);    
    }
    }

    public function failureAction(){}

    public function pendingAction(){
    try{
        $paidCharges = $_GET['external_reference'];
         $status = $_GET['collection_status'];
         $status = $this->getStatusChar($status);
         $collection_id = $_GET['collection_id'];
         $entity = $_GET['payment_type'];
         $paidCharges = explode(',', $paidCharges);
         foreach($paidCharges as $charge_id){
            $charge = new PAP_Model_Charge();
            $charge->loadById($charge_id);
            $charge->setStatus($status);
            $charge->save();
            $payment = new PAP_Model_Payment();
            $payment->setAmount($charge->getAmount())
                ->setControl($collection_id)
                ->setChargeId($charge->getId())
                ->setMethodId('MP')
                ->setPaymentDate(date('Y-m-d H:i:s'))
                ->setEntity($entity);
            $payment->save();
         }
     }
     catch(Exception $e){
        PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PaymentController->pendingAction',$e, $_SERVER['REQUEST_URI']);    
     }
    }

    public function createchargesAction(){
    try{
        $executionLog =  "Inicio ejecución: ".date("Y-m-d H:i:s")."\n\r";
        $payments = null;
        
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $config = new PAP_Helper_Config();
        $lastPeriod = $config->getLastPeriod();
        $currentPeriod = $this->getCurrentPeriodCode();
        if($lastPeriod <> $currentPeriod){
            if($lastPeriod <> ""){
                $promos = PAP_Model_Promotion::getPromotionsByPeriod($lastPeriod);
                
                $period = new PAP_Model_Period();
                $period->loadByCode($lastPeriod);
                $periods = array();
                $periods[] = $period;
                
                $payments = PAP_Model_Payment::getAllPayments($periods);
                foreach($payments as $payment){
                    $charge = new PAP_Model_Charge();
                    $charge->setAmount($payment["total"])
                            ->setDiscount(0)
                            ->setPeriod($payment["periodo"])
                            ->setStatus('P')
                            ->setUserId($payment["user_id"])
                            ->setFinalAmount($payment["total"]);
                    if($payment["total"] == "0.00")
                        $charge->setStatus('A');
                    $charge->save();
                }
            }
            $config->setLastPeriod($currentPeriod);
        }
        $executionLog = $executionLog . "Fin ejecución: ".date("Y-m-d H:i:s").". Periodos (last/current) = (".$lastPeriod."/".$currentPeriod.") Se crearon ".count($payments)." cargos.";
        echo $executionLog; 
        PAP_Helper_Logger::writeLog(Zend_Log::INFO, 'PaymentController->createchargesAction', $executionLog, $_SERVER['REQUEST_URI']); 
    }
     catch(Exception $e){
        PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PaymentController->createchargesAction',$e, $_SERVER['REQUEST_URI']);    
     }
      
    }

    public function mpnotificationAction(){
    try{
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $user_id = 0;
        if(isset($_GET['id']) && $_GET['topic'] == 'payment')
            $notification_id = $_GET['id'];
        else
            throw new Exception("Se ha llamado al método mpnotification pero no se encontró el parámetro id", 10001);
            
        //$pendigPayments = PAP_Model_Payment::getPendigPaymentsMP($user_id);
        
        $config = new PAP_Helper_Config();
        $MPConfig = $config->getMPConfig();
        $MPObject = new PAP_MP($MPConfig['mp_client_id'], $MPConfig['mp_client_secret']);
        $payment_info = $MPObject->get_payment_info($notification_id);
        
        if ($payment_info["status"] == 200) {
            $jsonPayment = json_decode($payment_info["response"]);
            $payment = new PAP_Model_Payment();
            $payment->loadByControl($jsonPayment->{'id'}, 'MP');
            $payment->setStatus($jsonPayment->{'status'});
            $charge = new PAP_Model_Charge();
            $charge->loadById($payment->getChargeId());
            $charge->setStatus($payment->getStatus());
            $payment->save();
            $charge->save();
            $user = new PAP_Model_User();
            $user->loadById($charge->getId());
            $user->refreshStatus();    
        }
        else{
            throw new Exception("MP Error (".$payment_info["status"].")".$payment_info["error"].": ".$payment_info["message"]." CAUSE:".$payment_info["cause"] );
        }
    }
    catch(Exception $ex){
         PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PaymentController->mpnotificationAction',$e, $_SERVER['REQUEST_URI']); 
    }
    }
    /***
    * Devuelve un array con los últimos n payments, trayendo por defecto los últimos 6
    * 
    * @param mixed $user_id
    * @param mixed $periods
    */
    public function getLastPayments(PAP_Model_User $user, $numperiods = 6){
        try{
            $date = date('Y-m-d');
            $periods = PAP_Model_Period::getPeriodsOffset($date, $numperiods);
            $payments = PAP_Model_Payment::getPayments($user, $periods);
            return $payments;
        }
        catch(Exception $ex){
             PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PaymentController->mpnotificationAction',$e, $_SERVER['REQUEST_URI']); 
        }
    }

    private function getCurrentPeriod(PAP_Model_User $user, $untiltoday){
    $dates = array(array(date('Y-m-d')),array(date('Y-m-d')));
    $periods = PAP_Model_Period::getPeriods($dates);
    if($untiltoday){
        $per = $periods[0];
        $per->setTo(date('Y-m-d H:i:s'));
        $periods[0] = $per;
    }
    $payments = PAP_Model_Payment::getPayments($user, $periods);
    return $payments;
    }

    public function getCurrentPeriodCode(){
    $code = "";
    $date = getdate();
    ($date['mday']<16)?$code = '1':$code = '2';
    $month_mini = array("","ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC");
    $code =$code.$month_mini[$date['mon']];
    $code =$code.substr($date['year'], -2);
    return $code;
    }

    private function getPendingPayments(PAP_Model_User $user = null){
    $payments = array();
    $unpaidCharges = PAP_Model_Charge::getUnpaidCharges($user);
    $periods = array();
    foreach($unpaidCharges as $charge){
        $period = new PAP_Model_Period();
        $period->loadByCode($charge->getPeriod());
        $periods[$charge->getId()] = $period;    
    }
    if(!isset($user)){
        $payments = PAP_Model_Payment::getAllPayments($periods);}
    else{
        $payments = PAP_Model_Payment::getPayments($user, $periods);}
    return $payments;    
    }

    private function getStatusChar($status){
    $status_char;
     switch ($status) {
        case 'approved':
            $status_char = 'A';
            break;
        case 'pending':
            $status_char = 'P';
            break;
        case 'in_process':
            $status_char = 'I';
            break;
        case 'rejected':
            $status_char = 'R';
            break;
        case 'refunded':
            $status_char = 'D';
            break;
        case 'cancelled':
            $status_char = 'C';
            break;
        case 'in_mediation':
            $status_char = 'M';
            break;
    }
    return $status_char;    
    }
    
    public function accountinfoAction(){
        $this->_helper->layout()->disableLayout();
        
        //$this->_helper->viewRenderer->setNoRender(true);
        $periods = $this->getRequest()->getparam("periods");
        $amount = $this->getRequest()->getparam("amount");
        $titular = "Rolando Daumas";
        $dni = "22367175";
        $bank = "Banco Galicia";
        $account_type = "Caja de Ahorro";
        $cbu = "00700108 - 30004004782152";
        
        $html ="<table>"
        ."<tr>"
        ."    <td class='label'>Titular:</td>"
        ."    <td><span id='titular'>".$titular."</span></td>"
        ."</tr>"
        ."<tr>"
        ."    <td class='label'>DNI</td>"
        ."    <td><span id='dni'>".$dni."</span></td>"
        ."</tr>"
        ."<tr>"
        ."    <td class='label'>Banco:</td>"
        ."    <td><span id='bank'>".$bank."</span></td>"
        ."</tr>"
        ."<tr>"
        ."    <td class='label'>CBU:</td>"
        ."    <td><span id='cbu'>".$cbu."</span></td>"
        ."</tr>"
        ."<tr>"
        ."    <td class='label'>Tipo Cuenta:</td>"
        ."    <td><span id='acctype'>".$account_type."</span></td>"
        ."</tr>"
        ."<tr>"
        ."    <td class='label'>Períodos:</td>"
        ."    <td><span id='acctype'>".$periods."</span></td>"
        ."</tr>"
        ."<tr>"
        ."    <td class='label'>Monto Total:</td>"
        ."    <td><span id='acctype'>".$amount."</span></td>"
        ."</tr>"
        ."<tr>"
        ."    <td colspan='2' align='center'><input type='button' value='Imprimir' onclick='window.print();'/></td>"
        ."</tr>"
        ."</table>";
        
        echo $html;    
    }
}