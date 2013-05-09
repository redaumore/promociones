<?php

class PAP_Model_Charge
{
      protected $_charge_id;
      protected $_user_id;
      protected $_period;
      protected $_amount;
      protected $_final_amount;
      protected $_discount;
      protected $_paid_off;
      protected $_created;
      
    public function setId($text){
        $this->_charge_id = (string) $text;
        return $this;}
 
    public function getId(){
        return $this->_charge_id;}
    
    public function setUserId($text){
        $this->_user_id = (string) $text;
        return $this;}
 
    public function getUserId(){
        return $this->_user_id;}    
    
    public function setPeriod($text){
        $this->_period = (string) $text;
        return $this;}
 
    public function getPeriod(){
        return $this->_period;}
        
    public function setAmount($text){
        $this->_amount = (string) $text;
        return $this;}
 
    public function getAmount(){
        return $this->_amount;}
        
    public function setDiscount($text){
        $this->_discount = (string) $text;
        return $this;}
 
    public function getDiscount(){
        return $this->_discount;}
        
    public function setPaidOff($text){
        $this->_paid_off = (string) $text;
        return $this;}
 
    public function getPaidOff(){
        return $this->_paid_off;}
        
    public function setCreated($text){
        $this->_created = (string) $text;
        return $this;}
 
    public function getCreated(){
        return $this->_created;}
        
    public function setFinalAmount($text){
        $this->_final_amount = (string) $text;
        return $this;}
 
    public function getFinalAmount(){
        return $this->_final_amount;}
        
    public function __construct(array $options = null){
        if (is_array($options)) 
            $this->setOptions($options);
    } 
    
    public function __set($name, $value){
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method))
            throw new Exception('Invalid charge property');
        $this->$method($value);
    }
 
    public function __get($name){
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method))
            throw new Exception('Invalid charge property');
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
    
    public function insert(array $options){
        $this->setOptions($options);
        $mapper = new PAP_Model_ChargeMapper();
        $mapper->save($this);
    }
    
    public function save(){
        $mapper = new PAP_Model_ChargeMapper();
        $mapper->save($this);
    }
    
    public function loadById($charge_id){
      $mapper = new PAP_Model_ChargeMapper();
      $mapper->find($charge_id, $this);  
    }
    
    public static function getUnpaidCharges(PAP_Model_User $user = null){
        $charges = array();
        $mapper = new PAP_Model_ChargeMapper();
        if(isset($user))
            $result = $mapper->getUnpaidCharges($user->getId());
        else
            $result = $mapper->getUnpaidCharges();
        foreach($result as $chargerecord){
            $charge = new PAP_Model_Charge();
            $charge->setId($chargerecord["charge_id"])
                    ->setAmount($chargerecord["amount"])
                    ->setDiscount($chargerecord["discount"])
                    ->setCreated($chargerecord["created"])
                    ->setFinalAmount($chargerecord["final_amount"])
                    ->setPaidOff($chargerecord["paid_off"])
                    ->setPeriod($chargerecord["period"])
                    ->setUserId($chargerecord["user_id"]);
            $charges[] = $charge;
        }
        return $charges;
    }
        
}

