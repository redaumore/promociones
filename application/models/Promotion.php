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
    protected $_valueSince;
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
    protected $_radius = 2;
    
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
        $id = $promoMapper->save($this, $options['branches']);
        $this->setId($id);
    }
    
    public function update(array $options){
        $this->setId($options['promoId']);
        $this->insert($options);
    }
    
    public function cloneMe(array $options){
        $cloned_promo_id = $options['promoId'];
        unset($options['promoId']);
        $options['starts'] = date("d-m-Y");
        $options['ends'] = date("d-m-Y");
        $options['promoCode'] = $this->getAutoPromoCode($options['userId']);
        $this->insert($options);
        $this->cloneImages($cloned_promo_id);    
    }
    
    public function saveImages($images){
        $promoMapper = new PAP_Model_PromotionMapper();
        $promoMapper->setImages($this, $images);
    }
    
    public function loadImages($user = null){
        $promoMapper = new PAP_Model_PromotionMapper();
        $loaded = $promoMapper->loadImages($this);    
        if ($loaded == false){
            $images = array();
            $branches = $promoMapper->getBranches($this->getId());
            $images[] = new PAP_Model_Image($branches[0]->getLogo());
            $this->setImages($images);
        }
    }
    
    public function loadById($promotion_id){
        $promoMapper = new PAP_Model_PromotionMapper();
        $promoMapper->find($promotion_id, $this);
    }
    
    public function getViewRecord($promotion_id){
        $promoMapper = new PAP_Model_PromotionMapper();
        return $promoMapper->getViewRecord($promotion_id);
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
        $this->_starts = str_replace("/", "-", $text);
        //$this->_starts = new Zend_Date($text, null, 'es_AR');
        return $this;
    }
    public function getStarts(){
        return $this->_starts;
    }

    public function setEnds($text){
        $this->_ends = str_replace("/", "-", $text);
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
    
    public function setValueSince($text){
        $this->_valueSince = (int)$text;
        return $this;
    }
    public function getValueSince(){
        return $this->_valueSince;
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
    
    public function getTotalPromoCost(){
        return PAP_Model_Payment::getGrandTotal($this);    
    }
    
    public function getPromotionsByCoords($lat, $lng, $categories = ''){
        $promomapper = new PAP_Model_PromotionMapper();
        $branchmapper = new PAP_Model_BranchMapper();
        
        $kmlat = 0.009003753;
        $kmlng = 0.01093571;
        
        $klat = $kmlat * $this->_radius;
        $klng = $kmlng * $this->_radius;
        
        $latO = $lat - $klat;
        $latE = $lat + $klat;
        $lngS = $lng - $klng;
        $lngN = $lng + $klng;
        
        $branches = $branchmapper->getBranchesByRange($latE, $latO, $lngN, $lngS);
        if(count($branches) == 0)
            return array();
        
        $promotions = $promomapper->getPromotionsByBranches($branches, $categories);
        
        $i = 0;
        
        foreach($promotions as $promo){
            $plat = $promo['latitude'];
            $deltalat = (($lat-$plat)*1000)/$kmlat;
            $plng = $promo['longitude'];
            $deltalng = (($lng-$plng)*1000)/$kmlng;
            $distance = round(sqrt(pow($deltalat, 2) + pow($deltalng, 2)));
            $valor = substr($promo['promo_cost'], strrpos($promo['promo_cost'], '-')+1);
            $valor = ($valor == '0.00')?1.00:floatval($valor);
            $indiceord = abs(($distance*$valor*1000)/(1-$distance)+(($valor -1)*1000))-1000;
            $promotions[$i]['distance'] = $distance;
            $promotions[$i]['ord'] = $indiceord;
            if(!isset($promo['path']))
                $promotions[$i]['path'] = $this->getBranchImage($promo['promotion_id']);
            unset($promotions[$i]['promo_cost']);
            $i += 1;
        }
        if(count($promotions)!=0)
            $promotions = $this->sortPromotions($promotions);
        else{
            if($this->_radius != 4){
                $this->_radius = 4;
                $promotions = $this->getPromotionsByCoords($lat, $lng, $categories);
            }
        }
        return $promotions;
    }
    
    public function getPromotionsByIds($ids, $lat, $lng){
        $promomapper = new PAP_Model_PromotionMapper();
        $branchmapper = new PAP_Model_BranchMapper();
        
        $kmlat = 0.009003753;
        $kmlng = 0.01093571;
        
        $promotions = $promomapper->getPromotionsByIds($ids);
        
        $i = 0;
        
        foreach($promotions as $promo){
            $plat = $promo['latitude'];
            $deltalat = (($lat-$plat)*1000)/$kmlat;
            $plng = $promo['longitude'];
            $deltalng = (($lng-$plng)*1000)/$kmlng;
            $distance = round(sqrt(pow($deltalat, 2) + pow($deltalng, 2)));
            $valor = substr($promo['promo_cost'], strrpos($promo['promo_cost'], '-')+1);
            $valor = ($valor == '0.00')?1.00:floatval($valor);
            $indiceord = abs(($distance*$valor*1000)/(1-$distance)+(($valor -1)*1000))-1000;
            $promotions[$i]['distance'] = $distance;
            $promotions[$i]['ord'] = $indiceord;
            if(!isset($promo['path']))
                $promotions[$i]['path'] = $this->getBranchImage($promo['promotion_id']);
            unset($promotions[$i]['promo_cost']);
            $i += 1;
        }
        if(count($promotions)!=0)
            $promotions = $this->sortPromotions($promotions);
        
        return $promotions;
    }
    
    public function getPromotionsByCity($city_id, $categories = ''){
        
        $city = new PAP_Model_City();
        $cityMapper = new PAP_Model_CityMapper();
        $cityMapper->find($city_id, $city);
        //TODO 2: verificar que la localidad seleccionada y las coordenadas sean congruenets.
        $promotions = $this->getPromotionsByCoords($city->getLatitude(), $city->getLongitude(), $categories);
        
        return $promotions;
    }
    
    public function getPromotionById($promotion_id, $lat = '', $lng = ''){
        $promomapper = new PAP_Model_PromotionMapper();
        $promo = $promomapper->getPromotionById($promotion_id);
        if(isset($promo[0])){
            $promo = $promo[0];
            $distance = $this->getDistance($lat, $lng, $promo['latitude'], $promo['longitude']);
            $valor = substr($promo['promo_cost'], strrpos($promo['promo_cost'], '-')+1);
            $valor = ($valor == '0.00')?1.00:floatval($valor);
            $promo['distance'] = $distance;
            unset($promo['promo_cost']);    
        }
        return $promo;    
    }
    
    public static function getPromotionsByDates($from, $to, $user = null){
        $promomapper = new PAP_Model_PromotionMapper();
        if(isset($user))
            $promos = $promomapper->getPromotionByDates($from, $to, $user->getId());
        else
            $promos = $promomapper->getPromotionByDates($from, $to);
        return $promos;
    }
    
    public static function getPromotionsByPeriod($period, $user = null){
        if(isset($user))
            return PAP_Model_PromotionMapper::getPromotionByPeriod($period, $user->getId());
        else
            return PAP_Model_PromotionMapper::getPromotionByPeriod($period);
    }
    
    private function sortPromotions($promotions){
        foreach ($promotions as $key => $row) {
            $indice[$key]  = $row['ord'];
        }
        // Add $data as the last parameter, to sort by the common key
        array_multisort($indice, SORT_DESC, $promotions);
        return $promotions;    
    }
    
    private function getDistance($lat1, $lng1, $lat2, $lng2){
        $kmlat = 0.009003753;
        $kmlng = 0.01093571;
        
        $deltalat = (($lat1-$lat2)*1000)/$kmlat;
        $deltalng = (($lng1-$lng2)*1000)/$kmlng;
        $distance = round(sqrt(pow($deltalat, 2) + pow($deltalng, 2)));
        return $distance;
    }
    
    private function getBranchImage($promotion_id){
        //devuelve el path de la imagen
        $promo_mapper = new PAP_Model_PromotionMapper();
        $branches = $promo_mapper->getBranches($promotion_id);
        $image = '';
        if(count($branches) != 0)
            $image = $branches[0]->getLogo();
        return $image;
    }
    
    private function cloneImages($promo_id){
        $promoMapper = new PAP_Model_PromotionMapper();
        $imagesObj = $promoMapper->getImagesByPromoId($promo_id);
        $images = array();    
        if (isset($imagesObj)){
            for($i = 0; $i < count($imagesObj); $i=$i+1){
                $img = $imagesObj[$i];
                $pathimg = explode("/", $img->getPath());
                $pathimg[2] = $this->getUserId();
                $pathimg[3] = $this->getId();
                $images[] = implode("/", $pathimg);
                $directory = IMAGE_PATH.'\\'.'customers\\'.$pathimg[2].'\\'.$pathimg[3]; 
                if (!file_exists($directory))
                    mkdir($directory);
                copy(IMAGE_PATH.$img->getPath(), IMAGE_PATH.$images[$i]);
            }    
        }
        else{
            $images = array();
            $branches = $promoMapper->getBranches($this->getId());
            if(isset($branches))
                $images[] = $branches[0]->getLogo();
        }    
        $this->saveImages($images);
    }
    
    private function getAutoPromoCode($user_id){
        $f = getDate();
        $auto = 'C'.$user_id.'-'.$f['yday'].$f['hours'].$f['minutes'].$f['seconds'];
        return $auto;
    }
    
}


