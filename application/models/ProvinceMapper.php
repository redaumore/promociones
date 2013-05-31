<?php

class PAP_Model_ProvinceMapper
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
            $this->setDbTable('PAP_Model_DbTable_Province');
        }
        return $this->_dbTable;
    }
    
    public function findForSelect(){
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $statement = "SELECT DISTINCT p.* FROM province p INNER JOIN city c ON (p.province_id = c.province_id) WHERE c.status = 'A' ORDER BY name";
        $results = $adapter->fetchAll($statement);
        return $results;
    }
 
    public function save(PAP_Model_Province $province)
    {
        $data = array(
            'name'   => $province->getName()
            
        );
 
        if (null === ($id = $province->getId())) {
            unset($data['province_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('province_id = ?' => $id));
        }
    }
 
    public function find($id, PAP_Model_Province $province)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $province->setId($row->province_id)
                  ->setName($row->name);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new PAP_Model_Branch();
            $entry->setId($row->province_id)
                  ->setName($row->name);
            $entries[] = $entry;
        }
        return $entries;
    }
}

