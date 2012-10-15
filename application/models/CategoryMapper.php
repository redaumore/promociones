<?php

class PAP_Model_CategoryMapper
{
protected $_dbTable;
protected $_dependentTables = array('PAP_CateroryUserMapper');
 
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
            $this->setDbTable('PAP_Model_DbTable_Category');
        }
        return $this->_dbTable;
    }
 
    public function save(PAP_Model_Category $category)
    {
        $data = array(
            'category_id'   => $category->getId(),
            'name' => $category->getName(),
            'parent_id' => $category->getParentId(),
        );
 
        if (null === ($id = $category->getId())) {
            unset($data['category_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('category_id = ?' => $id));
        }
    }
 
    public function find($id, PAP_Model_Category $category)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $category->setId($row->category_id)
                  ->setName($row->name)
                  ->setParent($row->parent_id);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new PAP_Model_Category();
            $entry->setId($row->category_id)
                  ->setName($row->name)
                  ->setParentId($row->parent_id);
            $entries[] = $entry;
        }
        return $entries;
    }

}

