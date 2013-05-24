<?php

class PAP_Model_Charge
{
      protected $_charge_id;
      protected $_user_id;
      protected $_period;
      protected $_amount;
      protected $_final_amount;
      protected $_discount;
      protected $_status;
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
        
    public function setStatus($text){
        $this->_status = $this->getStatusToDB($text);
        return $this;}
 
    public function getStatus(){
        return $this->_status;}
        
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
                    ->setStatus($chargerecord["status"])
                    ->setPeriod($chargerecord["period"])
                    ->setUserId($chargerecord["user_id"]);
            $charges[] = $charge;
        }
        return $charges;
    }
    
    private function getStatusToDB($status){
        $statusDB = "";
        switch ($status) {
            case 'approved':
                $statusDB = 'A';
                break;
            case 'pending':
                $statusDB = 'P';
                break;
            case 'in_process':
                $statusDB = 'I';
                break;
            case 'rejected':
                $statusDB = 'R';
                break;
            case 'refunded':
                $statusDB = 'D';
                break;
            case 'cancelled':
                $statusDB = 'C';
                break;
            case 'in_mediation':
                $statusDB = 'M';
                break;
            default:
                $statusDB = $status;
        }
        return $statusDB; 
    }
    
    /*
    approved    El pago fue aprobado y acreditado
    pending    El usuario no completó el pago
    in_process    El pago está siendo revisado
    rejected    El pago fué rechazado, el usuario puede intentar nuevamente el pago
    null    El usuario no completó el proceso de pago y no se ha generado ningún pago */
    
    /*public static function setStatus($id_charge, $status){
        $status_char;
        switch ($status) {
            case 'approved':
                $status_char = 'A';
                break;
            case 'pending':
                $status_char = 'P';
                break;
            case 'in_process':
                $status_char = 'I';
                break;
            case 'rejected':
                $status_char = 'R';
                break;
            case 'null':
                $status_char = 'N';
                break;
        }
        $this->loadById($id_charge);
        $this->setStatus($status_char);
        $this->save();
    } */   
}

