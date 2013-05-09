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
            'method_id' => $payment->getMethodId(),
            'payment_date' => $payment->getPaymentDate(),
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
                  ->setControlId($row->control_id)
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
                  ->setControlId($row->control_id)
                  ->setPaymentType($row->payment_type)
                  ->setCreated($row->created);
            $entries[] = $entry;
        }
        return $entries;
    }

}

