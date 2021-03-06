<?php

class PAP_Model_City
{
    protected $_name;
    protected $_id;
    protected $_province_id;
    protected $_latitude;
    protected $_longitude;
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
            throw new Exception('Invalid promotion property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid promotion property');
        }
        return $this->$method();
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
    
     public function setId($text){
        $this->_id = (string) $text;
        return $this;
    }
    public function getId(){
        return $this->_id;
    }
    
     public function setName($text){
        $this->_name = (string) $text;
        return $this;
    }
    public function getName(){
        return $this->_name;
    }
    
    public function setLatitude($text){
        $this->_latitude = (string) $text;
        return $this;
    }
    public function getLatitude(){
        return $this->_latitude;
    }
    
    public function setLongitude($text){
        $this->_longitude = (string) $text;
        return $this;
    }
    public function getLongitude(){
        return $this->_longitude;
    }
    
    public function setStatus($text){
        $this->_status = (string) $text;
        return $this;
    }
    public function getStatus(){
        return $this->_status;
    }
    
    public function setProvinceid($text){
        $this->_province_id = (string) $text;
        return $this;
    }
    public function getProvinceid(){
        return $this->_province_id;
    }

}

