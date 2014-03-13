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
                'class' => 'control-label'
                )),
                array('HtmlTag', array('tag' => 'div')),
        );
        
        $this->addElement('select', 'province', array(
            'label'      => 'Provincia',
            'required'   => false,
        ));
        $this->province->setRegisterInArrayValidator(false)
                ->setDecorators($decorators)
                ->setAttrib('onChange', 'loadCities();');
        
        $this->addElement('select', 'city', array(
            'label'      => 'Ciudad',
            'required'   => false,
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
                ->addMultiOption(1001, '└─Accesorios y Repuestos')
                ->addMultiOption(1002, '└─Servicios Varios')
                ->addMultiOption(1100, 'Bebes')
                ->addMultiOption(1101, '└─Accesorios')
                ->addMultiOption(1102, '└─Artículos para Bebé')
                ->addMultiOption(1103, '└─Indumentaria')
                ->addMultiOption(1200, 'Belleza y Salud')
                ->addMultiOption(1201, '└─Optica')
                ->addMultiOption(1202, '└─Perfumería y Cosmética')
                ->addMultiOption(1203, '└─Peluquería')
                ->addMultiOption(1204, '└─Farmacias')
                ->addMultiOption(1205, '└─Servicios para Ella y Él')
                ->addMultiOption(1206, '└─Tratamientos')
                ->addMultiOption(1300, 'Calzados')    
                ->addMultiOption(1301, '└─Dama')
                ->addMultiOption(1302, '└─Hombre')
                ->addMultiOption(1303, '└─Niños')
                ->addMultiOption(1400, 'Cultura, Ocio y Entretenimiento')    
                ->addMultiOption(1401, '└─Cds de Música')
                ->addMultiOption(1402, '└─Instrumentos Musicales')
                ->addMultiOption(1403, '└─Juguetería')
                ->addMultiOption(1404, '└─Libros y Revistas')
                ->addMultiOption(1405, '└─Películas')
                ->addMultiOption(1500, 'Deportes')    
                ->addMultiOption(1501, '└─Artículos Deportivos')
                ->addMultiOption(1502, '└─Indumentaria')
                ->addMultiOption(1503, '└─Instalaciones Deportivas')
                ->addMultiOption(1600, 'Educación y Oficina')
                ->addMultiOption(1601, '└─Artículos de Oficina')    
                ->addMultiOption(1602, '└─Librería')
                ->addMultiOption(1603, '└─Libros Escolares')
                ->addMultiOption(1604, '└─Uniformes Escolares')
                ->addMultiOption(2300, 'Fiestas y Eventos')
                ->addMultiOption(2301, '└─Eventos')    
                ->addMultiOption(2302, '└─Disfraces y Cotillón')
                ->addMultiOption(1700, 'Gastronomía')
                ->addMultiOption(1701, '└─Artículos Comestibles')
                ->addMultiOption(1702, '└─Bebidas y Delicatessen')    
                ->addMultiOption(1703, '└─Delivery')
                ->addMultiOption(1704, '└─Dietética y Herboristería')
                ->addMultiOption(1705, '└─Heladería')
                ->addMultiOption(1706, '└─Restaurantes')
                ->addMultiOption(1800, 'Hogar, Muebles y Jardín')    
                ->addMultiOption(1801, '└─Bazar')
                ->addMultiOption(1802, '└─Construcción y ferretería')
                ->addMultiOption(1803, '└─Decoración')
                ->addMultiOption(1804, '└─Electricidad')
                ->addMultiOption(1805, '└─Jardinería')
                ->addMultiOption(1806, '└─Iluminaciónción')
                ->addMultiOption(1807, '└─Muebles')
                ->addMultiOption(1808, '└─Pinturas')
                ->addMultiOption(1809, '└─Electrodomésticos')
                ->addMultiOption(1900, 'Indumentaria y Accesorios')    
                ->addMultiOption(1901, '└─Hombre')
                ->addMultiOption(1902, '└─Dama')
                ->addMultiOption(1903, '└─Niños')
                ->addMultiOption(1904, '└─Marroquinería y Bolsos')
                ->addMultiOption(1905, '└─Mercería')
                ->addMultiOption(1906, '└─Joyas y Relojes')    
                ->addMultiOption(2000, 'Mascotas')
                ->addMultiOption(2001, '└─Alimentos')
                ->addMultiOption(2002, '└─Accesorios')
                ->addMultiOption(2003, '└─Servicios')
                ->addMultiOption(2100, 'Musica')    
                ->addMultiOption(2101, '└─Instrumentos')
                ->addMultiOption(2102, '└─Producciones musicales')
                ->addMultiOption(2200, 'Tecnología')
                ->addMultiOption(2201, '└─Audio y Electrónica')
                ->addMultiOption(2202, '└─Cámaras y Accesorios')
                ->addMultiOption(2203, '└─Celulares y Telefonía')
                ->addMultiOption(2204, '└─Computación y Consolas');
        
        $this->category->setAttrib("height", "100px");
                
        $this->addElement('button', 'search', array(
            'ignore'   => true,
            'label'      => 'Buscar',
        ));
        $this->search->setAttrib('onClick', 'getPromotions();')
            ->setAttrib('class', 'btn');
        
        /*CATEGORIAS*/
        }
}