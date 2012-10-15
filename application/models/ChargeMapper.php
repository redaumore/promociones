<?php

class PAP_Model_ChargeMapper
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
            $this->setDbTable('PAP_Model_DbTable_Charge');
        }
        return $this->_dbTable;
    }
 
    public function save(PAP_Model_Charge $charge)
    {
        $data = array(
            'user_id'   => $charge->getuserId(),
            'period' => $charge->getPeriod(),
            'amount' => $charge->getAmount(),
            'discount' => $charge->getDiscount(),
            'paid_off' => $charge->getPaidOff(),
            'created' => date('Y-m-d H:i:s'),
        );
 
        if (null === ($id = $branch->getId())) {
            unset($data['charge_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('charge_id = ?' => $id));
        }
    }
 
    public function find($id, PAP_Model_Charge $charge)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $charge->setId($row->charge_id)
                  ->setuserId($row->user_id)
                  ->setPeriod($row->period)
                  ->setAmount($row->amount)
                  ->setDiscount($row->discount)
                  ->setPaidOff($row->paid_off)
                  ->setCreated($row->created);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new PAP_Model_Charge();
            $entry->setId($row->charge_id)
                  ->setuserId($row->user_id)
                  ->setPeriod($row->period)
                  ->setAmount($row->amount)
                  ->setDiscount($row->discount)
                  ->setPaidOff($row->paid_off)
                  ->setCreated($row->created);
            $entries[] = $entry;
        }
        return $entries;
    }
  }

