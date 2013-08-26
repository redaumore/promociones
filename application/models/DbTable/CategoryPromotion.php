<?php
  class PAP_Model_DbTable_CategoryPromotion extends Zend_Db_Table_Abstract
{
    protected $_name = 'category_promotion';
    
    protected $_referenceMap    = array(
        'category' => array(
            'columns'           => array('category_id'),
            'refTableClass'     => 'PAP_Model_DbTable_Category',
            'refColumns'        => array('category_id')
        ),
        'promotion' => array(
            'columns'           => array('promotion_id'),
            'refTableClass'     => 'PAP_Model_DbTable_User',
            'refColumns'        => array('promotion_id')
        )
    );


}