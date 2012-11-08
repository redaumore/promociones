<?php

class PAP_Model_PromotionMapper
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
            $this->setDbTable('PAP_Model_DbTable_Promotion');
        }
        return $this->_dbTable;
    }
 
    public function save(PAP_Model_Promotion $promotion, $branches)
    {
        $startsDate = strtotime( $promotion->getStarts());
        $startsDate = date( 'Y-m-d H:i:s', $startsDate );
        $endsDate = strtotime( $promotion->getEnds());
        $endsDate = date( 'Y-m-d H:i:s', $endsDate );
        
        $data = array(
            'promo_code'   => $promotion->getPromoCode(),
            'user_id' => $promotion->getUserId(),
            'starts' => $startsDate,
            'ends' => $endsDate,
            'short_description' => $promotion->getShortDescription(),
            'long_description' => $promotion->getLongDescription(),
            'promo_value' => $promotion->getPromoValue(),
            'promo_type' => $promotion->getPromoType(),
            'displayed_text' => $promotion->getDisplayedText(),
            'alert_type' => $promotion->getAlertType(),
            'state' => $promotion->getState(),
            'promo_cost' => $promotion->getPromoCost(),
            //'visited' => $promotion->getVisited(),
            //'updated' => date('Y-m-d H:i:s'),
        );
        $data['quantity'] = ($promotion->getQuantity() == '')? null:$promotion->getQuantity();
                                                                                   
        if (null === ($id = $promotion->getId())) {
            unset($data['promotion_id']);
            $data['created'] = date('Y-m-d H:i:s');
            $id = $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('promotion_id = ?' => $id));
        }
        
        $this->relateBranches($id, $branches);
        
        return $id;
    }
    
    private function relateBranches($promotion_id, $branches){
        $promotionBrancheTable = new PAP_Model_DbTable_PromotionBranch();
        
        $where[] = $promotionBrancheTable->getAdapter()->quoteInto('promotion_id = ?', $promotion_id);
        $promotionBrancheTable->delete($where);
        
        for($i = 0; $i < count($branches); ++$i) {
           $row = array(
                'branch_id' => $branches[$i],
                'promotion_id' => $promotion_id,
           );
           $promotionBrancheTable->insert($row);
        }
    }
 
    public function delete(PAP_Model_Promotion $promotion)
    {
        $table = new PAP_Model_DbTable_Image();
        $where[] = $table->getAdapter()->quoteInto('parent_id = ?', $promotion->getId());
        $where[] = $table->getAdapter()->quoteInto('parent_type = ?', 'P');
        $table->delete($where);   
        $where = null;
                            
        $table = $this->getDbTable();
        $where = $table->getAdapter()->quoteInto('promotion_id = ?', $promotion->getId());
        $table->delete($where);   
    }
    
    public function find($id, PAP_Model_Promotion $promotion)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        
        $promotion->setId($row->promotion_id)
                  ->setPromoCode($row->promo_code)
                  ->setUserId($row->user_id)
                  ->setStarts($this->getFormatedStringDate($row->starts))
                  ->setEnds($this->getFormatedStringDate($row->ends))
                  ->setShortDescription($row->short_description)        
                  ->setLongDescription($row->long_description)
                  ->setPromoValue($row->promo_value)
                  ->setQuantity($row->quantity)
                  ->setPromoType($row->promo_type)
                  ->setDisplayedText($row->displayed_text)
                  ->setAlertType($row->alert_type)
                  ->setState($row->state)
                  ->setPromoCost($row->promo_cost)
                  ->setVisited($row->visited)
                  ->setCreated($row->created);
                  
         $imageTable = new PAP_Model_DbTable_Image();
         $select = $imageTable->select();
         $select->where('parent_id = ?', $id);
         $select->where('parent_type = "P"');
         $images = $imageTable->fetchAll($select);
         $oimages = array();
         foreach($images as $img){
            $oimages[] = new PAP_Model_Image($img['path']);    
         }
         $promotion->setImages($oimages);
    }
 
    public function fetchAll($userId = null)
    {
        if(isset($userId)){
            $select = $this->getDbTable()->select();
            $select->where("user_id = ?", $userId);
            $resultSet = $this->getDbTable()->fetchAll($select);
        }
        else{
            $resultSet = $this->getDbTable()->fetchAll();
        }
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new PAP_Model_Branch();
            $entry->setId($row->promotion_id)
                  ->setPromoCode($row->promo_code)
                  ->setUserId($row->user_id)
                  ->setStarts($row->starts)
                  ->setEnds($row->ends)
                  ->setShortDescription($row->short_description)        
                  ->setLongDescription($row->long_description)
                  ->setPromoValue($row->promo_value)
                  ->setQuantity($row->quantity)
                  ->setPromoType($row->type)
                  ->setDisplayedText($row->displayed_text)
                  ->setAlertType($row->alert_type)
                  ->setState($row->state)
                  ->setPromoCost($row->promo_cost)
                  ->setVisited($row->visited)
                  ->setCreated($row->created);
            $entries[] = $entry;
        }
        return $entries;
    }
    
    public function getByUserId($user_id, $colord, $ord, $start, $limit)
    {
        $select = $this->getDbTable()->select();
        $select->distinct()
               ->where('user_id = ?', $user_id)
               ->order($colord, $ord)
               ->limit($limit,$start);
        $result = $this->getDbTable()->fetchAll($select);
        $i=0;
        foreach ($result as $r) {
            $r->starts = $this->getFormatedStringDate($r->starts);
            $r->ends = $this->getFormatedStringDate($r->ends);
        }
        return $result;
    }
    
    public function getByLocation($lat, $long){
        
    }
    
    public function countPromos($user_id)
    {
        $select = $this->getDbTable()->select();
        $select->distinct()
            ->where('user_id = ?', $user_id);
        $rowset = $select->query()->fetchAll();
        return count($rowset);        
    }
    
    public function setImages(PAP_Model_Promotion $promo, $images)
    {
        $this->deleteAllImages($promo);
        $imageTable = new PAP_Model_DbTable_Image();
        for($i = 0; $i < count($images); ++$i) {
           $row = array(
                'path' => $images[$i],
                'parent_id' => $promo->getId(),
                'parent_type' => 'P',
           );
           $image[$i]=$imageTable->insert($row);
        }
        $oimages = array();
        foreach($images as $img){
            $oimages[] = new PAP_Model_Image($img);    
         }
        $promo->setImages($oimages);
    }
    
    public function deleteAllImages(PAP_Model_Promotion $promo)
    {
         $imageTable = new PAP_Model_DbTable_Image();
         $where[] = $imageTable->getAdapter()->quoteInto('parent_id = ?', $promo->getId());
         $where[] = $imageTable->getAdapter()->quoteInto('parent_type = ?', 'P');
         $imageTable->delete($where);
    }
    
    public function loadImages(PAP_Model_Promotion $promo)
    {
        $imageTable = new PAP_Model_DbTable_Image();
        $images = array();
        $select = $imageTable->select();
        $select->where('parent_id = ?', $promo->getId())
                ->where('parent_type = ?', 'P');
               
        $result = $imageTable->fetchAll($select);
        $i=0;
        foreach ($result as $r) {
            $images[] = new PAP_Model_Image($r->path);
        }
        $promo->setImages($images);
    }
    
    private function getFormatedStringDate($date)
    {
        $dStarts = new DateTime($date);
        return $dStarts->format("d/m/Y");    
    }
    
    public function getPromotionsByBranches($branches){
        $in = '';
        $select = $this->getDbTable()->select();
        $select->where('starts >= ?', date('Y-m-d'));
        foreach($branches as $branch){
            $in .= $branch->getId().',';
        }
        $in = '('.substr($in, 0, strlen($in)-1).')';
        
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $statement = "SELECT DISTINCT p.* FROM promotion p ".
                     "INNER JOIN promotion_branch pb ON pb.promotion_id = p.promotion_id ".
                     "WHERE p.starts >= ".date('Y-m-d')." AND pb.branch_id IN ".$in;
        $results = $adapter->fetchAll($statement);
        return $results;
        
        /*PROBLEMA AL ARMAR EL "IN" $select->where('starts <= ?', date('Y-m-d'));
        $select->where('branch_id IN ?', $in);
        $rowset = $select->query()->fetchAll();
        return $rowset;    */
    }
    


}

