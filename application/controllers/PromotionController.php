<?php
  class PromotionController extends Zend_Controller_Action
{
    private $user;
    public function init()
    {
        /*$session = new Zend_Session_Namespace('PAP');
        echo $session->user->getName();*/
    }
    
    public function indexAction(){
        $this->checkLogin();
        $cant_activas = 0;
        $cant_historical = 0;
        $currentInfo = PAP_Model_Promotion::getCurrentPromoInfo($this->user);
        $i = 0;
        foreach($currentInfo as $row){
            $currentInfo[$i]["promo_cost"] = substr($row["promo_cost"], strrpos($row["promo_cost"], "-")+1);
            $i=$i+1;
            $cant_activas = $cant_activas + $row["cant"]; 
        }
        $this->view->cant_activas = $cant_activas;
        
        $historicalInfo = PAP_Model_Promotion::getHistoricalPromoInfo($this->user); 
        $i = 0;
        foreach($historicalInfo as $row){
            $historicalInfo[$i]["promo_cost"] = substr($row["promo_cost"], strrpos($row["promo_cost"], "-")+1);
            $i=$i+1;
            $cant_historical = $cant_historical + $row["cant"];
        }
        $this->view->cant_activas = $cant_activas;
        $this->view->cant_historical = $cant_historical;
        $this->view->currentInfo = $currentInfo;
        $this->view->historicalInfo = $historicalInfo; 
    }
    
    private function checkLogin(){
        if(!PAP_Helper_Session::checkLogin())
            $this->_redirect('/auth/login');
        $this->user = $this->_helper->Session->getUserSession();
    }
    
    public function newAction(){
        try{
            $this->checkLogin();
            $form = new PAP_Form_PromotionForm();
            $this->view->form = $form;
            $this->loadPriceRules($this->user);
            if($this->getRequest()->isPost()){
                if($form->isValid($_POST)){
                    $data = $form->getValues();
                    $data['userId'] = $this->user->getId();
                    $data['branches'] = $_POST['branches'];
                    $newPromotion = new PAP_Model_Promotion();
                    $newPromotion->insert($data);
                    /*TODO 6: Selecctión de categorías por promoción. Se asignan las categorias del usuario por ahora.*/
                    $newPromotion->setCategories($this->user->getCategories());
                    $this->saveImages($data, $newPromotion);
                    $this->_redirect('promotion/index'); 
                }
                else{
                    $this->loadUserBranches($this->user);
                }                
            }
            else{
                $this->loadUserBranches($this->user);
            }
            $city = new PAP_Model_City();
            $city->loadById($this->user->getBranch()->getCity());
            if($city->getKickoff() != ""){
                $kickoff = new DateTime($city->getKickoff());
                $form->availableStartDate->setValue($kickoff->format("d-m-Y"));
            }
            else{
                $form->availableStartDate->setValue("");
            }
                
            $form->imagePromo->setOptions(array('src' => $this->user->getBranch()->getLogo()));
            $form->promoCode->setValue($this->getAutoPromoCode());
            
            $form->dateAsNew->setValue($this->user->getDateAsNew());
            
            
            //TODO En la descripcion larga cambiar el estilo
            //TODO Descripcion larga no permite puntos.
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PromotionController->newAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
    
    public function editAction(){
        try{
            $this->checkLogin();
            $form = new PAP_Form_PromotionForm();
            $this->view->form = $form;
            $user = $this->_helper->Session->getUserSession();
            $this->loadPriceRules($user);
            if($this->getRequest()->isPost()){
                if($form->isValid($_POST)){
                    $data = $form->getValues();
                    $data['userId'] = $user->getId();
                    $data['branches'] = $_POST['branches'];
                    
                    if($form->clone->isChecked()){
                        $clonedPromotion = new PAP_Model_Promotion();
                        $clonedPromoId = $clonedPromotion->cloneMe($data);
                        $this->_redirect('promotion/edit/id/'.$clonedPromoId);
                        return true;
                    }
                     
                    
                    /*Validación de fecha por las dudas*/
                    $promo = new PAP_Model_Promotion();
                    $promo->loadById($data['promoId']);
                    if($promo->isLoaded()){
                        $formdate = DateTime::createFromFormat('d-m-Y', $data['starts']);
                        $promodate = DateTime::createFromFormat('d-m-Y', $promo->getStarts());
                        $today = DateTime::createFromFormat('d-m-Y', date('d-m-Y'));
                        if($promodate->format('Y-m-d') < $today->format('Y-m-d') && $formdate->format('Y-m-d') <> $promodate->format('Y-m-d')){
                            PAP_Helper_Logger::writeLog(Zend_Log::WARN, 'PromotionController->editAction()',
                            "El cliente ".$user->getId()." intentó cambiar la fecha de inicio de promocion sospechosamente. ".$promodate->format("d-m-Y")." por ".$formdate->format("d-m-Y").".", 
                            $_SERVER['REQUEST_URI']); 
                            return false; /*TODO 9: Hacer una salida más elegente si la validación fallo.*/       
                        }
                             
                    }
                    if ($form->save->isChecked()){
                        $newPromotion = new PAP_Model_Promotion();
                        $newPromotion->update($data);
                        $newPromotion->setCategories($this->user->getCategories());
                        $this->saveImages($data, $newPromotion);
                        $this->loadForm($newPromotion, 'update');
                    }
                    
                }                
            }
            else{
                $promotion = new PAP_Model_Promotion;
                $promo_id = $this->getParam('id');
                $promotionMapper = new PAP_Model_PromotionMapper();
                $promotionMapper->find($promo_id, $promotion);     
                if(isset($promotion)){
                     $this->loadForm($promotion, 'update');
                      //TODO Modificar la carga de Branches cuando sean màs de uno.
                     $this->_helper->Session->setBranchSession($this->user->getBranch());
                 }
                 else{
                    //@todo Mostrar mensaje de que no se encontrò la promo.
                 }
            }
            $this->loadUserBranches($this->user);
            
            $city = new PAP_Model_City();
            $city->loadById($this->user->getBranch()->getCity());
            if($city->getKickoff() != ""){
                $kickoff = new DateTime($city->getKickoff());
                $form->availableStartDate->setValue($kickoff->format("d-m-Y"));
            }
            else{
                $form->availableStartDate->setValue("");
            }
            $form->dateAsNew->setValue($this->user->getDateAsNew());
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PromotionController->editAction()',$ex, $_SERVER['REQUEST_URI']);
        } 
    }
    
    public function deleteAction(){
        try{
            $this->checkLogin();
            $promo_id = $this->getParam('id');
            $promoMapper = new PAP_Model_PromotionMapper();
            $promotion = new PAP_Model_Promotion();
            $promotion->loadById($promo_id);
            if($this->dateDiff($promotion->getStarts(), null) >= 0 ){
                //TODO Borrado lògico de las promociones y permitir borrar las promos del dìa actual
                //$promoMapper->delete($promotion);
                PAP_Model_Promotion::delete($promo_id);
            }
            $this->_redirect('promotion/index'); 
            //@todo Mostrar mensaje al usuario
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PromotionController->deleteAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
    
    public function datosAction(){
        try{
            $this->checkLogin();
            
            $user = $this->_helper->Session->getUserSession();
            
            $promoMapper = new PAP_Model_PromotionMapper();
            
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->getHelper('layout')->disableLayout();
            if($this->getRequest()->isPost()){
                $page = $_POST['page']; // get the requested page
                $limit = $_POST['rows']; // get how many rows we want to have into the grid
                $sidx = $_POST['sidx']; // get index row - i.e. user click to sort
                $sord = $_POST['sord']; // get the direction
            }
            else{
                $page = 0; // get the requested page
                $limit = 20; // get how many rows we want to have into the grid
                $sidx = 0; // get index row - i.e. user click to sort
                $sord =  'starts';
            }
            
            if(!$sidx) $sidx =1;
            
            $count =  $promoMapper->countPromos($user->getId());

            if( $count > 0 ) {$total_pages = ceil($count/$limit);} 
            else{$total_pages = 0;}
            
            if ($page > $total_pages)
                $page=$total_pages;

            $start = $limit * $page - $limit;
            
            if ($start<0) $start=0;
            
            $row = $promoMapper->getByUserId($user->getId(), $sidx, $sord, $start, $limit);

            $response['page'] = $page;
            $response['total'] = $total_pages;
            $response['records'] = $count;
            $i=0;
            
            foreach ($row as $r) {
                $response['rows'][$i]['id']=$r['promotion_id']; //id
                $response['rows'][$i]['cell']=array('',$r['promo_code'],$r['starts'],$r['ends'],$r['short_description'],$r['promo_value'],$r['state'],$r['visited'],$r['is_percentage']);
                $i++;
            }
            echo $this->_helper->json($response);

            /*    }                
            }
            else{
                $promoMapper = new PAP_Model_PromotionMapper();
                $result = $promoMapper->getByUserId($user->getId());    
            }
            */
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PromotionController->datosAction()',$ex, $_SERVER['REQUEST_URI']);
        }
    }
    
    public function searchAction(){
        try{
            $form = new PAP_Form_SearchForm();
            $this->view->form = $form;
            
            if($this->getRequest()->isPost()){
                $this->_helper->viewRenderer->setNoRender();
                $this->_helper->getHelper('layout')->disableLayout();
                $page = $_POST['page']; // get the requested page
                $limit = $_POST['rows']; // get how many rows we want to have into the grid
                $sidx = $_POST['sidx']; // get index row - i.e. user click to sort
                $sord = $_POST['sord'];
                $city_id = $this->getParam('city');
                $categories = ''.$this->getParam('categories');
            }
            else{
                $page = 0; // get the requested page
                $limit = 10; // get how many rows we want to have into the grid
                $sidx = 0; // get index row - i.e. user click to sort
                $sord =  'starts';
                $city_id = 150;
                return;
            }
            
            if(!$sidx) $sidx =1;
            $promo = new PAP_Model_Promotion(); 
            $promotions = $promo->getPromotionsByCity($city_id, $categories);
            $count = count($promotions);
            if( $count > 0 ) {$total_pages = ceil($count/$limit);} 
            else{$total_pages = 0;}
            
            if ($page > $total_pages)
                $page=$total_pages;

            $start = $limit * $page - $limit;
            
            if ($start<0) $start=0;
            
            //$row = $promoMapper->getByUserId($user->getId(), $sidx, $sord, $start, $limit);

            $response['page'] = $page;
            $response['total'] = $total_pages;
            $response['records'] = $count;
            $i=0;
            
            foreach ($promotions as $r) {
                $response['rows'][$i]['id']=$r['promotion_id']; //id
                $response['rows'][$i]['cell']=array($r['path'],$r['name'],$r['displayed_text'],$r['street'].' '.$r['number'].', '.$r['city'],$r['promo_value'],$r['is_percentage'],isset($r['distance'])?(string)$r['distance']:'N/D');
                $i++;
            }
            echo $this->_helper->json($response);
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'PromotionController->searchAction()',$ex, $_SERVER['REQUEST_URI']);
        }        
    }
    
    private function loadForm(PAP_Model_Promotion $promo)
    {
        $form = $this->view->form;
        
        $control = $form->getElement('promoId');
        $control->setValue($promo->getId());
        
        $control = $form->getElement('promoCode');
        $control->setValue($promo->getPromoCode());
        
        $control = $form->getElement('shortDescription');
        $control->setValue($promo->getShortDescription());
        
        $control = $form->getElement('longDescription');
        $control->setValue($promo->getLongDescription());
        
        $control = $form->getElement('longDescription');
        $control->setValue($promo->getLongDescription());
        
        $control = $form->getElement('starts');
        $control->setValue($promo->getStarts());
        /*if(strtotime($promo->getStarts()) <= time())  se pasa esta validación al cliente porque se usa un control.
            $control->setAttrib('readonly', true);*/
        
        $control = $form->getElement('ends');
        $control->setValue($promo->getEnds());
        
        $control = $form->getElement('promoValue');
        $control->setValue($promo->getPromoValue());
        
        $control = $form->getElement('valueSince');
        $control->setValue($promo->getValueSince());
        
        $control = $form->getElement('valueType');
        $control->setValue($promo->getIsPercentaje());
        
        $control = $form->getElement('quantity');
        $control->setValue($promo->getQuantity());
        
        $control = $form->getElement('promoType');
        $control->setValue($promo->getPromoType());
        
        $control = $form->getElement('displayedText');
        $control->setValue($promo->getDisplayedText());
        
        $control = $form->getElement('alertType');
        $control->setValue($promo->getAlertType());
        
        $control = $form->getElement('state');
        $control->setValue($promo->getState());
        
        $control = $form->getElement('promoCost');
        $options = $control->getMultiOptions();
        $selected_option = $options[$promo->getPromoCost()];
        if(array_key_exists($promo->getPromoCost(), $options)){
            $control->setValue($promo->getPromoCost());    
        }
        else{
            $promocost =  substr($promo->getPromoCost(), -4);
            foreach($options as $key=>$value){
                if($value == $promocost){
                    $control->setValue($key);
                    break;
                }
                else{
                    $control->setValue(key($options));    
                }
            }   
        }
        
        $control = $form->getElement('visited');
        $control->setValue($promo->getVisited());
                                         
        $control = $form->getElement('imagePromo');
        $img = $promo->getImage();
        if(isset($img))
            $control->setOptions(array('src' => $img->getPath()."?".time()));
        else
            $control->setOptions(array('src' => $this->user->getBranch()->getLogo()));
            
        $this->loadJsonUserInfo($promo);
    }
    
    private function loadJsonUserInfo($promo){
        $user = $this->_helper->Session->getUserSession();
        $mainbranch = $user->getBranch();
        $address = $mainbranch->getAddress();
        $userInfo = array("user_name"=>utf8_encode($user->getName()),
                    "user_email"=>utf8_encode($user->getEmail()),
                    "branch_street"=>utf8_encode($address->getStreet()),
                    "branch_number"=>$address->getNumber(),
                    "branch_local"=>utf8_encode($address->getOthers()),
                    "branch_city_name"=>utf8_encode($address->getCity()->getName()),
                    "branch_city_id"=>$address->getCity()->getId(),
                    "branch_logo"=>$mainbranch->getLogo(),
                    "promo_image"=>$mainbranch->getLogo(),
                    );
        $this->view->form->userInfo->setValue(json_encode($userInfo));    
    }
    
    private function saveImages($data, $promo)
    {
        if(isset($data['filePromo']))
        {
            $relativeImageDir = '/images/customers/'.$data["userId"];
            $customerImageDir = IMAGE_PATH.'/customers/'.$data["userId"];

            $form = $this->view->form;            
            $adapter = $form->filePromo->getTransferAdapter();
            //create directory where files would be hold
            if(!is_dir($customerImageDir))
                mkdir($customerImageDir, 0777, 1);
            $i=0;
            $images= array();

            //loop uploaded files
            
            $files  = $adapter->getFileInfo();
            foreach($files as $file => $fileInfo) {
                $extension = substr(strrchr($fileInfo['name'],'.'),1);
                $promoImageDir = $customerImageDir.'/'.$promo->getId();
               
                $imageName = $promoImageDir.'/image_'.$i.'.'.$extension;
                $imageName2 = $promoImageDir.'/thumb/image_'.$i.'.'.$extension;
                
                 if(!is_dir($promoImageDir))
                    mkdir($promoImageDir, 0777, 1);
                    
                if(!is_dir($promoImageDir.'/thumb'))
                    mkdir($promoImageDir.'/thumb', 0777, 1);
                    
                $adapter->addFilter('Rename', array('target'=>$imageName, 'overwrite'=>true));
                
                $filterChain = new Zend_Filter();
                $filterChain->appendFilter(new Skoch_Filter_File_Resize(array(
                        'width' => 300,
                        'height' => 300,
                        'keepRatio' => true,
                )));
                
                $filterChain->appendFilter(new Skoch_Filter_File_Resize(array(
                        'directory' => $promoImageDir.'/thumb',
                        'width' => 60,
                        'height' => 60,
                        'keepRatio' => true,
                )));
                
                $adapter->addFilter($filterChain);
                
                $images[] =  $relativeImageDir.'/'.$promo->getId().'/image_'.$i.'.'.$extension;
                /* TODO: resizing de la imagen
                $form->file->addFilter(new Skoch_Filter_File_Resize(array('width' => 200,'height' => 300,'keepRatio' => true,))); */
                if (!$adapter->receive($file)) {
                    $mensajes = $form->filePromo->getMessages();
                    throw new Exception('Error!!');
                }
                //chmod($logoName,0777);
                $i++;    
            }
            /*foreach ($adapter->getFileInfo() as $info)
            {     
                $extension = substr(strrchr($info['name'],'.'),1);
                
                $promoImageDir = $customerImageDir.'/'.$promo->getId();
                if(!is_dir($promoImageDir))
                    mkdir($promoImageDir, 0666, 1);
                
                $logoName = $promoImageDir.'/image_'.$i.'.'.$extension;
                        
                $adapter->addFilter('Rename', array('target'=>$logoName, 'overwrite'=>true));

                $images[] =  $relativeImageDir.'/'.$promo->getId().'/image_'.$i.'.'.$extension;
                 TODO: resizing de la imagen
                $form->file->addFilter(new Skoch_Filter_File_Resize(array('width' => 200,'height' => 300,'keepRatio' => true,))); 
                if (!$form->images->receive()) {
                    $mensajes = $form->images->getMessages();
                    throw new Exception('Error!!');
                }
                chmod($logoName,0666);
                $i++;    
            } */
            $promo->saveImages($images);
        }
        else{
            $promo->loadImages($this->user);
        }
    }
    
    private function dateDiff($date, $pivot)
    {
         if($pivot == null){
          $pivot = date("Y-m-d");
         }
         
         $date = DateTime::createFromFormat('d-m-Y', $date);
         $date = $date->format('Y-m-d');
         //$pivot = strtotime($pivot);
         
         if($date == $pivot){  
          return 0;
         }
         else if($date < $pivot){  
          return -1;
         }
         else if($date > $pivot){    
          return 1;
         }
         return false;
    }
    
    private function loadPriceRules($user){
        $form = $this->view->form;
        $rule = $this->user->getPriceRulesItems();
        $rule = $rule[0];
        if(isset($rule)){
            $comboPrices = $form->getElement('promoCost');
            $rules = array();
            $comboPrices->addMultioption($rule['price_rule_code'].'-'.$rule['value1'], $rule['value1']);
            $comboPrices->addMultioption($rule['price_rule_code'].'-'.$rule['value2'], $rule['value2']);
            $comboPrices->addMultioption($rule['price_rule_code'].'-'.$rule['value3'], $rule['value3']);
            $comboPrices->addMultioption($rule['price_rule_code'].'-'.$rule['value4'], $rule['value4']);
            if($rule['value5']!= '-1')
                $comboPrices->addMultioption($rule['price_rule_code'].'-'.$rule['value5'], $rule['value5']);
            if($rule['value6']!= '-1')
                $comboPrices->addMultioption($rule['price_rule_code'].'-'.$rule['value6'], $rule['value6']);
            if($rule['value7']!= '-1')
                $comboPrices->addMultioption($rule['price_rule_code'].'-'.$rule['value7'], $rule['value7']);
            if($rule['value8']!= '-1')
                $comboPrices->addMultioption($rule['price_rule_code'].'-'.$rule['value8'], $rule['value8']);
            if($rule['value9']!= '-1')
                $comboPrices->addMultioption($rule['price_rule_code'].'-'.$rule['value9'], $rule['value9']);
            $comboPrices->setValue($rule['price_rule_code'].'-'.$rule['value1']);
        }    
    }
    
    private function loadUserBranches($user){
        $form = $this->view->form;
        $branches = $this->user->getBranches();
        if(count($branches)>0){
            $comboBranches = $form->getElement('branches');
            foreach($branches as $branch){
                $comboBranches->addMultioption($branch->getId(), $branch->getName());
                $idBranch = $branch->getId();
                if(count($branches) == 1){
                    $selected = array();
                    $selected[] = $idBranch;
                    $comboBranches->setValue($selected);
                }
            }
        }    
    }
    
    private function getAutoPromoCode(){
        $f = getDate();
        $auto = 'C'.$this->user->getId().'-'.$f['yday'].$f['hours'].$f['minutes'].$f['seconds'];
        return $auto;
    }  
}

