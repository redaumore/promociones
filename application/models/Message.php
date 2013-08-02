<?php
class PAP_Model_Message
{
     protected $_message_id;
     protected $_ip;
     protected $_ip_number;
     protected $_message;
     protected $_email;
     protected $_name;
     protected $_message_type;
     protected $_location;

     
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    public function save(){
        $mapper = new PAP_Model_MessageMapper();
        $mapper->save($this);
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
    
     public function setOptions(array $options){
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods))
                $this->$method($value);
        }
        return $this;
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
    
    public function setEmail($text)
    {
        $this->_email = (string) $text;
        return $this;
    }
    public function getEmail()
    {
        return $this->_email;
    }
    
    public function setId($text)
    {
        $this->_message_id = (string) $text;
        return $this;
    }
    public function getId()
    {
        return $this->_message_id;
    }
    
    public function setIp($text)
    {
        $this->_ip = (string) $text;
        return $this;
    }
    public function getIp()
    {
        return $this->_ip;
    }
    
    public function setLocation($text)
    {
        $this->_location = (string) $text;
        return $this;
    }
    public function getLocation()
    {
        return $this->_location;
    }
    
    public function setIpNumber($text)
    {
        $this->_ip_number = (string) $text;
        return $this;
    }
    public function getIpNumber()
    {
        return $this->_ip_number;
    }
    
    public function setMessage($text)
    {
        $this->_message = (string) $text;
        return $this;
    }
    public function getMessage()
    {
        return $this->_message;
    }
    
    public function setMessageType($text)
    {
        $this->_message_type = (string) $text;
        return $this;
    }
    public function getMessageType()
    {
        return $this->_message_type;
    }
}