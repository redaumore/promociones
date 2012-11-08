<?php
class PAP_Form_SearchForm extends Zend_Form
  {
      public function init(){
   
        $this->setMethod('post');
        $this->setAttrib('enctype', Zend_Form::ENCTYPE_MULTIPART);
        //'multipart/form-data'
        $this->addDecorators(array('FormElements', 'Form'));
        
        $decorators = array(
                array('ViewHelper'),
                array('Label', array(
                'class' => 'leftalign ui-button-text'
                )),
                array('HtmlTag', array('tag' => 'div')),
        );
        
        $this->addElement('select', 'province', array(
            'label'      => 'Provincia',
            'required'   => true,
        ));
        $this->province->setRegisterInArrayValidator(false)
                ->setDecorators($decorators)
                ->setAttrib('onChange', 'loadCities();');
        
        $this->addElement('select', 'city', array(
            'label'      => 'Ciudad',
            'required'   => true,
        ));
        $this->city->setRegisterInArrayValidator(false)
                ->setDecorators($decorators);
                
        $this->addElement('button', 'search', array(
            'ignore'   => true,
            'label'      => 'Buscar',
        ));
        $this->search->setAttrib('onChange', 'getPromotions();');
      }
  }
?>
