<?php

class PAP_Model_Customer
{
    protected $_customer_id;
    protected $_email;
    protected $_name;
    protected $_password;
    protected $_price_rule_id;
    protected $_cuit;
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
            throw new Exception('Invalid customer property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid customer property');
        }
        return $this->$method();
    }
    
    public function checkUnique($email){
        $customerMapper = new PAP_Model_CustomerMapper();
        return $customerMapper->existByEmail($email);
    }
    
    public function loadByEmail($email){
        $customerMapper = new PAP_Model_CustomerMapper();
        $customerMapper->loadByEmail($email, $this);
    }
    
    public function insert(array $options){
        
        $this->setOptions($options);
        $customerMapper = new PAP_Model_CustomerMapper();
        $customerMapper->save($this);
    }
    
    public function update(){
        $customerMapper = new PAP_Model_CustomerMapper();
        $customerMapper->save($this);    
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
 
    public function setId($text)
    {
        $this->_customer_id = (string) $text;
        return $this;
    }
 
    public function getId()
    {
        return $this->_customer_id;
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

}

