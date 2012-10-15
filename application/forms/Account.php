<?php

class PAP_Form_Account extends Zend_Form
{

    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod(Zend_Form::METHOD_POST);
        $this->setAction('new');
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
            'validators' => array(
                'alnum'
            )
        ));
        
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
        $this->addElement('password', 'repassword', array(
            'label'      => 'Repita Contraseña:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StringtoUpper'),
            'maxlength' => 50,
            'validators' => array(
                'alnum'
            )
        ));
        
        // Repita Contraseña
        $this->addElement('submit', 'registrarse', array(
            'label'      => 'Registrarse'
        ));
        
    }

    //public function getValues(){
     //   return $parent->getValues();
    //}

}

