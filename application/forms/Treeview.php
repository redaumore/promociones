<?php

class PAP_Form_Treeview extends Zend_Form
{

    public function init()
    {
        $this->addPrefixPath('ZFExt_Form_Element', 'ZFExt/Form/Element/', 'Element');
        
        $this->setAttrib('id', 'categoryTree');
        
        $this->setOptions(array(
        'elements' => array(
            'tree' => array(
                'type' => 'treeview',
                'options' => array(
                    'label' => 'Tree:',
                    'multioptions' => array(
                        1000 => array('title'=>'Automotor', 'children'=>array(    
                            1001 => array('title'=>'Accesorios y repuestos', 'children'=>array()),
                            1002 => array('title'=>'Servicios', 'children'=>array()),
                            )),
                        1100 => array('title'=>'Bebes', 'children'=>array()),    
                        1200 => array('title'=>'Bebidas y Delicatessen', 'children'=>array()),    
                        1300 => array('title'=>'Calzados', 'children'=>array(    
                            1301 => array('title'=>'Hombre', 'children'=>array()),
                            1302 => array('title'=>'Dama', 'children'=>array()),
                            1303 => array('title'=>'Niños', 'children'=>array()),
                            )),
                        1400 => array('title'=>'Cocina', 'children'=>array(    
                            1401 => array('title'=>'Restaurant', 'children'=>array()),
                            1402 => array('title'=>'Delivery', 'children'=>array()),
                            1403 => array('title'=>'Artículos comestibles', 'children'=>array()),
                            )),
                        1500 => array('title'=>'Computación', 'children'=>array()),    
                        1600 => array('title'=>'Construcción y Ferretería', 'children'=>array()),    
                        1700 => array('title'=>'Deportes', 'children'=>array(    
                            1701 => array('title'=>'Indumentaria', 'children'=>array()),
                            1702 => array('title'=>'Artículos deportivos', 'children'=>array()),
                            )),
                        1800 => array('title'=>'Electrónica y Electrodomésticos', 'children'=>array()),    
                        1900 => array('title'=>'Entretenimientos', 'children'=>array()),    
                        2000 => array('title'=>'Hogar', 'children'=>array(    
                            2001 => array('title'=>'Muebles', 'children'=>array()),
                            2002 => array('title'=>'Decoración', 'children'=>array()),
                            )),
                        2100 => array('title'=>'Indumentaria', 'children'=>array(    
                            2101 => array('title'=>'Hombre', 'children'=>array()),
                            2102 => array('title'=>'Dama', 'children'=>array()),
                            2103 => array('title'=>'Niños', 'children'=>array()),
                            )),
                        2200 => array('title'=>'Joyas y Relojes', 'children'=>array()),    
                        2300 => array('title'=>'Juguetes', 'children'=>array()),    
                        2400 => array('title'=>'Libros y Librería', 'children'=>array()),    
                        2500 => array('title'=>'Marroquinería y Bolsos', 'children'=>array()),    
                        2600 => array('title'=>'Mascotas', 'children'=>array()),    
                        2700 => array('title'=>'Musica', 'children'=>array(    
                            2701 => array('title'=>'Instrumentos', 'children'=>array()),
                            2702 => array('title'=>'Producciones musicales', 'children'=>array()),
                            )),
                        2800 => array('title'=>'Optica', 'children'=>array()),    
                        2900 => array('title'=>'Perfumeria y Cosmética', 'children'=>array()),    
                        3000 => array('title'=>'Pinturas', 'children'=>array()),    
                        3100 => array('title'=>'Regalos', 'children'=>array()),    
                        3200 => array('title'=>'Servicios Varios', 'children'=>array()),    
                        3300 => array('title'=>'Telefonía', 'children'=>array()),
                    )
                )
            ),
            'submit' => array(
                'type' => 'submit',
                'options' => array(
                    'ignore' => true,
                    'label' => 'Guardar Cambios',
                    'class' => 'btn',
                )
            ),
         
        ),
        ));
       
        $decorators = array(
                array('ViewHelper'),
                array('Errors'),
                array('HtmlTag', array('tag' => 'div', 'id' => 'treeViewCategorias')),
                array(array('data'=>'HtmlTag'), array('tag' => 'td')),
                array(array('row'=>'HtmlTag'),array('tag'=>'tr')),
        );
         
        $this->tree->setDecorators($decorators);
        
        $decorators = array(
                array('ViewHelper'),
                array('Errors'),
                array(array('data'=>'HtmlTag'), array('tag' => 'td', 'align' => 'center')),
                array(array('row'=>'HtmlTag'),array('tag'=>'tr')),
        );
        $this->submit->setDecorators($decorators)
                        ->setAttrib("onClick", "return someCategoryChecked();");
 
        $this->setDecorators(array('FormElements',
                                    array(array('data'=>'HtmlTag'),array('tag'=>'table', 'width' => '740px')),
                                    'Form',));                                       
    }
 //TODO Agregar Agencias de viaje.
}


