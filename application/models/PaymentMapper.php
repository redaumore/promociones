<?php

class PAP_Model_PaymentMapper
{
protected $_dbTable;
 
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
 
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('PAP_Model_DbTable_Payment');
        }
        return $this->_dbTable;
    }
 
    public function save(PAP_Model_Payment $payment)
    {
        $data = array(
            'charge_id'   => $payment->getChargeId(),
            'amount' => $payment->getAmount(),
            'control' => $payment->getControl(),
            'status' => $payment->getStatus(),
            'info' => $payment->getInfo(),
            'method_id' => $payment->getMethodId(),
            'payment_date' => $this->getDateDB($payment->getPaymentDate()),
            'entity' => $payment->getEntity(),
            'created' => date('Y-m-d H:i:s'),
        );
 
        if (null === ($id = $payment->getId())) {
            unset($data['payment_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('payment_id = ?' => $id));
        }
    }
 
    public function find($id, PAP_Model_Payment $payment)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $payment->setId($row->payment_id)
                  ->setChargeId($row->charge_id)
                  ->setAmount($row->amount)
                  ->setStatus($row->status)
                  ->setControlId($row->control_id)
                  ->setPaymentDate($row->payment_date)
                  ->setInfo($row->info)
                  ->setPaymentType($row->payment_type)
                  ->setCreated($row->created);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new PAP_Model_Payment();
            $entry->setId($row->payment_id)
                  ->setChargeId($row->charge_id)
                  ->setAmount($row->amount)
                  ->setStatus($row->status)
                  ->setInfo($row->info)
                  ->setControlId($row->control_id)
                  ->setPaymentType($row->payment_type)
                  ->setPaymentDate($row->payment_date)
                  ->setCreated($row->created);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function getPendingPayments($paymentType, $user_id){
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $statement = "SELECT p.* FROM payment p ".(($user_id == 0)?" ":" INNER JOIN charge c ON (p.charge_id = c.charge_id)")
                    ."WHERE p.status = 'P' AND method_id = ".$paymentType.(($user_id == 0)?";":" AND c.user_id = ".$user_id.";");
        $results = $adapter->fetchAll($statement);
        return $results;    
    }
    
    public function getPaymentByControl($control, $method_id, PAP_Model_Payment $payment){
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $statement = "SELECT p.* FROM payment p WHERE p.control = '".$control."' AND p.method_id = '".$method_id."';";
        $result = $adapter->fetchAll($statement);
        if(isset($result))
            $payment->setId($result->payment_id)
                  ->setChargeId($result->charge_id)
                  ->setAmount($result->amount)
                  ->setStatus($result->status)
                  ->setControlId($result->control_id)
                  ->setPaymentType($result->payment_type)
                  ->setInfo($result->info)
                  ->setCreated($result->created);
        else
            return false;
        return true;
        
    }
    
    private function getDateDB($date){
        $str = explode('-', $date);
        return $str[2]."-".$str[1]."-".$str[0];
    }

}

