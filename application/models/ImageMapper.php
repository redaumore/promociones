<?php

class PAP_Model_ImageMapper
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
            $this->setDbTable('PAP_Model_DbTable_Image');
        }
        return $this->_dbTable;
    }
 
    public function save(PAP_Model_Image $image)
    {
        $data = array(
            'parent_id'   => $image->getParentId(),
            'parent_type' => $image->getParentType(),
            'path' => $image->getPath(),
            'created' => date('Y-m-d H:i:s'),
        );
 
        if (null === ($id = $image->getId())) {
            unset($data['image_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('image_id = ?' => $id));
        }
    }
 
    public function find($id, PAP_Model_Image $image)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $image->setId($row->image_id)
                  ->setParentId($row->parent_id)
                  ->setParentType($row->parent_type)
                  ->setPath($row->path)
                  ->setCreated($row->created);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new PAP_Model_Image();
            $entry->setId($row->image_id)
                  ->setParentId($row->parent_id)
                  ->setParentType($row->parent_type)
                  ->setPath($row->path)
                  ->setCreated($row->created);
            $entries[] = $entry;
        }
        return $entries;
    }

}

