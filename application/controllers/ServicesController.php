<?php
class servicesController extends Zend_Controller_Action
{
    public function init(){
        /*
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
        Zend_Loader::loadClass('Zend_Debug');
        */
        //$this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        /*
        $action = $this->_getParam('action');
        $this->_helper->getHelper('contextSwitch')
            ->addActionContext($action, 'json')
            ->initContext();
         */
        
    }
    
    
    public function getpromolistAction(){
        $this->_helper->layout->setLayout('json');  

        $callback = $this->getRequest()->getParam('jsoncallback');
        if ($callback != "")
        {
            // strip all non alphanumeric elements from callback
            $callback = preg_replace('/[^a-zA-Z0-9_]/', '', $callback);
        }  
        $this->view->callback = $callback;

        $lat  = $this->_getParam('lat'); //$_GET['lat'];
        $lng = $this->_getParam('lng'); //$_GET['lng']; 
        $promotion = new PAP_Model_Promotion();
        $data = $promotion->getPromotionsByCoords($lat, $lng, '');
        
        $i = 0;
        foreach($data as $item){
            $data[$i]["path"] = $this->getDataURI("./images".$this->getThumb($item["path"]));
            $i = $i + 1;
        }
        
        //header('Content-Type: application/json');
        /*$data = array();
        $data['latitud'] = $lat;
        $data['longitud'] = $lng;
        */
        $response = $this->getFrontController()->getResponse();
        $response->appendBody($callback.'('.json_encode($data).')');
        $this->getFrontController()->setResponse($response);
    }
    
    private function getThumb($path){
        return str_replace('/image_', '/thumb/image_', $path);
    }
    
    public function getpromodetailAction(){
        $this->_helper->layout->setLayout('json');  

        $callback = $this->getRequest()->getParam('jsoncallback');
        if ($callback != "")
        {
            // strip all non alphanumeric elements from callback
            $callback = preg_replace('/[^a-zA-Z0-9_]/', '', $callback);
        }  
        $this->view->callback = $callback;

        $lat  = $this->_getParam('lat'); //$_GET['lat'];
        $lng = $this->_getParam('lng'); //$_GET['lng'];
        $promotion_id = $this->_getParam('promoid'); //$_GET['lng']; 
        $promotion = new PAP_Model_Promotion();
        $data = $promotion->getPromotionById($promotion_id, $lat, $lng);
        $data["logo"] = $this->getDataURI("./images".$data["logo"]);
        $data["path"] = $this->getDataURI("./images".$this->getThumb($data["path"]));
        $data["image"] = "./images".$data["path"];
        
        $response = $this->getFrontController()->getResponse();
        $response->appendBody($callback.'('.json_encode($data).')');
        $this->getFrontController()->setResponse($response);
    }
    
    private function getDataURI($image, $mime = '') {
        return 'data: '.(function_exists('mime_content_type') ? mime_content_type($image) : $mime).';base64,'.base64_encode(file_get_contents($image));
    }
}

