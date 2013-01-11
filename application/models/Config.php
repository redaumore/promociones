<?php
  
Class PAP_Model_Config{
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
            $this->setDbTable('PAP_Model_DbTable_Config');
        }
        return $this->_dbTable;
    }
    
    public function getCurrentRegionVersion(){
        $select =  $this->getDbTable()->select();
        $select->where('element = ?', 'regions');
       $result = $this->getDbTable()->fetchAll($select);
        if (0 == count($result)) {
            return 0;
        }
        $version = $result[0]->version;
        return $version;
    }
    
    public function getRegions($from, $to){
        $data = array();
        $provinceTable = new PAP_Model_DbTable_Province();
        $query = $provinceTable->select();
        $query->where('version > ? ', $from);
        $query->where('version <= ? ', $to);
        $query->where('status = ?', 'A');
        $result = $provinceTable->fetchAll($query);
        $data = array();
        foreach($result as $row) {
            $data["province"][] = array("province_id"=>$row->province_id, "name"=>$row->name);
        }
        

        $cityTable = new PAP_Model_DbTable_City();
        $query = $cityTable->select();
        $query->where('version > ? ', $from);
        $query->where('version <= ? ', $to);
        $query->where('status = ?', 'A');
        $result = $cityTable->fetchAll($query);
        foreach ($result as $row) {
            $data["city"][] = array("city_id"=>$row->city_id, "name"=>$row->name, "latitude"=>$row->latitude, "longitude"=>$row->longitude, "province_id"=>$row->province_id);
        }
        return $data;
    }
}