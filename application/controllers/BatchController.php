<?php
class batchController extends Zend_Controller_Action
{
    /*15 días después de haberse dado de alta el usuario tiene que ser cambiado a la lista de precios C2*/
    public function updatepricerulesAction(){
        try{
            $executionLog =  "Inicio ejecución: ".date("Y-m-d H:i:s")."\n\r";
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            
            PAP_Model_User::changePriceRulesToNewUsers();
            $executionLog = $executionLog . "Fin ejecución: ".date("Y-m-d H:i:s");
            echo $executionLog; 
            PAP_Helper_Logger::writeLog(Zend_Log::INFO, 'PaymentController->updatepricerulesAction', $executionLog, $_SERVER['REQUEST_URI']); 
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BatchController->updatepricerulesAction',$e); 
        }        
    }
    
    /*Se verifica si los usuarios tienen más de dos períodos impagos o excedieron su crédito.*/
    public function actualizeuserstatusAction(){
        try{
             $executionLog =  "Inicio ejecución: ".date("Y-m-d H:i:s")."\n\r";
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            PAP_Model_Charge::proccessDebtorUsers();
            $executionLog = $executionLog . "Fin ejecución: ".date("Y-m-d H:i:s");
            echo $executionLog; 
            PAP_Helper_Logger::writeLog(Zend_Log::INFO, 'PaymentController->actualizeuserstatusAction', $executionLog, $_SERVER['REQUEST_URI']);     
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BatchController->actualizeuserstatusAction',$e); 
        }        
    }       
}
