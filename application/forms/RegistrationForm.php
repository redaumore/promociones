<?php
  class PAP_Form_RegistrationForm extends Zend_Form{
      public function init(){
            // Set the method for the display form to POST
        $this->setMethod(Zend_Form::METHOD_POST);
        //$this->setAction('new');
        $this->setAttrib('enctype', Zend_Form::ENCTYPE_MULTIPART);
 
        // Add an email element
        $this->addElement('text', 'email', array(
            'label'      => 'Email:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'maxlength' => 50,
            'validators' => array(
                'EmailAddress', 
            )
        ));
        
        // Nombre del comercio
        $this->addElement('text', 'name', array(
            'label'      => 'Nombre del Comercio:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'maxlength' => 50,
        ));
        
        $validator = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
        $this->getElement('name')->addValidator($validator, true);
        
        // Nombre del comercio
        $this->addElement('text', 'cuit', array(
            'label'      => 'CUIT:',
            'required'   => false,
            'filters'    => array('StringTrim'),
            'maxlength' => 13,
            'validators' => array(
                array('regex', false, '/^[0-9]{2}-[0-9]{8}-[0-9]/')
            )
        ));
        
        // Contraseña
        $this->addElement('password', 'password', array(
            'label'      => 'Contraseña:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StringtoUpper'),
            'maxlength' => 50,
            'validators' => array(
                'alnum'
            )
        ));
        
        // Repita Contraseña
        $this->addElement('password', 'confirmPassword', array(
            'label'      => 'Repita Contraseña:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StringtoUpper'),
            'maxlength' => 50,
            'validators' => array(
                'alnum'
            )
        ));
        
        $this->addElement('hidden', 'rol');
        
        // Repita Contraseña
        $this->addElement('submit', 'registrarse', array(
            'label'      => 'Registrarse'
        ));
      }
  }
?>
