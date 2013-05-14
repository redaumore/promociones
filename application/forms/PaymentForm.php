<?php
 class PAP_Form_PaymentForm extends Zend_Form
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
                'class' => 'control-label'
                )),
        );
        
        //$allowWhiteSpace = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
        $decoratorsButton = array(
                array('ViewHelper'),
                array('HtmlTag', array('tag' => 'div')),
        );
        
        $control = new Zend_Form_Element_Radio('reportType');
        $control->setLabel('Tipo de Reporte')
                ->setMultiOptions(array(
                'actual' => 'Período Actual',
                'pendientes' => 'Cargos Pendientes',
                'ultimos' => 'Últimos períodos'
                ))
                ->setOptions(array('id' => 'reportType'))
                ->setSeparator('')
                ->setAttrib('label_class', 'leftalign ui-button-text')
                ->setDecorators($decorators);
        $this->addElement($control);
        
        $this->addElement('submit', 'search', array(
            'ignore'   => true,
            'label'      => 'Buscar',
        ));
        $this->search->setAttrib('class', 'btn btn-primary');
        
        $this->addElement('button', 'pay', array(
            'ignore'   => true,
            'label'      => 'Informar Pago',
        ));
        $this->pay->setAttrib('class', 'btn')
                    ->setAttrib('onClick', 'showPaymentInfo()');
            
        $this->addElement('hidden', 'data');
      }
  }