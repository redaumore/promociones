<?php

class PAP_Model_PeriodMapper
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
            $this->setDbTable('PAP_Model_DbTable_Period');
        }
        return $this->_dbTable;
    }
    
    public function getPeriods($dates){
        $periods = array();
        $date_from = $dates[0];
        $date_to = $dates[1];
        $select = $this->getDbTable()->select();
        $select->where('date_from <= ?', $date_from);
        $select->where('date_to >= ?', $date_to);
        $result = $this->getDbTable()->fetchAll($select);
        foreach($result as $period){
            $per = new PAP_Model_Period();
            $per->setId($period->period_id)
                ->setFrom($period->date_from)
                ->setTo($period->date_to)
                ->setCode($period->code);
            $periods[] = $per;        
        }
        return $periods;        
    }
    
    public function getPeriodsByIds($ids){
        //$strids = implode(",", $ids);
        $select = $this->getDbTable()->select();
        $select->where('period_id IN (?)', $ids);
        $result = $this->getDbTable()->fetchAll($select);
        foreach($result as $period){
            $per = new PAP_Model_Period();
            $per->setId($period->period_id)
                ->setFrom($period->date_from)
                ->setTo($period->date_to)
                ->setCode($period->code);
            $periods[] = $per;        
        }
        return $periods;        
        
        
    }    
    
 
}