<?php

class PAP_Form_PromotionForm extends Zend_Form
{

    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttrib('enctype', Zend_Form::ENCTYPE_MULTIPART);
        //'multipart/form-data'
        $this->addPrefixPath('PAP_Form_Element_', 'PAP/Form/Element/', 'Element');
        
          
        $decorators = array(
                array('ViewHelper'),
                array('Errors'),
                array('Label', array(
                'class' => 'control-label'
                )),
                array('HtmlTag', array('tag' => 'div')),
        );
        $decoratorsButton = array(
                array('ViewHelper'),
                array('HtmlTag', array('tag' => 'div')),
        );
        
        $this->addElement('text', 'promoCode', array(
            'label'      => 'Código ',
            'required'   => false,
            'size'       => 16,
            'maxsize'    => 50,
            'readonly'   => 'true',
        ));
        $control = $this->getElement("promoCode");
        $control->setDecorators($decorators);
        
        $this->addElement('textarea', 'shortDescription', array(
            'label'      => 'Texto ',
            'required'   => true,
            'size' => 60, 
            'maxlength' => 60,
            'filters'    => array('StringTrim', 'StripTags'),
            //'validators' => array(
            //    array('regex', false, '/^[a-zA-Z0-9 áéíóúñÁÉÍÓÚÑ.!*+¡¿?$@,;:-]+$/')),
        ));
        $control = $this->getElement('shortDescription');
        $validator = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
        $control->addValidator($validator, true);
        $control->setDecorators($decorators);
        $control->setOptions(array('rows' => '2','cols' => '30'));
        
        /*
        $this->addElement('textarea', 'long_description', array(
            'label'      => 'Descripción larga:',
            'required'   => true,
            'size' => 60, 
            'maxlength' => 250,
            'filters'    => array('StringTrim'),
        ));
        $control->addValidator($validator, true);
        $control = $this->getElement("long_description");
        $control->setDecorators($decorators);*/
        
        
        $allowWhiteSpace = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
        $control = new Zend_Form_Element_TextArea('longDescription');
        $control->setLabel('Desc. larga ')
            ->setOptions(array('rows' => '5','cols' => '30'))
            ->setRequired(true)
            //->addFilter('HTMLEntities', 'UTF-8')
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true)
            //->addValidator($allowWhiteSpace)
            //->addValidator('regex', false, '/^[a-zA-Z0-9 áéíóúñÁÉÍÓÚÑ.!*+¡¿?$@,;:-]+$/')
            ->addDecorators($decorators);
        $this->addElement($control);
        
        
        $this->addElement('text', 'starts', array(
            'label'      => 'Desde ',
            'required'   => true,
            'size' => 10, 
            'maxlength' => 10,
        ));
        $control = $this->getElement('starts');
        $control->addValidator('Date',false, array('format'=>'dd/MM/yyyy'));
        $control->setDecorators($decorators);
                
        $control = $this->addElement('text', 'ends', array(
            'label'      => 'Hasta ',
            'required'   => true,
            'size' => 10, 
            'maxlength' => 10,
        ));
        $control = $this->getElement('ends');
        $control->addValidator('Date',false, array('format'=>'dd/MM/yyyy'));
        $control->setDecorators($decorators);
        
        $this->addElement('text', 'promoValue', array(
            'label'      => 'Valor ',
            'required'   => true,
            'size' => 8, 
            'maxlength' => 8,
            'style' => 'text-align:right;',
            'validators' => array(
                array('Float', true, array('locale' => 'en_US')),
            ),
        ));
        $control = $this->getElement('promoValue');
        $control->setDecorators($decorators);
        
        $this->addElement('checkbox', 'valueSince', array(
          'label'   => 'Precio "desde" ',
          'required'=> false, 
          'value'   => 0,  
        ));
        $control = $this->getElement('valueSince');
        $control->setDecorators($decorators);
        
        $this->addElement('text', 'quantity', array(
            'label'      => 'Unidades ',
            'required'   => false,
            'size' => 4, 
            'maxlength' => 8,
            'validators' => array(
                'Int',
            ),
        ));
        $control = $this->getElement('quantity');
        $control->setDecorators($decorators);
        
        $this->addElement('select', 'promoType', array(
            'label'      => 'Tipo Promo ',        //P=producto S=Servicio
            'required'   => true,
        ));
        $control = $this->getElement('promoType');
        $control->addMultioption('P', 'Producto')
                ->addMultioption('S', 'Servicio');
        $control->setDecorators($decorators);
        
        $control = $this->addElement('text', 'displayedText', array(
            'label'      => 'Título ',      //2x1, Liquidación, etc
            'required'   => true,
            'size' => 25, 
            'validators' => array(
                'StringLength',
            ),
        ));
        $control = $this->getElement('displayedText');
        //$control->addValidator('regex',true,array('^([A-Za-z0-9.,@?!%]*)$')); //^([A-Za-z0-9-'.,&@:?!()$#/\\]*)$
        $control->setDecorators($decorators);
        
        $control = new Zend_Form_Element_Radio('alertType');
        $control->setLabel('Alertas')
                ->setMultiOptions(array(
                'D' => 'Mostror por días',
                'Q' => 'Mostrar por cantidad',
                'N' => 'No mostrar'
                ))
                ->setOptions(array('id' => 'alertType'))
                ->setAttrib('label_class', 'leftalign ui-button-text');
        $this->addElement($control);
        $this->alertType->setAttrib('class', 'leftalign ui-button-text');
                
        $this->addElement('select', 'state', array(
            'label'      => 'Estado',
            'required'   => true,
        ));
        $control = $this->getElement("state");
        $control->addMultiOptions(array(
                    'A' => 'Activa',
                    'I' => 'Inactiva',
                        ));
        $control->setDecorators($decorators);
        
        $this->addElement('select', 'promoCost', array(
            'label'      => 'Costo',
            'required'   => true,
        ));
        $control = $this->getElement('promoCost');
        $control->setRegisterInArrayValidator(false);
        $control->setDecorators($decorators);
        
        $this->addElement('text', 'visited', array(
            'label'      => 'Visitas',      //2x1, Liquidación, etc
            'required'   => false,
            'size' => 5, 
        ));
        $this->visited->setAttrib('readonly', true)
                ->setDecorators($decorators);
        
        $file = new Zend_Form_Element_File('filePromo');
        $file->setLabel('Imagenes Promo')
            ->setDestination(PUBLIC_PATH.'/images/tmp')
            ->setRequired(false)
            //->setIsArray(true)
            //->setMultiFile(3)
            ->setValueDisabled(true);
        // ensure only one file
        $file->addValidator('Count', false, 1);
        // max 2MB
        $file->addValidator('Size', false, 1048576)
            ->setMaxFileSize(1048576);
        $file->addValidator('Extension', false, 'jpg,png,gif')
            ->setAttrib('onChange', 'sub(this)')
            ->setDecorators(array(
                    'File',
                    'Errors',
                    array('HtmlTag', array('tag'=>'div','style'=>'height:0px; width:0px; overflow:hidden;'))));
        $this->addElement($file);
        //@todo Hacerlo para 3 imàgenes
        
        $this->addElement('button', 'fakefile', array(
            'ignore'   => true,
            'label'      => 'Cambiar',
        ));
        $this->fakefile->setAttrib('class', 'btn btn-primary')
            ->setAttrib("onClick", "clickFile();");
        
        $this->addElement('img', 'imagePromo', array(
            'label'     => 'Imagen',
            'ignore'   => true,
            'width' => '75', 
            'height' => '75',
        ));
        $this->imagePromo->setDecorators($decorators);
        
        //$div = new PAP_Form_Element_Div('totalCost');
        //$div->setValue('Costo de la promo')->setAttrib('class', 'costo');
        //$this->addElement($div);
        
        $this->addElement('multiselect', 'branches', array(
            'label'     => 'Sucursales',
            'ignore'   => true,
        ));
        $this->branches->setRegisterInArrayValidator(false)
                ->setDecorators($decorators)
                ->setAttrib('class', 'invisible');
        
        $this->addElement('hidden', 'userId');
        $this->addElement('hidden', 'promoId');
        
        $this->addElement('submit', 'save', array(
            'ignore'   => true,
            'label'      => 'Guardar',
        ));
        $control = $this->getElement("save");
        $control->setDecorators($decoratorsButton);
        $control->setAttrib('class', 'btn');
    }
}

