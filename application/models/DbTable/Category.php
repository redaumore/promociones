<?php

class PAP_Model_DbTable_Category extends Zend_Db_Table_Abstract
{

    protected $_name = 'category';

    protected $_dependentTables = array('PAP_Model_DbTable_CategoryUser');

}

