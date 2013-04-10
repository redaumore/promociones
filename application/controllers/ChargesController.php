<?php
class ChargesController extends Zend_Controller_Action
{
    /***
    * Devuelve un array con los últimos n payments, trayendo por defecto los últimos 6
    * 
    * @param mixed $user_id
    * @param mixed $periods
    */
    public function getLastPeyments($user_id, $periods = 6){
        $payments = PAP_Model_Payment::getPayments($user_id);            
    }
}