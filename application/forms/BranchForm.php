<?php
  class PAP_Form_BranchForm extends Zend_Form
  {
      public function init(){
          
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttrib('enctype', Zend_Form::ENCTYPE_MULTIPART);
        //'multipart/form-data'
        $this->addPrefixPath('PAP_Form_Element_', 'Pap/Form/Element/', 'Element');
        $this->addDecorators(array('FormElements', 'Form'));
          
        $decorators = array(
                'ViewHelper',
                'Label',
                /*array(
                'requiredSuffix' => ' *',
                'class' => 'leftalign'
                ),*/
                array('HtmlTag', array('tag' => 'div')),
        );
        $decoratorsButton = array(
                array('HtmlTag', array('tag' => 'div')),
        );
        
        $controlAttribs = array(
            'readonly' => 'true',
        );
        
        
        $this->addElement('text', 'name', array(
            'label'      => 'Comercio:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            //'filters'    => array('StringTrim', 'HtmlEntities'),
        ));
        $control = $this->getElement("name");
        $control->setDecorators($decorators);
        $validator = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
        $control->addValidator($validator, true);
                //->setAttrib("escape",true);
        //$control->setAttribs($controlAttribs);
        
        $this->addElement('text', 'street', array(
            'label'      => 'Calle:',
            'required'   => true,
            'filters'    => array('StringTrim', 'StringtoUpper'),
        ));
        $control = $this->getElement("street");
        $control->setDecorators($decorators);
        $validator = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
        $control->addValidator($validator, true);
        
        $this->addElement('text', 'number', array(
            'label'      => 'Número:',
            'required'   => true,
            'size' => 8,
            'filters'    => array('Digits'),
            'validators' => array(
                'Digits',
            ),
        ));
        $control = $this->getElement("number");
        $control->setDecorators($decorators);
        
        $this->addElement('text', 'local', array(
            'label'      => 'Local:',
            'required'   => false,
            'filters'    => array('StringTrim'),
        ));
        $control = $this->getElement("local");
        $control->setDecorators($decorators);
        
        $this->addElement('text', 'phone', array(
            'label'      => 'Teléfono:',
            'required'   => false,
            'size' => 30, 
            'maxlength' => 50,
            'filters'    => array('StringTrim'),
        ));
        $control = $this->getElement("phone");
        $control->setDecorators($decorators);
        
        $this->addElement('select', 'province', array(
            'label'      => 'Provincia:',
            'required'   => true,
        ));
        $control = $this->getElement("province");
        $control->setRegisterInArrayValidator(false);
        $control->setDecorators($decorators);
        
        $this->addElement('select', 'city', array(
            'label'      => 'Ciudad:',
            'required'   => true,
        ));
        $control = $this->getElement("city");
        $control->setRegisterInArrayValidator(false);
        $control->setDecorators($decorators);
        
        $this->addElement('text', 'zipcode', array(
            'label'      => 'CP:',
            'required'   => false,
            'size' => 8,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'alnum',
            ),
        ));
        $control = $this->getElement("zipcode");
        $control->setDecorators($decorators);
        
        $this->addElement('text', 'lat', array(
            'label'      => 'Latitud:',
            'size' => 12,
            'required'   => true,
            'validators' => array(
                'Float',
            ),
        ));
        $control = $this->getElement("lat");
        $control->setDecorators($decorators);
        $control->setAttribs($controlAttribs);
                
        $this->addElement('text', 'lng', array(
            'label'      => 'Longitud:',
            'size' => 12,
            'required'   => true,
            'validators' => array(
                'Float',
            ),
        ));
        $control = $this->getElement("lng");
        $control->setDecorators($decorators);
        $control->setAttribs($controlAttribs);
        
        $this->addElement('button', 'localization', array(
            'ignore'   => true,
            'label'      => 'Localizarme',
        ));
        $control = $this->getElement("localization");
        $control->setDecorators($decoratorsButton);
        $control->class = "buttons";
        $control->setAttrib("onClick", "ShowGoogleLocalizator();");
        
        $file = new Zend_Form_Element_File('file');
        $file->setLabel('Cambiar Logo')
            ->setDestination(PUBLIC_PATH.'\\images\\tmp')
            ->setRequired(true)
            ->setValueDisabled(true);
        // ensure only one file
        $file->addValidator('Count', false, 1);
        // max 2MB
        $file->addValidator('Size', false, 1048576)
            ->setMaxFileSize(1048576);
        // only JPEG, PNG, or GIF
        $file->addValidator('Extension', false, 'jpg,png,gif');
        $this->addElement($file);
        
        $this->addElement('img', 'logo', array(
            'ignore'   => true,
            'width' => '100', 
            'height' => '100'
        ));
        
        $this->addElement('submit', 'save', array(
            'ignore'   => true,
            'label'      => 'Guardar',
        ));
        $control = $this->getElement("save");
        $control->setDecorators($decoratorsButton);
        $control->class = "buttons";
        
        $this->addElement('hidden', 'user');
        $this->addElement('hidden', 'branch_id', array(
            'ignore' => true,
            )
        );
        $this->addElement('hidden', 'branch_order', array(
            'ignore' => true,
            )
        );
        $this->addElement('hidden', 'latitude');
        $this->addElement('hidden', 'longitude');
        
        $this->addDisplayGroup(array(
                    'name',
                    'street',
                    'number',
                    'local',
                    'phone',
                    'city',
                    'province',
                    'zipcode',
            ),'contact',array('legend' => 'Información del Comercio'));
      }
  }
