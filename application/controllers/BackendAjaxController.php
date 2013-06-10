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
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        if ($this->getRequest()->isXmlHttpRequest()){
            $promotion_id = $this->_getParam('promotion_id');
            $promotion = new PAP_Model_Promotion();
            $data = $promotion->getViewRecord($promotion_id);
            $this->_helper->json($data);
        }    
    }
    
    public function getprovincesAction(){
        $provinces = new PAP_Model_ProvinceMapper();
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        //if ($this->getRequest()->isXmlHttpRequest()) {
            $provincesData = $provinces->findForSelect();
            $this->_helper->json($provincesData);
        //}
    }
    
    public function getprovAction(){
        $provinces = new PAP_Model_ProvinceMapper();
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        //if ($this->getRequest()->isXmlHttpRequest()) {
            $provincesData = $provinces->findForSelect();
            $this->_helper->json($provincesData);
            //echo '{"items":'. $this->_helper->json($provincesData) .'}';
        //}
    }
    
    public function getfeaturedAction(){
        
        // Array indexes are 0-based, jCarousel positions are 1-based.
        $first = max(0, intval($_GET['first']) - 1);
        $last  = max($first + 1, intval($_GET['last']) - 1);

        $length = $last - $first + 1;

        // ---

        $images = array(
            'http://static.flickr.com/66/199481236_dc98b5abb3_s.jpg',
            'http://static.flickr.com/75/199481072_b4a0d09597_s.jpg',
            'http://static.flickr.com/57/199481087_33ae73a8de_s.jpg',
            'http://static.flickr.com/77/199481108_4359e6b971_s.jpg',
            'http://static.flickr.com/58/199481143_3c148d9dd3_s.jpg',
            'http://static.flickr.com/72/199481203_ad4cdcf109_s.jpg',
            'http://static.flickr.com/58/199481218_264ce20da0_s.jpg',
            'http://static.flickr.com/69/199481255_fdfe885f87_s.jpg',
            'http://static.flickr.com/60/199480111_87d4cb3e38_s.jpg',
            'http://static.flickr.com/70/229228324_08223b70fa_s.jpg',
        );

        $total    = count($images);
        $selected = array_slice($images, $first, $length);

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
        $data= array('total' => $total,
                  'images' => $images,);
        
        $this->_helper->json($data);
    }
}
