<?php

class PAP_Model_PriceRule
{
    protected $_price_rule_id;
    protected $_price_rule_code;
    protected $_value1;
    protected $_value2;
    protected $_value3;
    protected $_value4;
    protected $_value5;
    protected $_value6;
    protected $_value7;
    protected $_value8;
    protected $_value9;
    protected $_created;
    
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
    
     public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
            if($key == 'price_rule_id')
                $this->setId($value);
            if($key == 'price_rule_code')
                $this->setCode($value);
        }
        return $this;
    }
    
    public function setId($text){
        $this->_price_rule_id = (string) $text;
        return $this;
    }
 
    public function getId(){
        return $this->_price_rule_id;
    }
    
    public function setCode($text){
        $this->_price_rule_code = (string) $text;
        return $this;
    }
 
    public function getCode(){
        return $this->_price_rule_code;
    }
    
    public function setCreated($text){
        $this->_created = (string) $text;
        return $this;
    }
 
    public function getCreated(){
        return $this->_created;
    }
    
    public function setValue1($text){
        $this->_value1 = (string) $text;
        return $this;
    }
    public function getValue1(){
        return $this->_value1;
    }
    
    public function setValue2($text){
        $this->_value2 = (string) $text;
        return $this;
    }
    public function getValue2(){
        return $this->_value2;
    }
    
    public function setValue3($text){
        $this->_value3 = (string) $text;
        return $this;
    }
    public function getValue3(){
        return $this->_value3;
    }
    
    public function setValue4($text){
        $this->_value4 = (string) $text;
        return $this;
    }
    public function getValue4(){
        return $this->_value4;
    }
    
    public function setValue5($text){
        $this->_value5 = (string) $text;
        return $this;
    }
    public function getValue5(){
        return $this->_value5;
    }
    
    public function setValue6($text){
        $this->_value6 = (string) $text;
        return $this;
    }
    public function getValue6(){
        return $this->_value6;
    }
    
    public function setValue7($text){
        $this->_value7 = (string) $text;
        return $this;
    }
    public function getValue7(){
        return $this->_value7;
    }
    
    public function setValue8($text){
        $this->_value8 = (string) $text;
        return $this;
    }
    public function getValue8(){
        return $this->_value8;
    }
    
    public function setValue9($text){
        $this->_value9 = (string) $text;
        return $this;
    }
    public function getValue9(){
        return $this->_value9;
    }
        
    /***
    * Devuelve el código del período al cual pertenece una fecha con formato dd-mm-yyyy
    */
    public static function getPeriodCode($date){
        try{
            $code = '';
            $date = DateTime::createFromFormat('d-m-Y', $date);
            $day = date('j', $date);
            $month = date('m', $date);
            $year = date('Y', $date);
            if($day < 15)
                $code = '1-'.$month.'-'.$year;    
            else
                $code = '2-'.$month.'-'.$year;
            return $code;
        }
        catch(Exception $ex){
            return '';
        }
    }
    
    public function loadByCode($toPR){
        $mapper = new PAP_Model_PriceRuleMapper();
        $mapper->loadByCode($toPR, $this);    
    }     
    
    

}

