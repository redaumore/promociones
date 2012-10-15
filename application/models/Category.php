<?php

class PAP_Model_Category
{
    protected $_category_id;
    protected $_name;
    protected $_parent_id;
    
    
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    public function init($id){
        if(isset($id))
            $this->loadById($id);    
    }
    
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid user property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid user property');
        }
        return $this->$method();
    }
    
    public function loadById($id){
        $categoryMapper = new PAP_Model_CategoryMapper();
        $categoryMapper->find($id, $this);
    }
    
    public function setId($text)
    {
        $this->_category_id = (string) $text;
        return $this;
    }
 
    public function getId()
    {
        return $this->_category_id;
    }
    
    public function setName($text)
    {
        $this->_name = (string) $text;
        return $this;
    }
 
    public function getName()
    {
        return $this->_name;
    }
    
    public function setParent($text)
    {
        $this->_parent_id = (string) $text;
        return $this;
    }
 
    public function getParent()
    {
        return $this->_parent_id;
    }

}

