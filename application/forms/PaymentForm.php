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
                'pendientes' => ' Cargos Pendientes',
                'actual' => ' Período Actual',
                'ultimos' => ' Últimos 6 períodos'
                ))
                ->setOptions(array('id' => 'reportType'))
                ->setAttrib('label_class', 'leftalign ui-button-text')
                ->setDecorators($decorators)
                ->setValue('pendientes');
                
        $this->addElement($control);
        
        $this->addElement('submit', 'search', array(
            'ignore'   => true,
            'label'      => 'Buscar',
        ));
        $this->search->setAttrib('class', 'btn');
        $this->search->setDecorators(array(
                            'ViewHelper',
                            array(array('emptyrow'=>'HtmlTag'), array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'tag'=>'i', 'class'=>'icon-search')),
        ));
        
        $this->addElement('button', 'pay', array(
            'ignore'   => true,
            'label'      => 'Informar Pago por Transferencia/Depósito',
        ));
        $this->pay->setAttrib('class', 'btn')
                    ->setAttrib('onClick', 'showPaymentInfo()')
                    ->setDecorators($decoratorsButton);
        
        $this->addElement('button', 'payMP', array(
            'ignore'   => true,
            'label'      => 'Pagar con MercadoPago',
        ));
        $this->payMP->setAttrib('class', 'btn')
                    ->setAttrib('onClick', 'showMercadoPago()')
                    ->setDecorators($decoratorsButton);
            
        $this->addElement('hidden', 'data');
        $this->data->setDecorators($decorators);
        
      }
  }