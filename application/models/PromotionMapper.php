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
        $endsDate = strtotime( $promotion->getEnds().' 23:59:59');
        $endsDate = date( 'Y-m-d H:i:s', $endsDate);
        
        $data = array(
            'promo_code'   => $promotion->getPromoCode(),
            'user_id' => $promotion->getUserId(),
            'starts' => $startsDate,
            'ends' => $endsDate,
            'short_description' => $promotion->getShortDescription(),
            'long_description' => $promotion->getLongDescription(),
            'promo_value' => $promotion->getPromoValue(),
            'value_since' => $promotion->getValueSince(),
            'promo_type' => $promotion->getPromoType(),
            'displayed_text' => $promotion->getDisplayedText(),
            'alert_type' => $promotion->getAlertType(),
            'state' => $promotion->getState(),
            'is_percentage' => ($promotion->getIsPercentaje())?1:0,
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
    
    public function getBranches($promotion_id){
        $promotionBrancheTable = new PAP_Model_DbTable_PromotionBranch();
        $select = $promotionBrancheTable->select();
        $select->where('promotion_id = ?', $promotion_id);
        $rows = $promotionBrancheTable->fetchAll($select);
        $branches = array();
        $branchMapper = new PAP_Model_BranchMapper();
        foreach($rows as $row){
            $branch = new PAP_Model_Branch();
            $branchMapper->find($row['branch_id'], $branch);
            $branches[] = $branch;
        }
        return $branches;
        
    }
    
    public function getViewRecord($promotion_id){
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $statement = "SELECT DISTINCT b.name, b.street, b.number, b.logo, b.latitude, b.longitude, b.branch_website, b.branch_email, b.phone, c.name as city_name, p.*, i.path FROM promotion p ".
                     "INNER JOIN promotion_branch pb ON pb.promotion_id = p.promotion_id ".
                     "INNER JOIN branch b ON pb.branch_id = b.branch_id ".
                     "INNER JOIN city c ON b.city_id = c.city_id ".
                     "LEFT JOIN image i ON (p.promotion_id = i.parent_id AND i.parent_type = 'P') ".
                     "WHERE p.promotion_id = ".$promotion_id. " ";
                     
        $results = $adapter->fetchAll($statement);
        return $results;   
    }
 
     public static function getPromotionByPeriod($period, $user_id=null){
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $statement = "SELECT DISTINCT pro.user_id, pro.promo_cost, pro.starts, pro.ends, per.code ";
        $statement .= "FROM promotion pro, periods per ";
        $statement .= "WHERE (pro.starts <= per.date_to AND pro.ends >= per.date_from) AND per.code IN ('".$period."') ";
        $statement .= (isset($user_id))?" AND pro.user_id = 1 ":" ";
        $statement .= "ORDER BY pro.user_id, pro.promo_cost ";
                     
        $results = $adapter->fetchAll($statement);
        return $results;   
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
                  ->setValueSince($row->value_since)
                  ->setQuantity($row->quantity)
                  ->setPromoType($row->promo_type)
                  ->setDisplayedText($row->displayed_text)
                  ->setAlertType($row->alert_type)
                  ->setState($row->state)
                  ->setVisited($row->visited)
                  ->setIsPercentaje($row->is_percentage)
                  ->setPromoCost($row->promo_cost)
                  ->setCreated($row->created);
         /*         
         $imageTable = new PAP_Model_DbTable_Image();
         $select = $imageTable->select();
         $select->where('parent_id = ?', $id);
         $select->where('parent_type = "P"');
         $images = $imageTable->fetchAll($select);
         $oimages = array();
         foreach($images as $img){
            $oimages[] = new PAP_Model_Image($img['path']);    
         }
         $promotion->setImages($oimages);*/
         $promotion->loadImages();
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
            $entry = new PAP_Model_Promotion();
            $entry->setId($row->promotion_id)
                  ->setPromoCode($row->promo_code)
                  ->setUserId($row->user_id)
                  ->setStarts($row->starts)
                  ->setEnds($row->ends)
                  ->setShortDescription($row->short_description)        
                  ->setLongDescription($row->long_description)
                  ->setPromoValue($row->promo_value)
                  ->setValueSince($row->value_since)
                  ->setQuantity($row->quantity)
                  ->setPromoType($row->type)
                  ->setDisplayedText($row->displayed_text)
                  ->setAlertType($row->alert_type)
                  ->setState($row->state)
                  ->setPromoCost($row->promo_cost)
                  ->setVisited($row->visited)
                  ->setIsPercentaje($row->is_percentage)
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
    {   $images = $this->getImages($promo);
        if(!isset($images))
            return false;
        $promo->setImages($images);
        return true;
    }
    
    public function getImages(PAP_Model_Promotion $promo){
        return $this->getImagesByPromoId($promo->getId());    
    }
    
    public function getImagesByPromoId($id){
        $imageTable = new PAP_Model_DbTable_Image();
        $images = array();
        $select = $imageTable->select();
        $select->where('parent_id = ?', $id)
                ->where('parent_type = ?', 'P');
        $result = $imageTable->fetchAll($select);
        if(count($result)==0)
            return null;
        $i=0;
        foreach ($result as $r) {
            $images[] = new PAP_Model_Image($r->path);
        }
        return $images;    
    }
    
    private function getFormatedStringDate($date)
    {
        $dStarts = new DateTime($date);
        return $dStarts->format("d/m/Y");    
    }
    
    public function getPromotionsByBranches($branches, $categories = '', $limit = 0){
        $in = '';
        foreach($branches as $branch){
            $in .= $branch->getId().',';
        }
        $in = '('.substr($in, 0, strlen($in)-1).')';
        
        $incat = '';
        if($categories <> ''){
            $incat = implode(',', $categories);     
        }
        
        
        $adapter = Zend_Db_Table::getDefaultAdapter();
        //$statement = (($origin=='')?"SELECT DISTINCT b.name, b.latitude, b.longitude, p.*, i.path FROM promotion p ":
        //$statement = "SELECT DISTINCT b.name, b.latitude, b.longitude, p.*, i.path FROM promotion p ".
        $statement = "SELECT DISTINCT b.name, b.logo, b.latitude, b.longitude, b.street, b.number, b.branch_website, b.branch_email, b.phone, c.name as city, p.short_description, p.displayed_text, p.promotion_id, p.promo_value, p.promo_cost, i.path, p.value_since, p.is_percentage FROM promotion p ".
                     "INNER JOIN promotion_branch pb ON pb.promotion_id = p.promotion_id ".
                     "INNER JOIN branch b ON pb.branch_id = b.branch_id ".
                     "INNER JOIN city c ON b.city_id = c.city_id ".
                     "LEFT JOIN image i ON (p.promotion_id = i.parent_id AND i.parent_type = 'P') ".
                     (($categories == '')?'':"INNER JOIN category_user cu ON (b.user_id = cu.user_id) ").
                     "WHERE p.starts <= '".date('Y-m-d')."' AND p.ends >= '".date('Y-m-d')."' AND pb.branch_id IN ".$in. " ".
                     (($incat == '')?'':"AND cu.category_id IN (".$incat.") ").
                     "ORDER BY p.promo_cost DESC ".(($limit == 0)?'':"LIMIT ".$limit." ");
                     
        $results = $adapter->fetchAll($statement);
        return $results;
    }
    
    public function getPromotionsByIds($ids){
        $in = '';
        $adapter = Zend_Db_Table::getDefaultAdapter();
        //$statement = (($origin=='')?"SELECT DISTINCT b.name, b.latitude, b.longitude, p.*, i.path FROM promotion p ":
        //$statement = "SELECT DISTINCT b.name, b.latitude, b.longitude, p.*, i.path FROM promotion p ".
        $statement = "SELECT DISTINCT b.name, b.logo, b.latitude, b.longitude, b.street, b.number, b.branch_website, b.branch_email, b.phone, c.name as city, p.short_description, p.displayed_text, p.promotion_id, p.promo_value, p.promo_cost, i.path, p.value_since, p.is_percentage FROM promotion p ".
                     "INNER JOIN promotion_branch pb ON pb.promotion_id = p.promotion_id ".
                     "INNER JOIN branch b ON pb.branch_id = b.branch_id ".
                     "INNER JOIN city c ON b.city_id = c.city_id ".
                     "LEFT JOIN image i ON (p.promotion_id = i.parent_id AND i.parent_type = 'P') ".
                     "WHERE p.state = 'A' AND p.starts <= '".date('Y-m-d')."' AND p.ends >= '".date('Y-m-d')."' AND p.promotion_id IN (".$ids.")";
        $results = $adapter->fetchAll($statement);
        return $results;
    }
    
    public function getPromotionById($promotion_id){
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $statement = "SELECT DISTINCT b.name, b.logo, b.latitude, b.longitude, b.street, b.number, b.phone, b.local, b.logo, b.branch_id, b.branch_website, b.branch_email, b.phone, c.name as city, p.*, i.path FROM promotion p ".
                     "INNER JOIN promotion_branch pb ON pb.promotion_id = p.promotion_id ".
                     "INNER JOIN branch b ON pb.branch_id = b.branch_id ".
                     "INNER JOIN city c ON b.city_id = c.city_id ".
                     "LEFT JOIN image i ON (p.promotion_id = i.parent_id AND i.parent_type = 'P') ".
                     "WHERE p.promotion_id = ".$promotion_id;
        $results = $adapter->fetchAll($statement);
        if(isset($results))
             $this->getDbTable()->update(array('visited' => new Zend_Db_Expr( 'visited+1' ) ), '`promotion_id` ='.$results[0]['promotion_id']);
        return $results;    
    }
    
    public function getPromotionByDates($from, $to, $user_id = 0){
        $adapter = Zend_Db_Table::getDefaultAdapter();
        $statement = "SELECT p.user_id, p.promo_cost, p.starts, p.ends ".
                      "FROM promotion AS p ".
                      "WHERE p.ends > str_to_date('".$from."','%Y-%m-%d') AND p.starts < str_to_date('".$to."','%Y-%m-%d')".(($user_id == 0)?" ":" AND p.user_id = ".$user_id)." ". 
                      "ORDER BY p.user_id, p.promo_cost;";
        $results = $adapter->fetchAll($statement);
        return $results;
    }
}

