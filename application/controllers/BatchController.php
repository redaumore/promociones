<?php
class batchController extends Zend_Controller_Action
{
    /*15 días después de haberse dado de alta el usuario tiene que ser cambiado a la lista de precios C2*/
    public function updatepricerulesAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            
            PAP_Model_User::changePriceRulesToNewUsers();
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BatchController->updatepricerulesAction',$e); 
        }        
    }
    
    /*Se verifica si los usuarios tienen más de dos períodos impagos o excedieron su crédito.*/
    public function actualizeuserstatusAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            PAP_Model_Charge::proccessDebtorUsers();    
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BatchController->actualizeuserstatusAction',$e); 
        }        
    }       
}
