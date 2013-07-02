<?php
class PAP_Form_ContactForm extends Zend_Form
  {
      public function init(){
          $this->setMethod(Zend_Form::METHOD_POST);
          $this->setAttrib('enctype', Zend_Form::ENCTYPE_MULTIPART);
          //'multipart/form-data'
          $this->addPrefixPath('PAP_Form_Element_', 'PAP/Form/Element/', 'Element');
          $this->addElement('hidden', 'periods');
          $this->addElement('hidden', 'amount');
          $this->addElement('hidden', 'cbu');
          $this->addElement('hidden', 'bank');
      }
  }