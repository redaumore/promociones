<?php

class PAP_Model_MessageMapper
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
            $this->setDbTable('PAP_Model_DbTable_Messages');
        }
        return $this->_dbTable;
    }
    
    public function save(PAP_Model_Message $message)
    {
        $data = array(
            'ip'   => $message->getIp(),
            'ip_number' => $message->getIpNumber(),
            'message' => $message->getMessage(),
            'email' => $message->getEmail(),
            'name' => $message->getName(),
            'location' => $message->getLocation(),
            'message_type' => $message->getMessageType(),
            'created' => date('Y-m-d H:i:s'),
        );
 
        if (null === ($id = $message->getId())) {
            unset($data['message_id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('message_id = ?' => $id));
        }
    }
    
    public function find($id, PAP_Model_Message $message)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $message->setId($row->message_id)
                  ->setIp($row->ip)
                  ->setIpNumber($row->ip_number)
                  ->setMessage($row->message)
                  ->setEmail($row->email)
                  ->setName($row->name)
                  ->setLocation($row->location)
                  ->setMessageType($row->type)
                  ->setCreated($row->created);
    }
    
}