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
           /* include ('models/ajax.php');
            $id = trim($this->getRequest()->getParam('province_id'));
            $cities = new PAP_Model_CityMapper();
            $this->_view->result = $cities->getCitiesByProvinceId($id);
             */
            $cities = new PAP_Model_CityMapper();
            
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            
            if ($this->getRequest()->isXmlHttpRequest()) {
                $id = $this->_getParam('province_id');
                $citiesData = $cities->getCitiesByProvinceId($id);
                $this->_helper->json($citiesData);
            }
            
            /*
            if ($this->getRequest()->isXmlHttpRequest()) {
                $id = $this->_getParam('province_id');
                $citiesData = $cities->getCitiesByProvinceId($id);
                //$dojoData= new Zend_Dojo_Data('city_id',$citiesData);
                //echo $dojoData->toJson();
                echo json_encode($citiesData);
            }
            */
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
            $this->_helper->json($data);
        //} 
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BackendAjaxController->getpromotionAction()',$ex, $_SERVER['REQUEST_URI']);
        }   
    }
    
    public function getprovincesAction(){
        try{
            $provinces = new PAP_Model_ProvinceMapper();
            
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            
            //if ($this->getRequest()->isXmlHttpRequest()) {
                $provincesData = $provinces->findForSelect();
                $this->_helper->json($provincesData);
            //}
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BackendAjaxController->getprovincesAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
    
    public function getprovAction(){
        try{
            $provinces = new PAP_Model_ProvinceMapper();
            
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            
            //if ($this->getRequest()->isXmlHttpRequest()) {
                $provincesData = $provinces->findForSelect();
                $this->_helper->json($provincesData);
                //echo '{"items":'. $this->_helper->json($provincesData) .'}';
            //}
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BackendAjaxController->getprovAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
    
    public function getfeaturedAction(){
        try{
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            
            $client = new PAP_Helper_Client();
            $ip = $client->getIP();
            $city = PAP_Model_City::getByIp($ip);
            if(!isset($city)){
                $city = new PAP_Model_City();
                $city->loadById(150); //TODO 8: Salimos con San Justo por defecto. Luego hay que generalizarlo.
            }
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
                      'images' => $promos,);
            $this->_helper->json($data);
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'BackendAjaxController->getfeaturedAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
}
