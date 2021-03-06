<?php
class servicesController extends Zend_Controller_Action
{
    protected $_return;
    protected $_MP_client_id = '1002026871332942';
    protected $_MP_client_secret = '6vomHLtI0O03ZrMP7LUOeIy61tnH06Kr';
    protected $_MPTokenUrl = 'https://api.mercadolibre.com/oauth/token';
    
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
    
    public function getpromolistbyidsAction(){
        $this->_helper->layout->setLayout('json');  

        $callback = $this->getRequest()->getParam('jsoncallback');
        if ($callback != "")
        {
            // strip all non alphanumeric elements from callback
            $callback = preg_replace('/[^a-zA-Z0-9_]/', '', $callback);
        }  
        $this->view->callback = $callback;

        $lat  = $this->_getParam('lat'); //$_GET['lat'];
        $lng = $this->_getParam('lng');
        $ids  = $this->_getParam('ids'); //$_GET['lat'];
        $promotion = new PAP_Model_Promotion();
        $data = $promotion->getPromotionsByIds($ids, $lat, $lng);
        
        $i = 0;
        foreach($data as $item){
            $data[$i]["path"] = $this->getDataURI("./images".$this->getThumb($item["path"]));
            $i = $i + 1;
        }
        
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
        $data["promo_photo"] = "./images".$data["path"];
        
        $response = $this->getFrontController()->getResponse();
        $response->appendBody($callback.'('.json_encode($data).')');
        $this->getFrontController()->setResponse($response);
    }
    
    private function getDataURI($image, $mime = '') {
        if(file_exists($image))
            $_return = 'data: '.(function_exists('mime_content_type') ? mime_content_type($image) : $mime).';base64,'.base64_encode(file_get_contents($image));
        else{
            //$noimage = './images/backend/photo_error.png';
            //$_return = 'data: '.(function_exists('mime_content_type') ? mime_content_type($noimage) : 'image/png').';base64,'.base64_encode(file_get_contents($noimage));        
            $_return = "NOPIC";
        }
        return $_return;
    }
    
    public function getregionsAction(){
        
        $this->_helper->layout->setLayout('json');  
        
        $callback = $this->getRequest()->getParam('jsoncallback');
        if ($callback != "")
        {
            // strip all non alphanumeric elements from callback
            $callback = preg_replace('/[^a-zA-Z0-9_]/', '', $callback);
        }  
        $this->view->callback = $callback;
        
        $req_version  = $this->_getParam('lastupdate');
        $config = new PAP_Model_Config();
        $response = $this->getFrontController()->getResponse();
        $result = $config->getRegions($req_version);
        if(count($result) == 0)        
            $response->appendBody($callback.'()');
        else{
            $response->appendBody($callback.'('.json_encode($result).')');
        }
        $this->getFrontController()->setResponse($response);
    }
    
    public function sendpaymentAction(){
        $this->_helper->layout->setLayout('json');  
        
        $callback = $this->getRequest()->getParam('jsoncallback');
        if ($callback != "")
        {
            // strip all non alphanumeric elements from callback
            $callback = preg_replace('/[^a-zA-Z0-9_]/', '', $callback);
        }  
        $this->view->callback = $callback; 
        
        $data = $this->_getParam("data");
        $payment_json = $data['data'][0];
        $charges = $payment_json['charges_ids'];
        $charges = explode(',', $charges);
        
        try{
            foreach($charges as $charge_id){
                $charge = new PAP_Model_Charge();
                $charge->loadById($charge_id);
                $payment = new PAP_Model_Payment();
                $payment->setAmount($charge->getAmount())
                        ->setChargeId($charge->getId())
                        ->setControl($payment_json['nro_tx'])
                        ->setMethodId($payment_json['operacion'])
                        ->setPaymentDate($payment_json['fecha']);
                if($payment_json['operacion'] == "T"){
                    $payment->setEntity($payment_json['banco_origen']);
                }
                else
                    $payment->setEntity($payment_json['banco_destino']);
                $payment->save();
                $charge->setStatus('S');
                $charge->save();                            
            }
            
            $data = array();
            $data['result_code'] = '0';
            $data['result_message'] = 'Información del pago informada guardada con éxito.';        
            $response = $this->getFrontController()->getResponse();
            $response->appendBody($callback.'('.json_encode($data).')');
            $this->getFrontController()->setResponse($response);   
        }
        catch(Exception $ex){
            $data = array();
            $data['result_code'] = $ex->getCode();
            $data['result_message'] = 'Hubo un error guardando la información del pago. Por favor envíe un email a soporte@promosalpaso.com con dicha información.';
            $response = $this->getFrontController()->getResponse();
            $response->appendBody($callback.'('.json_encode($data).')');
            $this->getFrontController()->setResponse($response);    
        }
    }
    
    public function getmptokenAction(){
        $this->_helper->layout->setLayout('json');  
        $callback = $this->getRequest()->getParam('jsoncallback');
        if ($callback != ""){
            // strip all non alphanumeric elements from callback
            $callback = preg_replace('/[^a-zA-Z0-9_]/', '', $callback);
        }  
        $this->view->callback = $callback;
        
        $client = new Zend_Http_Client();
        $client->setMethod(Zend_Http_Client::POST);
        $client->setUri($this->_MPTokenUrl);
        $client->setHeaders(array(
            'Accept'  => 'application/json',
            'Content-Type'   => 'application/x-www-form-urlencoded'
        ));
        $client->setParameterPost(array(
            'grant_type' => 'client_credentials', 
            'client_id' => $this->_MP_client_id,
            'client_secret' => $this->_MP_client_secret
        ));
        $response = $client->request();
        $json = json_encode(array('status'=>'ERROR', 'body'=>''));
        if($response->getStatus() == 200){
            $resp = json_decode($response->getBody());
            //$json = json_encode(array('status'=>'OK', 'body'=>$resp->access_token));
            
        }
        else{
            $json = json_encode(array('status'=>'ERROR', 'body'=>$response->getMessage()));
        }
        $response = $this->getFrontController()->getResponse();
        $response->appendBody($callback.'('.$json.')');
        $this->getFrontController()->setResponse($response);
        // echo $json;
    }
    
    private function getEntryPoint($access_token, $json_preference){
        $client = new Zend_Http_Client();
        $client->setMethod(Zend_Http_Client::POST);
        $client->setUri($this->_MPTokenUrl);
        $client->setHeaders(array(
            'Accept'  => 'application/json',
            'Content-Type'   => 'application/x-www-form-urlencoded'
        ));
        $client->setParameterPost(array(
            'grant_type' => 'client_credentials', 
            'client_id' => $this->_MP_client_id,
            'client_secret' => $this->_MP_client_secret
        ));
        $response = $client->request();    
    }
    
    public function getmpinitpointAction(){
        $this->_helper->layout->setLayout('json');  
        $callback = $this->getRequest()->getParam('jsoncallback');
        if ($callback != ""){
            // strip all non alphanumeric elements from callback
            $callback = preg_replace('/[^a-zA-Z0-9_]/', '', $callback);
        }  
        $this->view->callback = $callback;
        $json_preference = $this->_getParam('data');
        $json_preference['items']['0']['quantity'] = (integer)$json_preference['items']['0']['quantity'];
        $json_preference['items']['0']['unit_price'] = (float)$json_preference['items']['0']['unit_price'];
        $json_preference['payment_methods']['installments'] = (integer)$json_preference['payment_methods']['installments'];
        $mp = new PAP_MP($this->_MP_client_id, $this->_MP_client_secret);
        $preferenceResult = $mp->create_preference($json_preference);
        
        $json = json_encode(array('status'=>'OK', 'body'=>$preferenceResult["response"]["sandbox_init_point"]));
        $response = $this->getFrontController()->getResponse();
        $response->appendBody($callback.'('.$json.')');
        $this->getFrontController()->setResponse($response);    
    }
}


