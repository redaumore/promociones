<?php

class PAP_Model_UserMapper
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
            $this->setDbTable('PAP_Model_DbTable_User');
        }
        return $this->_dbTable;
    }
 
    public function save(PAP_Model_user $user)
    {
        $data = array(
            'email'   => $user->getEmail(),
            'name' => $user->getName(),
            'password' => $user->getPassword(),
            'price_rule_id' => $user->getPriceRuleId(),
            'cuit' => $user->getCuit(),
            'rol' => $user->getRol(),
            'status' => $user->getStatus(),
            'created' => date('Y-m-d H:i:s'),
        );
 
        if (null === ($id = $user->getId())) {
            unset($data['user_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('user_id = ?' => $id));
        }
    }
 
    public function find($id, PAP_Model_user $user)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $user->setId($row->user_id)
                  ->setEmail($row->email)
                  ->setName($row->name)
                  ->setPassword($row->password)
                  ->setPriceRuleId($row->price_rule_id)
                  ->setCuit($row->cuit)
                  ->setStatus($row->status)
                  ->setRol($row->rol)
                  ->setCreated($row->created);
    }
    
    public function existByEmail($param){
        $db = Zend_Db_Table::getDefaultAdapter();
        $email = $db->quote($param);
        $statement = "SELECT COUNT(user_id) AS total FROM user WHERE email = " .$email;
        $results = $db->fetchOne($statement);
        if($results == 1)
            return true;
        return false;         
    }
    
    public function getIdByEmail($email){
        $db = Zend_Db_Table::getDefaultAdapter();
        $email = $db->quote($email);
        $statement = "SELECT user_id FROM user WHERE email = " .$email;
        $results = $db->fetchOne($statement);
        return $results;      
    }
    
    public function loadByEmail($email, PAP_Model_user $user){
        $user_id = $this->getIdByEmail($email);
        if($user_id){
            $this->find($user_id, $user);
            return true;    
        }
        
        return false;
            
    }
 
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new PAP_Model_user();
            $entry->setId($row->user_id)
                  ->setEmail($row->email)
                  ->setName($row->name)
                  ->setPassword($row->password)
                  ->setPriceRuleId($row->price_rule_id)
                  ->setCuit($row->cuit)
                  ->setStatus($row->status)
                  ->setRol($row->rol)
                  ->setCreated($row->created);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function getCategories(PAP_Model_User $user){
        $userRow = $this->getDbTable()->find($user->getId())->current();
        $categoriesRowset = $userRow->findManyToManyRowset('PAP_Model_DbTable_Category','PAP_Model_DbTable_CategoryUser');
        $categoriesArray = array();
        foreach($categoriesRowset as $row){
            $category = new PAP_Model_Category();
            $category->init($row->category_id);
            $categoriesArray[] = $category;
        }
        return $categoriesArray;
    }
    
    public function setCategories(PAP_Model_User $user, $categories){
        $this->deleteAllCategories($user);
        $categoryUserTable = new PAP_Model_DbTable_CategoryUser();
        for($i = 0; $i < count($categories); ++$i) {
           $row = array(
                'category_id' => $categories[$i],
                'user_id' => $user->getId(),
           );
           $categoryUserTable->insert($row);
        }
    }
    
    public function getPriceRulesItems($id){
        $priceRulesTable = new PAP_Model_DbTable_PriceRule();
        $select = $priceRulesTable->select()
                    ->where('price_rule_id = ?', $id);
        $result = $priceRulesTable->fetchAll($select);
        return $result;
    }
    
    private function deleteAllCategories(PAP_Model_User $user){
        $categoryUserTable = new PAP_Model_DbTable_CategoryUser();
        $where = 'user_id = '.$user->getId();;
        $categoryUserTable->delete($where);
    }
    
    

}

