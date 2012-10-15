<?php

class PAP_Model_CustomerMapper
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
            $this->setDbTable('PAP_Model_DbTable_Customer');
        }
        return $this->_dbTable;
    }
 
    public function save(PAP_Model_Customer $customer)
    {
        $data = array(
            'email'   => $customer->getEmail(),
            'name' => $customer->getName(),
            'password' => $customer->getPassword(),
            'price_rule_id' => $customer->getPriceRuleId(),
            'cuit' => $customer->getCuit(),
            'status' => $customer->getStatus(),
            'created' => date('Y-m-d H:i:s'),
        );
 
        if (null === ($id = $customer->getId())) {
            unset($data['customer_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('customer_id = ?' => $id));
        }
    }
 
    public function find($id, PAP_Model_Customer $customer)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $customer->setId($row->customer_id)
                  ->setEmail($row->email)
                  ->setName($row->name)
                  ->setPassword($row->password)
                  ->setPriceRuleId($row->price_rule_id)
                  ->setCuit($row->cuit)
                  ->setStatus($row->status)
                  ->setCreated($row->created);
    }
    
    public function existByEmail($param){
        $db = Zend_Db_Table::getDefaultAdapter();
        $email = $db->quote($param);
        $statement = "SELECT COUNT(customer_id) AS total FROM customer WHERE email = " .$email;
        $results = $db->fetchOne($statement);
        if($results == 1)
            return true;
        return false;         
    }
    
    public function getIdByEmail($email){
        $db = Zend_Db_Table::getDefaultAdapter();
        $email = $db->quote($email);
        $statement = "SELECT customer_id FROM customer WHERE email = " .$email;
        $results = $db->fetchOne($statement);
        return $results;      
    }
    
    public function loadByEmail($email, PAP_Model_Customer $customer){
        $customer_id = $this->getIdByEmail($email);
        if(isset($customer_id))
            $this->find($customer_id, $customer);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new PAP_Model_Customer();
            $entry->setId($row->customer_id)
                  ->setEmail($row->email)
                  ->setName($row->name)
                  ->setPassword($row->password)
                  ->setPriceRuleId($row->price_rule_id)
                  ->setCuit($row->cuit)
                  ->setStatus($row->status)
                  ->setCreated($row->created);
            $entries[] = $entry;
        }
        return $entries;
    }

}

