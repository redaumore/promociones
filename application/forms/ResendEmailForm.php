<?php
  class PAP_Form_ResendEmailForm extends Zend_Form
  {
      public function init(){
        // Set the method for the display form to POST
        $this->setMethod('post');
        //$this->addElement('hidden', 'email', array('disableLoadDefaultDecorators' => true));
        $this->addElement('submit', 'reenviar', array(
            'ignore'   => true,
            'label'      => 'Enviar Email',
        ));
        $this->reenviar->setAttrib('class', 'buttons');
        
        $this->addElement('hidden', 'email');
      }
  }
?>
