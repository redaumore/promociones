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
                //array('requiredSuffix' => ' *', 'class' => 'leftalign')
                //),
                array('HtmlTag', array('tag' => 'div')),
        );
        $allowWhiteSpace = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
        $decoratorsButton = array(
                array('ViewHelper'),
                array('HtmlTag', array('tag' => 'div')),
        );
                
        $this->addElement('text', 'name', array(
            'label'      => 'Nombre',
            'size' => 50,
            'required'   => true,
            'filters'    => array('StringTrim'),
        ));
        $this->name->addValidator($allowWhiteSpace, true)
                    ->setDecorators($decorators)
                    ->addDecorator('HtmlTag', array('tag' => 'div'));
        
        $this->addElement('text', 'street', array(
            'label'      => 'Calle',
            'size' => 40,
            'required'   => true,
            'filters'    => array('StringTrim', 'StringtoUpper'),
        ));
        $this->street->setDecorators($decorators)
            ->addValidator($allowWhiteSpace, true)
            ->setAttrib('onChange', 'clearCoord();');
        
        $this->addElement('text', 'number', array(
            'label'      => 'Número',
            'required'   => true,
            'size' => 8,
            'filters'    => array('Digits'),
            'validators' => array(
                'Digits',
            ),
        ));
        $this->number->setDecorators($decorators)
            ->setAttrib('onChange', 'clearCoord();');
        
        $this->addElement('text', 'local', array(
            'label'      => 'Local',
            'required'   => false,
            'filters'    => array('StringTrim'),
        ));
        $this->local->setDecorators($decorators);
        
        $this->addElement('text', 'phone', array(
            'label'      => 'Teléfono',
            'required'   => false,
            'size' => 15, 
            'maxlength' => 50,
            'filters'    => array('StringTrim'),
        ));
        $this->phone->setDecorators($decorators);
        
        $this->addElement('select', 'province', array(
            'label'      => 'Provincia',
            'required'   => true,
        ));
        $this->province->setRegisterInArrayValidator(false)
                ->setDecorators($decorators);
        
        $this->addElement('select', 'city', array(
            'label'      => 'Ciudad',
            'required'   => true,
        ));
        $this->city->setRegisterInArrayValidator(false)
                ->setDecorators($decorators)
                ->setAttrib('onChange', 'clearCoord();');
        
        $this->addElement('text', 'zipcode', array(
            'label'      => 'CP',
            'required'   => false,
            'size' => 8,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'alnum',
            ),
        ));
        $this->zipcode->setDecorators($decorators);
        
        $this->addElement('text', 'lat', array(
            'label'      => 'Lat.',
            'size' => 12,
            'required'   => true,
            //'validators' => array(
            //    'Float',
            //),
        ));
        $this->lat->setDecorators($decorators)
                ->setAttribs(array('readonly' => 'true',));
                
        $this->addElement('text', 'lng', array(
            'label'      => 'Long.',
            'size' => 12,
            'required'   => true,
            //'validators' => array(
            //    'Float',
            //),
        ));
        $this->lng->setDecorators($decorators)
                ->setAttribs(array('readonly' => 'true',));
        
        $this->addElement('button', 'localization', array(
            'ignore'   => true,
            'label'      => 'Localizarme',
        ));
        $this->localization->setDecorators($decoratorsButton)
                    ->setAttrib('class', 'buttons')
                    ->setAttrib("onClick", "ShowGoogleLocalizator();");
        
        $this->addElement('img', 'logo', array(
            'ignore'   => true,
            'width' => '75', 
            'height' => '75',
            'src' => '/images/backend/img-comercio.png',
        ));
        
        $file = new Zend_Form_Element_File('filebranch');
        $this->addElement($file);
        $this->filebranch->setLabel('Cambiar Logo')
                ->setDestination(PUBLIC_PATH.'\\images\\tmp')
                ->setRequired(true)
                ->setValueDisabled(true)
                ->addValidator('Count', false, 1)// ensure only one file
                ->addValidator('Size', false, 1048576)// max 2MB
                ->setMaxFileSize(1048576)
                //->setDecorators($decoratorsButton)
                ->setDecorators(array(
                    'File',
                    'Errors',
                    array('HtmlTag', array('tag'=>'div','style'=>'height:0px; width:0px; overflow:hidden;')),
                ))
                ->setAttrib('onChange', 'sub(this)')
                ->addValidator('Extension', false, 'jpg,png,gif');// only JPEG, PNG, or GIF
        
        $this->addElement('button', 'fakefile', array(
            'ignore'   => true,
            'label'      => 'Cargar Imagen',
        ));
        $this->fakefile->setAttrib('class', 'buttons')
            ->setAttrib("onClick", "clickFile();");
        
        $this->addElement('submit', 'save', array(
            'ignore'   => true,
            'label'      => 'Guardar',
        ));
        $this->save->setDecorators($decoratorsButton)
                ->setAttrib('class', 'buttons');
        
        $this->addElement('submit', 'cancel', array(
            'ignore'   => true,
            'label'      => 'Cancelar',
        ));
        $this->cancel->setDecorators($decoratorsButton)
                ->setAttrib('class', 'buttons');
        
        $this->addElement('hidden', 'user');
        $this->user->setDecorators(array('ViewHelper'));
        $this->addElement('hidden', 'branch_id', array('ignore' => true,));
        $this->branch_id->setDecorators(array('ViewHelper'));
        $this->addElement('hidden', 'branch_order', array('ignore' => true,));
        $this->branch_order->setDecorators(array('ViewHelper'));
        $this->addElement('hidden', 'latitude', array('disableLoadDefaultDecorators' => true));
        $this->latitude->setDecorators(array('ViewHelper'));
        $this->addElement('hidden', 'longitude', array('disableLoadDefaultDecorators' => true));
        $this->longitude->setDecorators(array('ViewHelper'));
        
        $this->addDisplayGroup(array(
                    'name',
                    'street',
                    'number',
                    'local',
                    'phone',
                    'city',
                    'province',
                    'zipcode',
                    'user'
            ),'branch_contact',array('legend' => 'Información del Comercio', 'disableDefaultDecorators' => true));
        $contact = $this->getDisplayGroup('branch_contact');
        $contact->setDecorators(array(
                    'FormElements',
                    'Fieldset',
                    array('HtmlTag',array('tag'=>'div','style'=>'width:50%;float:left;')),
                    //array(array('fstag'=>'HtmlTag'),'options'=>array('tag'=>'fieldset','openOnly'=>true)),
        ));
        
        $this->addDisplayGroup(array(
                'filebranch',
                'logo',
                'fakefile'
        ),'branch_picture',array('legend' => 'Imagen del Comercio', 'disableDefaultDecorators' => true));
        $contact = $this->getDisplayGroup('branch_picture');
        $contact->setDecorators(array(
                    'FormElements',
                    'Fieldset',
                    array('HtmlTag',array('tag'=>'div','style'=>'width:45%;;float:right;')),
                    array(array('fstag'=>'HtmlTag'),'options'=>array('tag'=>'fieldset')),
        ));
            
        $this->addDisplayGroup(array(
                'lat',
                'lng',
                'localization',
                'latitude',
                'longitude'
        ),'branch_location',array('legend' => 'Localización geográfica', 'disableDefaultDecorators' => true,));
        $contact = $this->getDisplayGroup('branch_location');
        $contact->setDecorators(array(
                    'FormElements',
                    'Fieldset',
                    array('HtmlTag',array('tag'=>'div','style'=>'width:50%;;float:left;')),
                    //array(array('fstag'=>'HtmlTag'),'options'=>array('tag'=>'fieldset','closeOnly'=>true)),
        ));
        
        
            
            
      }
  }
