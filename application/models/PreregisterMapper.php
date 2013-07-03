<?php
class PAP_Model_PreregisterMapper
{
    public function insert($email, $name){
        try{
            $data = array(
                'name'   => $name,
                'email' => $email,
                'created' => date('Y-m-d H:i:s'),
            );
            $this->getDbTable()->insert($data);
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PreregisterMapper->insert()',$ex, "name:".$name." email:".$email);
        }
    }
    
     public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('PAP_Model_DbTable_Preregister');
        }
        return $this->_dbTable;
    }
    
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
}