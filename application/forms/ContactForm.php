<?php
class PAP_Form_ContactForm extends Zend_Form
  {
      public function init(){
          
        // Set the method for the display form to POST
        $this->setMethod(Zend_Form::METHOD_POST);
        $this->setAttrib('enctype', Zend_Form::ENCTYPE_MULTIPART);
        //'multipart/form-data'
        $this->addPrefixPath('PAP_Form_Element_', 'PAP/Form/Element/', 'Element');
        $this->addDecorators(array('FormElements', 'Form'));
        
        $decorators = array(
                array('ViewHelper'),
                array('Label', array(
                'class' => 'control-label'
                )),
                array('HtmlTag', array('tag' => 'div')),
        );
        
        $decoratorsButton = array(
                array('ViewHelper'),
                array('HtmlTag', array('tag' => 'div')),
        );
                
        $this->addElement('text', 'name', array(
            'label'      => 'Nombre ',
            'size' => 40,
            'required'   => true,
            'filters'    => array('StringTrim'),
        ));
        $this->name->addDecorators($decorators);
        
         // Add an email element
        $this->addElement('text', 'email', array(
            'label'      => 'Email',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'size' => 50,
            'maxlength' => 50,
            'validators' => array(
                'EmailAddress', 
            )
        ));
        $this->email->addDecorators($decorators);
        
        $control = new Zend_Form_Element_TextArea('message');
        $control->setLabel('Mensaje ')
            ->setOptions(array('rows' => '10','cols' => '50'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true)
            ->addDecorators($decorators);
        $control->addValidator('stringLength', true, array(0, 500));
        $this->addElement($control);
      
      
        $this->addElement('submit', 'send', array(
            'label'      => 'Enviar'
        ));
        $this->send->setDecorators($decoratorsButton)
                ->setAttrib('class', 'btn');
      }
  }