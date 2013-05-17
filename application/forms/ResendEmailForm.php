<?php
  class PAP_Form_ResendEmailForm extends Zend_Form
  {
      public function init(){
        // Set the method for the display form to POST
        $this->setMethod('post');
        //$this->addElement('hidden', 'email', array('disableLoadDefaultDecorators' => true));
        $this->addElement('submit', 'reenviar', array(
            'ignore'   => true,
            'label'      => 'Re-Enviar Email',
        ));
        $this->reenviar->setAttrib('class', 'btn btn-large');
        
        $this->addElement('hidden', 'email');
      }
  }
?>
