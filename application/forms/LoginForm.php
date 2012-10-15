<?php
  class PAP_Form_LoginForm extends Zend_Form
  {
      public function init(){
        // Set the method for the display form to POST
        
        $this->setMethod('post');
        //$this->setAction('/login/login');
 
        // Add an email element
        $this->addElement('text', 'email', array(
            'label'      => 'Usuario (email):',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'EmailAddress',
            ),
        ));
        
        // Add a password element
        $this->addElement('password', 'password', array(
            'label'      => 'Contraseña:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StringtoUpper'),
            'validators' => array(
                'alnum',
            ),
        ));
        
        $this->addElement('submit', 'login', array(
            'ignore'   => true,
            'label'      => 'Ingresar',
        ));
      }
  }
