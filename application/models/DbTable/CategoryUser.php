<?php
  class PAP_Model_DbTable_CategoryUser extends Zend_Db_Table_Abstract
{
    protected $_name = 'category_user';
    
    protected $_referenceMap    = array(
        'category' => array(
            'columns'           => array('category_id'),
            'refTableClass'     => 'PAP_Model_DbTable_Category',
            'refColumns'        => array('category_id')
        ),
        'user' => array(
            'columns'           => array('user_id'),
            'refTableClass'     => 'PAP_Model_DbTable_User',
            'refColumns'        => array('user_id')
        )
    );


}