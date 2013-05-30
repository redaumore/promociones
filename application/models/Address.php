<?php
class PAP_Model_Address
{
    protected $_street;
    protected $_number;
    protected $_others;
    protected $_province;
    protected $_city;
    protected $_zipcode;
    protected $_phones = array();
    protected $_lat;
    protected $_lng;
    
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
    
    public function setStreet($text){
        $this->_street = (string) $text;
        return $this;}
    public function getStreet(){
        return $this->_street;}
    
    public function setNumber($text){
        $this->_number = (string) $text;
        return $this;}
    public function getNumber(){
        return $this->_number;}
    
    public function setOthers($text){
        $this->_others = (string) $text;
        return $this;}
    public function getOthers(){
        return $this->_others;}
    
    public function setProvince($id){
        $province = new PAP_Model_Province();
        $province->loadById($id);
        $this->_province = $province;
        return $this;}
    public function getProvince(){
        return $this->_province;}
        
    public function setCity($id){
        $city = new PAP_Model_City();
        $city->loadById($id);
        $this->_city = $city;
        return $this;}
    public function getCity(){
        return $this->_city;}
        
    public function setZipCode($text){
        $this->_zipcode = (string) $text;
        return $this;}
    public function getZipCode(){
        return $this->_zipcode;}
    
    public function setPhone($text){
        $this->_phones[] = (string) $text;
        return $this;}
    public function getPhones(){
        return $this->_phones;}
    
    public function setLatitude($text){
        $this->_lat = (string) $text;
        return $this;}
    public function getLatitude(){
        return $this->_lat;}
    
    public function setLongitude($text){
        $this->_lng = (string) $text;
        return $this;}
    public function getLongitude(){
        return $this->_lng;}
    
}