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
            'user_id'   => $charge->getUserId(),
            'period' => $charge->getPeriod(),
            'amount' => $charge->getAmount(),
            'final_amount' => $charge->getFinalAmount(),
            'discount' => $charge->getDiscount(),
            'status' => $charge->getStatus(),
            'created' => date('Y-m-d H:i:s'),
        );
 
        try{
        if (null === ($id = $charge->getId())) {
            unset($data['charge_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('charge_id = ?' => $id));
        }
        }
        catch(Exception $ex){
            //TODO 6: Loguear intento de inserción duplicado   
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
                  ->setUserId($row->user_id)
                  ->setPeriod($row->period)
                  ->setAmount($row->amount)
                  ->setFinalAmount($row->final_amount)
                  ->setDiscount($row->discount)
                  ->setStatus($row->status)
                  ->setCreated($row->created);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new PAP_Model_Charge();
            $entry->setId($row->charge_id)
                  ->setUserId($row->user_id)
                  ->setPeriod($row->period)
                  ->setAmount($row->amount)
                  ->setFinalAmount($row->final_amount)
                  ->setDiscount($row->discount)
                  ->setStatus($row->status)
                  ->setCreated($row->created);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function getUnpaidCharges($user_id = 0){
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $statement = "SELECT * FROM charge WHERE status <> 'A' " .(($user_id == 0)?";":" AND user_id = ".$user_id).";";
        $results = $adapter->fetchAll($statement);
        return $results;
    }
  }

