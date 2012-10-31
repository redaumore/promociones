<?php
require_once 'Zend/Locale.php';

class PAP_Model_Promotion
{
    protected $_id;
    protected $_promocode;
    protected $_userId;
    protected $_starts;
    protected $_ends;
    protected $_shortDescription;
    protected $_longDescription;
    protected $_promoValue;
    protected $_quantity;
    protected $_promoType;
    protected $_displayedText;
    protected $_alertType;
    protected $_state;
    protected $_promoCost;
    protected $_visited;
    protected $_created;
    protected $_updated;
    protected $_images;
    
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
    
    public function insert(array $options){
        $this->setOptions($options);
        $promoMapper = new PAP_Model_PromotionMapper();
        $id = $promoMapper->save($this);
        $this->setId($id);
    }
    
    public function update(array $options){
        $this->setId($options['promoId']);
        $this->insert($options);
    }
    
    public function saveImages($images){
        $promoMapper = new PAP_Model_PromotionMapper();
        $promoMapper->setImages($this, $images);
    }
    
    public function loadImages(){
        $promoMapper = new PAP_Model_PromotionMapper();
        $promoMapper->loadImages($this);    
    }
    
    /* PROPERTIES */
    
    public function setId($text){
        $this->_id = (string) $text;
        return $this;
    }
    public function getId(){
        return $this->_id;
    }

    public function setPromoCode($text){
        $this->_promocode = (string) $text;
        return $this;
    }
    public function getPromoCode(){
        return $this->_promocode;
    }

    public function setUserId($text){
        $this->_userId = (string) $text;
        return $this;
    }
    public function getUserId(){
        return $this->_userId;
    }

    public function setStarts($text){
        $this->_starts = (string) $text;
        //$this->_starts = new Zend_Date($text, null, 'es_AR');
        return $this;
    }
    public function getStarts(){
        return $this->_starts;
    }

    public function setEnds($text){
        $this->_ends = (string) $text;
        //$this->_ends = new Zend_Date($text, null, 'es_AR');
        return $this;
    }
    public function getEnds(){
        return $this->_ends;
    }

    public function setShortDescription($text){
        $this->_shortDescription = (string) $text;
        return $this;
    }
    public function getShortDescription(){
        return $this->_shortDescription;
    }

    public function setLongDescription($text){
        $this->_longDescription = (string) $text;
        return $this;
    }
    public function getLongDescription(){
        return $this->_longDescription;
    }

    public function setPromoValue($text){
        $this->_promoValue = (string) $text;
        return $this;
    }
    public function getPromoValue(){
        return $this->_promoValue;
    }

    public function setQuantity($text){
        $this->_quantity = (string) $text;
        return $this;
    }
    public function getQuantity(){
        return $this->_quantity;
    }

    public function setPromoType($text){
        $this->_promoType = (string) $text;
        return $this;
    }
    public function getPromoType(){
        return $this->_promoType;
    }

    public function setDisplayedText($text){
        $this->_displayedText = (string) $text;
        return $this;
    }
    public function getDisplayedText(){
        return $this->_displayedText;
    }

    public function setAlertType($text){
        $this->_alertType = (string) $text;
        return $this;
    }
    public function getAlertType(){
        return $this->_alertType;
    }

    public function setState($text){
        $this->_state = (string) $text;
        return $this;
    }
    public function getState(){
        return $this->_state;
    }

    public function setPromoCost($text){
        $this->_promoCost = (string) $text;
        return $this;
    }
    public function getPromoCost(){
        return $this->_promoCost;
    }

    public function setVisited($text){
        $this->_visited = (string) $text;
        return $this;
    }
    public function getVisited(){
        return $this->_visited;
    }

    public function setCreated($text){
        $this->_created = (string) $text;
        return $this;
    }
    public function getCreated(){
        return $this->_created;
    }

    public function setUpdated($text){
        $this->_updated = (string) $text;
        return $this;
    }
    public function getUpdated(){
        return $this->_updated;
    }
    
    public function getImage($img = 0){
        if($this->getImageCount() == 0)
            return null;
        return $this->_images[$img];    
    }
    
    public function setImages($img){
        $this->_images = $img;    
    }
    
    public function getImageCount(){
        return count($this->_images);
    }
}


