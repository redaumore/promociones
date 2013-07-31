<?php

class PAP_Model_Province
{
    protected $_id;
    protected $_name;
    
    public function loadById($id){
        $mapper = new PAP_Model_ProvinceMapper();
        $mapper->find($id, $this);    
    }
     public function setId($text){
        $this->_id = (string) $text;
        return $this;
    }
    public function getId(){
        return $this->_id;
    }
    
     public function setName($text){
        $this->_name = (string) $text;
        return $this;
    }
    public function getName(){
        return $this->_name;
    }
    
    public function setStatus($text){
        $this->_status = (string) $text;
        return $this;
    }
    public function getStatus(){
        return $this->_status;
    }
}

