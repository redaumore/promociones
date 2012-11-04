<?php

class BranchController extends Zend_Controller_Action
{
    public $user;
    public function init()
    {
        /* Initialize action controller here */
        // insert sidebar to the response object
        $this->user = $this->_helper->Session->getUserSession();
        if(!isset($this->user))
            $this->_redirect('/auth/login');
    }

    public function indexAction()
    {
        $branch_order = 0;
        $branch = new PAP_Model_Branch;
        $form = new PAP_Form_BranchForm();
        $this->view->form = $form;
        //$user = $this->_helper->Session->getUserSession();
        
        $comboProvinces = $form->getElement('province');
        $this->loadProvinces($comboProvinces);
        $comboProvinces->setAttrib('onChange', 'loadCities();');
        $comboCities = $form->getElement('city');
        $branchMapper = new PAP_Model_BranchMapper();
        $branchMapper->findByUserId($this->user, $branch_order, $branch);     
                
        if($this->getRequest()->isPost()){
            if($form->isValidPartial($_POST)){
                $data = $form->getValues();
                $branch = $this->saveBranch($data, 'update');
                $this->loadForm($branch, 'update');
                $this->loadCities($comboCities, $branch->getProvince(), $branch->getCity());
            }                
        }
        else{
             
             if(isset($branch)){
                 $this->loadProvinces($comboProvinces, $branch->getProvince());
                 $this->loadCities($comboCities, $branch->getProvince(), $branch->getCity());
                 $this->loadForm($branch, 'update');
                 $this->_helper->Session->setBranchSession($branch);
             }
             else{
                $form->name->setValue($this->user->getName());
                $form->user->setValue($this->user->getId());
             }
        }    
        
    }
    
    public function newAction()
    {
        $form = new PAP_Form_BranchForm();
        $this->view->form = $form;
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                
                $data = $form->getValues();
                //$fullFilePath = $form->file->getFileName();
                $this->saveBranch($data, 'new');
                $this->_redirect('branch/index');
            }
            else{
                $dataOld = $_POST;
                $comboProvinces = $form->getElement('province');
                $this->loadProvinces($comboProvinces);
                $comboProvinces->setAttrib('onChange', 'loadCities();');
                $comboProvinces->setValue($dataOld["province"]);
         
                $comboCities = $form->getElement('city');
                $this->loadCities($comboCities, $dataOld["province"]);
                $comboCities->setValue($dataOld["city"]);
                
                //$form->logo->setVisibility() = false;
            }
        }
        else{
            $this->view->form = $form;
            
            $comboProvinces = $form->getElement('province');
            $this->loadProvinces($comboProvinces);
            $comboProvinces->setAttrib('onChange', 'loadCities();');
         
            $comboCities = $form->getElement('city');
            $this->loadCities($comboCities, 1);
            
            //$user = $this->_helper->Session->getUserSession();
            $form->name->setValue($this->user->getName());
            $form->user->setValue($this->user->getId());
        }
        
    }
    
    private function saveBranch($data, $operation)
    {           //$user = $this->_helper->Session->getUserSession();
                $data["user"] = $this->user->getId();
                
                if(isset($data['filebranch'])){
                    $relativeImageDir = '/customers/'.$data["user"];
                    $customerImageDir = IMAGE_PATH.$relativeImageDir;
                
                    //Tratamiento de la imagen
                    if(!is_dir($customerImageDir))
                        mkdir($customerImageDir);
                    
                    $form = $this->view->form;
                    $fullFilePath = $form->filebranch->getFileName();
                    //$fullFilePath = $data['file'];
                    $extension = substr(strrchr($fullFilePath,'.'),1);
                    $logoName = $customerImageDir.'/logo_'.$data["user"].'.'.$extension;
                    
                    $form->filebranch->addFilter('Rename', array('target' => $logoName,
                             'overwrite' => true));
                    $data['logo'] =  $relativeImageDir.'/logo_'.$data["user"].'.'.$extension;
                    /* TODO: resizing de la imagen
                    $form->file->addFilter(new Skoch_Filter_File_Resize(array('width' => 200,'height' => 300,'keepRatio' => true,))); */
                    if (!$form->filebranch->receive()) {
                        throw new Exception($form->filebranch->getMessages());
                    }
                    chmod($logoName,0644);
                }
                
                if($operation == 'new'){
                    $branch = new PAP_Model_Branch;
                    $data["branchorder"] = "0";
                    $this->user->setStatus("active");
                    $this->user->update();
                    //TODO Luego de activar la cuenta, mostrar un cartel indicando que se deben cargar las categorias.
                }
                else{
                    $branch = $this->_helper->Session->getBranchSession();    
                    $data["branchorder"] = $branch->getBranchorder();
                }
                
                $branch->insert($data);
                $this->_helper->Session->setBranchSession($branch);
                return $branch;
    }

    public function categoriesAction()
    {
        $this->view->addHelperPath('ZFExt/View/Helper', 'ZFExt_View_Helper');
 
        $form = new PAP_Form_Treeview();
        
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $values = $form->getValues(true);
                $this->user->setCategories($values['tree']);
            }
        }
        else{
            $this->loadUserCategories($this->user, $form);    
        }
        $this->view->form = $form;
    }
    
    private function loadUserCategories(PAP_Model_User $user, PAP_Form_Treeview $form){
        $categories = $user->getCategories();
        $categoriesArray = array();
        foreach($categories as $cat){
            $categoriesArray[] = $cat->getId();    
        }
        $form->getElement('tree')->setValue($categoriesArray);
    }

    private function loadForm(PAP_Model_Branch $branch, $formName)
    {
        $form = $this->view->form;
        
        $form->name->setValue($branch->getName());
        $form->street->setValue($branch->getStreet());
        $form->number->setValue($branch->getNumber());
        $form->local->setValue($branch->getLocal());
        $form->phone->setValue($branch->getPhone());
        $form->zipcode->setValue($branch->getZipcode());
        $form->lat->setValue($branch->getLatitude())
                ->setAttrib('readonly', 'true')
                ->setAttrib('class', 'readonly');
        $form->latitude->setValue($branch->getLatitude());
        $form->lng->setValue($branch->getLongitude())
                ->setAttrib('readonly', 'true')
                ->setAttrib('class', 'readonly');
        $form->longitude->setValue($branch->getLongitude());
        $form->user->setValue($branch->getUser());
        $form->branch_id->setValue($branch->getId());
        $form->branch_order->setValue($branch->getBranchorder());
        $form->logo->setOptions(array('src' => '/images'.$branch->getLogo()));
        $form->setDefault('province', $branch->getProvince());
        //$combo->setAttrib('onChange', 'loadCities();');
        //$combo = $form->getElement("city");
        //$this->loadCities($combo, $branch->getProvince());
        $form->setDefault('city', $branch->getCity());
        
        if($formName = 'update'){
            $form->filebranch->setRequired(false)
                ->setLabel('Imagen del Comercio');
        }
    }
    
    private function loadProvinces(Zend_Form_Element_Select $combo, $province_id = 0)
    {
        $provinceMapper = new PAP_Model_ProvinceMapper();
        foreach($provinceMapper->findForSelect() as $p){
            $combo->addMultiOption($p['province_id'], $p['name']);
        }
        if($province_id == 0)            
            $this->view->form->setDefault('province', '1');
        else
            $this->view->form->setDefault('province', $province_id);    
    }

    private function loadCities(Zend_Form_Element_Select $combo, $province_id, $city_id = 0)
    {
        $cityMapper = new PAP_Model_CityMapper();
        foreach($cityMapper->getCitiesByProvinceId($province_id) as $c){
            $combo->addMultiOption($c['city_id'], $c['name']);
        }
        if($city_id != 0)
            $this->view->form->setDefault('city', $city_id);    
    }
}

/* Copia de campos editables
    private function copyEditableFields($branch, $data)
    {
        $branch->setName($data['name']);
        $branch->setLocal($data['local']);
        $branch->setPhone($data['phone']);
        $branch->setZipcode($data['zipcode']);
    }
*/






