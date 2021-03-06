<?php
  class PAP_Form_LoginForm extends Zend_Form
  {
      public function init(){
        // Set the method for the display form to POST
        
        $this->setMethod('post');
        //$this->setAction('/login/login');
        $decorators = array(
                'ViewHelper',
                array('Label', array(
                'class' => 'rightalign ui-button-text'
                )), 
                array('HtmlTag', array('tag' => 'div')),
        );
 
        // Add an email element
        $this->addElement('text', 'email', array(
            'label'      => 'Usuario (email)',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'EmailAddress',
            ),
        ));
        $this->email->setDecorators($decorators);
        
        // Add a password element
        $this->addElement('password', 'password', array(
            'label'      => 'Contraseña',
            'required'   => true,
            'filters'    => array('StringTrim', 'StringToUpper'),
            'validators' => array(
                'alnum',
            ),
        ));
        $this->password->setDecorators($decorators);
        
        $this->addElement('submit', 'login', array(
            'ignore'   => true,
            'label'      => 'Ingresar',
        ));
        $decoratorsButton = array(
                array('ViewHelper'),
                array('HtmlTag', array('tag' => 'div')),
        );
        $this->login->setDecorators($decoratorsButton)
                ->setAttrib('class', 'buttons');
      }
  }
