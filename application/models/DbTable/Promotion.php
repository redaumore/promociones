<?php

class PAP_Model_DbTable_Promotion extends Zend_Db_Table_Abstract
{

    protected $_name = 'promotion';
    
    protected $_dependentTables = array('PAP_Model_DbTable_PromotionBranch');


}

