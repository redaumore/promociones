<?php

class PAP_Model_PriceRuleMapper
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
            $this->setDbTable('PAP_Model_DbTable_PriceRule');
        }
        return $this->_dbTable;
    }
 
    public function save(PAP_Model_PriceRule $pricerule)
    {
        $data = array(
            'price_rule_code'   => $pricerule->getPriceRuleCode(),
            'value' => $pricerule->getValue(),
            'created' => date('Y-m-d H:i:s'),
        );
 
        if (null === ($id = $pricerule->getId())) {
            unset($data['price_rule_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('price_rule_id = ?' => $id));
        }
    }
 
    public function find($id, PAP_Model_PriceRule $pricerule)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $pricerule->setId($row->price_rule_idid)
                  ->setPriceRuleCode($row->price_rule_code)
                  ->setValue($row->value)
                  ->setCreated($row->created);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new PAP_Model_PriceRule();
            $entry->setId($row->price_rule_idid)
                  ->setPriceRuleCode($row->price_rule_code)
                  ->setValue($row->value)
                  ->setCreated($row->created);
            $entries[] = $entry;
        }
        return $entries;
    }

}

