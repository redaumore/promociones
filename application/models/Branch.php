<?php

class PAP_Model_Branch
{
    protected $_branch_id;
    protected $_user_id;
    protected $_branch_order;
    protected $_branch_email;
    protected $_branch_website;
    protected $_latitude;
    protected $_longitude;
    protected $_name;
    protected $_street;
    protected $_number;
    protected $_phone;
    protected $_local;
    protected $_logo;
    protected $_province_id;
    protected $_city_id;
    protected $_zip_code;
    protected $_created;
    protected $_updated;
    
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
            throw new Exception('Invalid branch property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid branch property');
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
    
    public function insert(array $options){
        
        $this->setOptions($options);
        $branchMapper = new PAP_Model_BranchMapper();
        $branchMapper->save($this);
    }
    
    public function getCategories(){
        
    }
    
    public function getAddress(){
        $address = new PAP_Model_Address();
        $address->setStreet($this->_street);
        $address->setNumber($this->_number);
        $address->setPhone($this->_phone);
        $address->setOthers($this->_local);
        $address->setProvince($this->_province_id);
        $address->setCity($this->_city_id);
        $address->setLatitude($this->_latitude);
        $address->setLongitude($this->_longitude);
        $address->setZipCode($this->_zip_code);
        return $address;
    }
    
    public static function getBranchesByRange($latE, $latO, $lngN, $lngS){
        $mapper = new PAP_Model_BranchMapper();
        $branches = $mapper->getBranchesByRange($latE, $latO, $lngN, $lngS);
        return $branches;
    }
    /* PROPERTIES */
 
    public function setId($text)
    {
        $this->_branch_id = (string) $text;
        return $this;
    }
 
    public function getId()
    {
        return $this->_branch_id;
    }
    
    public function setUser($text)
    {
        $this->_user_id = (string) $text;
        return $this;
    }
 
    public function getUser()
    {
        return $this->_user_id;
    }
    
    public function setBranchorder($text)
    {
        $this->_branch_order = (string) $text;
        return $this;
    }
 
    public function getBranchorder()
    {
        return $this->_branch_order;
    }
    
    public function setLatitude($text)
    {
        $this->_latitude = (string) $text;
        return $this;
    }
 
    public function getLatitude()
    {
        return $this->_latitude;
    }
    
    public function setLongitude($text)
    {
        $this->_longitude = (string) $text;
        return $this;
    }
 
    public function getLongitude()
    {
        return $this->_longitude;
    }
    
    public function setName($text)
    {
        $this->_name = (string) $text;
        return $this;
    }
 
    public function getName()
    {
        return utf8_encode($this->_name);
    }
    
    public function setStreet($text)
    {
        $this->_street = (string) $text;
        return $this;
    }
 
    public function getStreet()
    {
        return utf8_encode($this->_street);
        //return $this->_street;
    }
    
    public function setNumber($text)
    {
        $this->_number = (string) $text;
        return $this;
    }
 
    public function getNumber()
    {
        return $this->_number;
    }
    
    public function setLocal($text)
    {
        $this->_local = (string) $text;
        return $this;
    }
 
    public function getLocal()
    {
        return utf8_encode($this->_local);
    }
    
    public function setProvince($text)
    {
        $this->_province_id = (string) $text;
        return $this;
    }
 
    public function getProvince()
    {
        return $this->_province_id;
    }
    
    public function setCity($text)
    {
        $this->_city_id = (string) $text;
        return $this;
    }
 
    public function getCity()
    {
        return $this->_city_id;
    }
    
    public function setZipcode($text)
    {
        $this->_zip_code = (string) $text;
        return $this;
    }
 
    public function getZipcode()
    {
        return $this->_zip_code;
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
    
    public function setUpdated($text)
    {
        $this->_updated = (string) $text;
        return $this;
    }
 
    public function getUpdated()
    {
        return $this->_updated;
    }
    
    public function setEmail($text)
    {
        $this->_branch_email = (string) $text;
        return $this;
    }
 
    public function getEmail()
    {
        return $this->_branch_email;
    }
    
    public function setWebsite($text)
    {
        $this->_branch_website = (string) $text;
        return $this;
    }
 
    public function getWebsite()
    {
        return $this->_branch_website;
    }
    
    public function setPhone($text)
    {
        $this->_phone = (string) $text;
        return $this;
    }
 
    public function getPhone()
    {
        return $this->_phone;
    }
    
    public function setLogo($text)
    {
        $this->_logo = (string) $text;
        return $this;
    }
 
    public function getLogo()
    {
        return $this->_logo;
    }

}

