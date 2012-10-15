<?php

class PAP_Model_DbTable_Image extends Zend_Db_Table_Abstract
{

    protected $_name = 'image';
    
    protected $_referenceMap    = array(
        'Promotion' => array(
            'columns'           => array('promotion_id'),
            'refTableClass'     => 'PAP_Model_DbTable_Promotion',
            'refColumns'        => array('promotion_id')
        )
    );


}

