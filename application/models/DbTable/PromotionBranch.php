<?php
class PAP_Model_DbTable_PromotionBranch extends Zend_Db_Table_Abstract
{
    protected $_name = 'promotion_branch';
    
    protected $_referenceMap    = array(
        'promotion' => array(
            'columns'           => array('promotion_id'),
            'refTableClass'     => 'PAP_Model_DbTable_Category',
            'refColumns'        => array('promotion_id')
        ),
        'branch' => array(
            'columns'           => array('branch_id'),
            'refTableClass'     => 'PAP_Model_DbTable_User',
            'refColumns'        => array('branch_id')
        )
    );


}
