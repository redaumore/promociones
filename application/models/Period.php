<?php
class PAP_Model_Period
{
    protected $_id;
    protected $_from;
    protected $_to;
    protected $_code;
    
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
    public function setFrom($text){
        $this->_from = (string) $text;
        return $this;
    }
    public function getFrom(){
        return $this->_from;
    }
    public function setTo($text){
        $this->_to = (string) $text;
        return $this;
    }
    public function getTo(){
        return $this->_to;
    }
    public function setCode($text){
        $this->_code = (string) $text;
        return $this;
    }
    public function getCode(){
        return $this->_code;
    }
    
    public function loadByCode($code){
        $mapper = new PAP_Model_PeriodMapper();
        $mapper->loadPeriodByCode($code, $this);
        return;
    }
    
    public static function getPeriods($dates){
        $periodMapper = new PAP_Model_PeriodMapper();
        return $periodMapper->getPeriods($dates);
    }
    
    public static function getPeriodsOffset($date, $numperiods){
        //se asume que $numperiods siempre es negativo
        $ids = array();
        $dates = array($date, $date);
        $periodMapper = new PAP_Model_PeriodMapper();
        $pivot = $periodMapper->getPeriods($dates);
        $periodpivot = $pivot[0];
        $idpivot = $periodpivot->getId();
        for($idpivot; $numperiods > 0 ; $idpivot--){
            if($idpivot <> 0)
                $ids[] = $idpivot;
            $numperiods--;
        }
        return $periodMapper->getPeriodsByIds($ids);
    }
}