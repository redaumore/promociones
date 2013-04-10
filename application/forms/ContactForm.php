<?php
class PAP_Form_BranchForm extends Zend_Form
  {
      public function init(){
          
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttrib('enctype', Zend_Form::ENCTYPE_MULTIPART);
        //'multipart/form-data'
        $this->addPrefixPath('PAP_Form_Element_', 'PAP/Form/Element/', 'Element');
        $this->addDecorators(array('FormElements', 'Form'));
        
        $decorators = array(
                array('ViewHelper'),
                array('Label', array(
                'class' => 'leftalign ui-button-text'
                )),
                array('HtmlTag', array('tag' => 'div')),
        );
        $allowWhiteSpace = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
        $decoratorsButton = array(
                array('ViewHelper'),
                array('HtmlTag', array('tag' => 'div')),
        );
                
        $this->addElement('text', 'name', array(
            'label'      => 'Nombre',
            'size' => 40,
            'required'   => true,
            'filters'    => array('StringTrim'),
        ));
      }
  }