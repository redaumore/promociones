<?php

class PAP_Model_User
{
    protected $_user_id;
    protected $_email;
    protected $_name;
    protected $_password;
    protected $_price_rule_id;
    protected $_cuit;
    protected $_rol;
    protected $_created;
    protected $_status;
    
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
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
    
    public function checkUnique($email){
        $userMapper = new PAP_Model_UserMapper();
        return $userMapper->existByEmail($email);
    }
    
    public function loadByEmail($email){
        $userMapper = new PAP_Model_UserMapper();
        $userMapper->loadByEmail($email, $this);
    }
    
    public function insert(array $options){
        
        $this->setOptions($options);
        $userMapper = new PAP_Model_UserMapper();
        $userMapper->save($this);
    }
    
    public function update(){
        $userMapper = new PAP_Model_UserMapper();
        $userMapper->save($this);    
    }
 
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
    
    public function getPriceRulesItems(){
        $mapper = new PAP_Model_UserMapper();
        $rules = $mapper->getPriceRulesItems($this->getPriceRuleId()); 
        return $rules;   
    }
    
    public function getBranch($branch_order = 0){
        $branch = new PAP_Model_Branch();
        $branchMapper = new PAP_Model_BranchMapper();
        $branchMapper->findByUserId($this, $branch_order, $branch);
        return $branch;
    }
    
    public function getCategories(){
        $userMap = new PAP_Model_UserMapper();
        $categories = $userMap->getCategories($this);
        return $categories;
    }
    
    public function setCategories($categories){
        $userMap = new PAP_Model_UserMapper();
        $categories = $userMap->setCategories($this, $categories);
        return;    
    }
 
    public function setId($text)
    {
        $this->_user_id = (string) $text;
        return $this;
    }
 
    public function getId()
    {
        return $this->_user_id;
    }
    
    public function setEmail($text)
    {
        $this->_email = (string) $text;
        return $this;
    }
 
    public function getEmail()
    {
        return $this->_email;
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
    
    public function setPassword($text)
    {
        $this->_password = (string) $text;
        return $this;
    }
 
    public function getPassword()
    {
        return $this->_password;
    }
    
    public function setPriceRuleId($text)
    {
        $this->_price_rule_id = (string) $text;
        return $this;
    }
 
    public function getPriceRuleId()
    {
        return $this->_price_rule_id;
    }
    
    public function setCuit($text)
    {
        $this->_cuit = (string) $text;
        return $this;
    }
 
    public function getCuit()
    {
        return $this->_cuit;
    }
    
    public function setCreated($text)
    {
        $this->_created = (string) $text;
        return $this;
    }
 
    public function getCreated()
    {
        return $this->_created;
    }
    
    public function setStatus($text)
    {
        $this->_status = (string) $text;
        return $this;
    }
 
    public function getStatus()
    {
        return $this->_status;
    }
    
    public function setRol($text)
    {
        $this->_rol = (string) $text;
        return $this;
    }
 
    public function getRol()
    {
        return $this->_rol;
    }
}

