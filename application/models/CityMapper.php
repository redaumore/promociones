<?php

class PAP_Model_CityMapper
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
            $this->setDbTable('PAP_Model_DbTable_City');
        }
        return $this->_dbTable;
    }
 
    public function save(PAP_Model_City $city)
    {
        $data = array(
            'name'   => $city->getName(),
            'province_id' => $city->getProvinceId(),
          );
 
        if (null === ($id = $city->getId())) {
            unset($data['city_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('city_id = ?' => $id));
        }
    }
 
    public function find($id, PAP_Model_City $city)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $city->setId($row->city_id)
                  ->setName($row->name)
                  ->setProvinceId($row->province_id)
                  ->setLatitude($row->latitude)
                  ->setLongitude($row->longitude);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new PAP_Model_City();
            $entry->setId($row->city_id)
                  ->setName($row->name)
                  ->setProvinceId($row->province_id);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function getCitiesByProvinceId($province_id){
        $CityTable = $this->getDbTable();
        $select = $CityTable->db()->select()
                            ->from('city')
                            ->where('province_id = ?', $province_id)
                            ->where('status = ?', 'A')
                            ->order('name');
        $result = $CityTable->getAdapter()->fetchAll($select);
        return $result;
    }
    
    /*public function findForSelect(){
        $db = Zend_Db_Table::getDefaultAdapter();
        $email = $db->quote($param);
        $statement = "SELECT province_id, name FROM province ORDER BY name";
        $results = $db->fetchAll($statement);
        return $results;
    } */

}

