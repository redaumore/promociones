<?php
class BackendajaxController extends Zend_Controller_Action
{
    public function init(){
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
        Zend_Loader::loadClass('Zend_Debug');
        
        $action = $this->_getParam('action');
        $this->_helper->getHelper('contextSwitch')
            ->addActionContext($action, 'json')
            ->initContext();
        /*
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('getCities', 'json');
        $ajaxContext->initContext();
        */
    }
    public function indexAction()
    {
        //$form = new MyCustomForm();
        //$this->view->form = $form;
        $this->view->title = "Zend Ajax 101";
    }
    
    public function getcitiesAction()
    {
        try{
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $id = $this->_getParam('province_id');    
            $cache = Zend_Registry::get('cache');
            if(!$citiesData = $cache->load('city_'.$id)) {
                $cities = new PAP_Model_CityMapper();
                $citiesData = $cities->getCitiesByProvinceId($id);
                $cache->save($citiesData, 'city_'.$id);
            }
            $this->_helper->json($citiesData);
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BackendAjaxController->getcitiesAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
    
    /*public function getpromotionsAction(){
        
        $promo = new PAP_Model_Promotion();
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        if ($this->getRequest()->isXmlHttpRequest()){
            $lat = $this->_getParam('lat');
            $lng = $this->_getParam('lng');
            $promosData = $promo->getPromotionsByCoords($lat, $lng, $radius); //TODO Incluir radio para cuando las promos sean muchas.
            $this->_helper->json($promosData);
        }    
    }*/
    
    public function getpromotionAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
        
        //if ($this->getRequest()->isXmlHttpRequest()){     LO SACO PORQUE ES UN GET LO QUE SE HACE.
            $promotion_id = $this->_getParam('promotion_id');
            $promotion = new PAP_Model_Promotion();
            $data = $promotion->getViewRecord($promotion_id);
            //RED$data[0]['path'] = '/images'.$data[0]['path'];
            //RED$data[0]['logo'] = '/images'.$data[0]['logo'];
            $this->_helper->json($data);
        //} 
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BackendAjaxController->getpromotionAction()',$ex, $_SERVER['REQUEST_URI']);
        }   
    }
    
    public function getprovincesAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
                
            $cache = Zend_Registry::get('cache');
            if(!$provincesData = $cache->load('province')) {
                $provinces = new PAP_Model_ProvinceMapper();
            
                $provincesData = $provinces->findForSelect();
                $cache->save($provincesData, 'province');
            }
            $this->_helper->json($provincesData);
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BackendAjaxController->getprovincesAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
    
    public function getcategoriesAction(){
            try{
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            
            $lastupdate = new DateTime($this->_getParam('last_update'));
                
            $category = new PAP_Model_Category();
            $categories = $category->getFrom($lastupdate);
            $this->_helper->json($categories);
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BackendAjaxController->getcategoriesAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
    
    public function getprovAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
                
            $cache = Zend_Registry::get('cache');
            if(!$provincesData = $cache->load('province')) {
                $provinces = new PAP_Model_ProvinceMapper();
            
                $provincesData = $provinces->findForSelect();
                $cache->save($provincesData, 'province');
            }
            $this->_helper->json($provincesData);
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BackendAjaxController->getprovAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
    
    public function getfeaturedAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            
            $client = new PAP_Helper_Client();
            $ip = $client->getIP();
            $city = PAP_Model_City::getByIp($ip);
            if(!isset($city)){
                $city = new PAP_Model_City();
                $city->loadById(150); //TODO 8: Salimos con San Justo por defecto. Luego hay que generalizarlo.
            }
            PAP_Helper_Logger::writeDebug('BackendAjaxController->getfeaturedAction', 'ip:'.$ip.'|city:'.$city->getId(), $_SERVER['REQUEST_URI']);
            $promos = PAP_Model_Promotion::getPromotionsForWeb($city);
            $total    = count($promos);
            
            header('Content-type: application/json');
            // Array indexes are 0-based, jCarousel positions are 1-based.
            
            $first = max(0, intval($_GET['first']) - 1);
            $last  = max($first + 1, intval($_GET['last']) - 1);

            $length = $last - $first + 1;

            //$selected = array_slice($images, $first, $length);
            
            // ---
            /*
            header('Content-Type: text/xml');

            echo '<data>';

            // Return total number of images so the callback
            // can set the size of the carousel.
            echo '  <total>' . $total . '</total>';

            foreach ($selected as $img) {
                echo '  <image>' . $img . '</image>';
            }

            echo '</data>';
             */
            $data= array("total" => $total,
                      'promos' => $promos,);
            //$this->_helper->json($data);
            
             $response = $this->getResponse();
             $response->setHeader('Content-Type', 'text/json');
             $response->setBody(json_encode($data));

             return;
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BackendAjaxController->getfeaturedAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
}
