<?php

class PAP_Model_BranchMapper
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
            $this->setDbTable('PAP_Model_DbTable_Branch');
        }
        return $this->_dbTable;
    }
 
    public function save(PAP_Model_Branch $branch)
    {
        $data = array(
            'user_id'   => $branch->getUser(),
            'branch_order' => $branch->getBranchorder(),
            'branch_email' => $branch->getEmail(),
            'branch_website' => $branch->getWebsite(),
            'latitude' => $branch->getLatitude(),
            'longitude' => $branch->getLongitude(),
            'name' => $branch->getName(),
            'street' => $branch->getStreet(),
            'number' => $branch->getNumber(),
            'phone' => $branch->getPhone(),
            'local' => $branch->getLocal(),
            'logo' => $branch->getLogo(),
            'province_id' => $branch->getProvince(),
            'city_id' => $branch->getCity(),
            'zip_code' => $branch->getZipcode(),
            'created' => date('Y-m-d H:i:s'),
            'updated' => date('Y-m-d H:i:s'),
        );
 
        if (null === ($id = $branch->getId())) {
            unset($data['branch_id']);
            $this->getDbTable()->insert($data);
            $last_id = $this->getDbTable()->getAdapter()->lastInsertId();
            $branch->setId($last_id);
        } else {
            $this->getDbTable()->update($data, array('branch_id = ?' => $id));
        }
    }
 
    public function find($id, PAP_Model_Branch $branch)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $this->load($row, $branch);
    }
    
    public function findByUserId($user, $branchOrder = 0, PAP_Model_Branch $branch){
        $select = $this->getDbTable()->select();
        $select->where('user_id = ?', $user->getId());
        $select->where('branch_order = ?', $branchOrder);
        $result = $this->getDbTable()->fetchAll($select);
        if (0 == count($result)) {
            return;
        }
        $this->load($result->current(), $branch);
    }
    
    public function findAllByUserId($user){
        $branches = array();
        $select = $this->getDbTable()->select();
        $select->where('user_id = ?', $user->getId());
        $result = $this->getDbTable()->fetchAll($select);
        foreach($result as $row){
            $branch = new PAP_Model_Branch();
            $this->load($row, $branch);
            $branches[] = $branch;
        }
        return $branches;
    }
    
    private function load($row, PAP_Model_Branch $branch){
        $branch->setId($row->branch_id)
                  ->setUser($row->user_id)
                  ->setBranchorder($row->branch_order)
                  ->setEmail($row->branch_email)
                  ->setWebsite($row->branch_website)
                  ->setLatitude($row->latitude)
                  ->setLongitude($row->longitude)
                  ->setName($row->name)
                  ->setStreet($row->street)
                  ->setNumber($row->number)
                  ->setPhone($row->phone)
                  ->setLocal($row->local)
                  ->setLogo($row->logo)
                  ->setProvince($row->province_id)
                  ->setCity($row->city_id)
                  ->setZipcode($row->zip_code)
                  ->setCreated($row->created)
                  ->setUpdated($row->updated);
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new PAP_Model_Branch();
            $entry->setId($row->branch_id)
                  ->setUser($row->user_id)
                  ->setBranchorder($row->branch_order)
                  ->setEmail($row->branch_email)
                  ->setWebsite($row->branch_website)
                  ->setLatitude($row->latitude)
                  ->setLongitude($row->longitude)
                  ->setName($row->name)
                  ->setStreet($row->street)
                  ->setNumber($row->number)
                  ->setPhone($row->phone)
                  ->setLocal($row->local)
                  ->setLogo($row->logo)
                  ->setProvince($row->province_id)
                  ->setCity($row->city_id)
                  ->setZipcode($row->zip_code)
                  ->setCreated($row->created)
                  ->setUpdated($row->updated);
            $entries[] = $entry;
        }
        return $entries;
    }
    
     public function getBranchesByRange($latE, $latO, $lngN, $lngS){
        $branches = array();
        $select = $this->getDbTable()->select();
        $select->where('latitude > ?', $latO)
                ->where('latitude < ?', $latE)
                ->where('longitude > ?', $lngS)
                ->where('longitude < ?', $lngN);
        $result = $this->getDbTable()->fetchAll($select);
        foreach($result as $row){
            $branch = new PAP_Model_Branch();
            $this->load($row, $branch);
            $branches[] = $branch;
        }
        return $branches;
     }
}

