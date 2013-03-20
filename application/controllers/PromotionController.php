<?php
  class PromotionController extends Zend_Controller_Action
{
    private $user;
    public function init()
    {
        /*$session = new Zend_Session_Namespace('PAP');
        echo $session->user->getName();*/
    }
    
    private function checkLogin(){
        $this->user = $this->_helper->Session->getUserSession();
        if(!isset($this->user))
            $this->_redirect('/auth/login');    
    }
    
    public function newAction(){
        //$user = $this->_helper->Session->getUserSession();
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
        $form->imagePromo->setOptions(array('src' => '/images'.$this->user->getBranch()->getLogo()));
        $form->promoCode->setValue($this->getAutoPromoCode());
        
        //TODO En la descripcion larga cambiar el estilo
        //TODO Descripcion larga no permite puntos.
    }
    
    public function indexAction(){
        
    }
    
    public function editAction(){
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
                $newPromotion = new PAP_Model_Promotion();
                $newPromotion->update($data);
                $this->saveImages($data, $newPromotion);
                //$this->loadForm($newPromotion, 'update');
                $this->_redirect('promotion/index');
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
                 //$this->_helper->Session->setBranchSession($branch);
             }
             else{
                //@todo Mostrar mensaje de que no se encontrò la promo.
             }
        }
        $this->loadUserBranches($this->user); 
    }
    
    public function deleteAction(){
        $this->checkLogin();
        $promo_id = $this->getParam('id');
        $promoMapper = new PAP_Model_PromotionMapper();
        $promotion = new PAP_Model_Promotion();
        $promoMapper->find($promo_id, $promotion);
        if($this->dateDiff($promotion->getStarts(), null) >= 0 ){
            //TODO Borrado lògico de las promociones y permitir borrar las promos del dìa actual
            $promoMapper->delete($promotion);
        }
        $this->_redirect('promotion/index'); 
        //@todo Mostrar mensaje al usuario
    }
    
    public function datosAction(){
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
            $limit = 10; // get how many rows we want to have into the grid
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
            $response['rows'][$i]['cell']=array('',$r['promo_code'],$r['starts'],$r['ends'],$r['short_description'],$r['promo_value'],$r['state'],$r['visited']);
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
    
    public function searchAction(){
        
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
            $r['path'] = 'images'.$r['path'];
            $response['rows'][$i]['cell']=array($r['path'],$r['name'],$r['displayed_text'],$r['short_description'],$r['promo_value'],isset($r['distance'])?(string)$r['distance']:'N/D');
            $i++;
        }
        echo $this->_helper->json($response);        
    }
    
    private function loadForm(PAP_Model_Promotion $promo, $formName = null)
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
        
        $control = $form->getElement('ends');
        $control->setValue($promo->getEnds());
        
        $control = $form->getElement('promoValue');
        $control->setValue($promo->getPromoValue());
        
        //$control = $form->getElement('totalCost');
        //$control->setValue($promo->getTotalPromoCost());
        
        $control = $form->getElement('valueSince');
        $control->setValue($promo->getValueSince());
        
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
        $control->setValue($promo->getPromoCost());
        
        $control = $form->getElement('visited');
        $control->setValue($promo->getVisited());
                                         
        $control = $form->getElement('imagePromo');
        $img = $promo->getImage();
        if(isset($img))
            $control->setOptions(array('src' => '/images'.$img->getPath()));
        else
            $control->setOptions(array('src' => '/images'.$this->user->getBranch()->getLogo()));
    }
    
    private function saveImages($data, $promo)
    {
        if(isset($data['filePromo']))
        {
            $relativeImageDir = '/customers/'.$data["userId"];
            $customerImageDir = IMAGE_PATH.$relativeImageDir;

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
                chmod($logoName,0777);
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
         
         $date = DateTime::createFromFormat('d/m/Y', $date);
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

