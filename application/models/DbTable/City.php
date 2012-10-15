<?php

class PAP_Model_DbTable_City extends Zend_Db_Table_Abstract
{

    protected $_name = 'city';

    public function db(){
        return $this->_db;
    }
}

