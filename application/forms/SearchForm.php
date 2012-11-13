<?php
class PAP_Form_SearchForm extends Zend_Form
  {
      public function init(){
   
        $this->setMethod('post');
        $this->setAttrib('enctype', Zend_Form::ENCTYPE_MULTIPART);
        //'multipart/form-data'
        $this->addDecorators(array('FormElements', 'Form'));
        
        $decorators = array(
                array('ViewHelper'),
                array('Label', array(
                'class' => 'leftalign ui-button-text'
                )),
                array('HtmlTag', array('tag' => 'div')),
        );
        
        $this->addElement('select', 'province', array(
            'label'      => 'Provincia',
            'required'   => true,
        ));
        $this->province->setRegisterInArrayValidator(false)
                ->setDecorators($decorators)
                ->setAttrib('onChange', 'loadCities();');
        
        $this->addElement('select', 'city', array(
            'label'      => 'Ciudad',
            'required'   => true,
        ));
        $this->city->setRegisterInArrayValidator(false)
                ->setDecorators($decorators);
        
        $this->addElement('select', 'category', array(
            'label'      => 'Categorias',
            'required'   => false,
            'multiple'   => 'multiple',
        ));
        $this->category->setRegisterInArrayValidator(false)
                ->setDecorators($decorators)
                ->addMultiOption(1000, 'Automotor')    
                ->addMultiOption(1001, '►Accesorios y repuestos')
                ->addMultiOption(1002, '►Servicios')
                ->addMultiOption(1100, 'Bebes')    
                ->addMultiOption(1200, 'Bebidas y Delicatessen')    
                ->addMultiOption(1300, 'Calzados')    
                ->addMultiOption(1301, '►Hombre')
                ->addMultiOption(1302, '►Dama')
                ->addMultiOption(1303, '►Niños')
                ->addMultiOption(1400, 'Cocina')    
                ->addMultiOption(1401, '►Restaurant')
                ->addMultiOption(1402, '►Delivery')
                ->addMultiOption(1403, '►Articulos comestibles')
                ->addMultiOption(1500, 'Computación')    
                ->addMultiOption(1600, 'Construcción y Ferretería')    
                ->addMultiOption(1700, 'Deportes')    
                ->addMultiOption(1701, '►Indumentaria')
                ->addMultiOption(1702, '►Artículos deportivos')
                ->addMultiOption(1800, 'Electrónica y Electrodomésticos')
                ->addMultiOption(1900, 'Entretenimientos')    
                ->addMultiOption(2000, 'Hogar')    
                ->addMultiOption(2001, '►Muebles')
                ->addMultiOption(2002, '►Decoración')
                ->addMultiOption(2100, 'Indumentaria')    
                ->addMultiOption(2101, '►Hombre')
                ->addMultiOption(2102, '►Dama')
                ->addMultiOption(2103, '►Niños')
                ->addMultiOption(2200, 'Joyas y Relojes')    
                ->addMultiOption(2300, 'Juguetes')    
                ->addMultiOption(2400, 'Libros y Librería')    
                ->addMultiOption(2500, 'Marroquinería y Bolsos')    
                ->addMultiOption(2600, 'Mascotas')    
                ->addMultiOption(2601, 'Musica')    
                ->addMultiOption(2602, '►Instrumentos')
                ->addMultiOption(2700, '►Producciones musicales')
                ->addMultiOption(2800, 'Optica')    
                ->addMultiOption(2900, 'Perfumeria y Cosmética')    
                ->addMultiOption(3000, 'Pinturas')    
                ->addMultiOption(3100, 'Regalos')    
                ->addMultiOption(3200, 'Servicios Varios')    
                ->addMultiOption(3300, 'Telefonía');
                
        $this->addElement('button', 'search', array(
            'ignore'   => true,
            'label'      => 'Buscar',
        ));
        $this->search->setAttrib('onClick', 'getPromotions();')
            ->setAttrib('class', 'buttons');
        
        /*CATEGORIAS*/
        }
  }
?>
