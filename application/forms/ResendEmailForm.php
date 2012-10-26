<?php
  class PAP_Form_ResendEmailForm extends Zend_Form
  {
      public function init(){
        // Set the method for the display form to POST
        $this->setMethod('post');
        //$this->addElement('hidden', 'email', array('disableLoadDefaultDecorators' => true));
        $submit = new Zend_Form_Element_Image('submit', array(
            'ignore' => true,
            'label'  => 'Enviar Email',
            'src'    => '/images/backend/shamrock.png',
            'decorators' => array('ViewHelper')
        ));
        $this->addElement($submit);
        $this->addElement('hidden', 'email');
      }
  }
?>
