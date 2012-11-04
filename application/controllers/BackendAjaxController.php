<?php
class backendAjaxController extends Zend_Controller_Action
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
            //echo json_encode($citiesData);
            $this->_helper->json($citiesData);
            //$dojoData= new Zend_Dojo_Data('city_id',$citiesData);
            //echo $dojoData->toJson();
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
    
    public function getprovincesAction(){
         $cities = new PAP_Model_ProvinceMapper();
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        if ($this->getRequest()->isXmlHttpRequest()) {
            $id = $this->_getParam('province_id');
            $citiesData = $cities->getCitiesByProvinceId($id);
            $this->_helper->json($citiesData);
        }
    }
}