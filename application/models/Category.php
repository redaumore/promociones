<?php

class PAP_Model_Category
{
    protected $_category_id;
    protected $_name;
    protected $_parent_id;
    
    
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    public function init($id){
        if(isset($id))
            $this->loadById($id);    
    }
    
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid user property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid user property');
        }
        return $this->$method();
    }
    
    public function getAll(){
        $arrCategories = array(
                        array('id'=>1000, 'title'=>'Automotor', 'children'=>array(
                            array('id'=>1001, 'title'=>'Accesorios y Repuestos', 'children'=>array()),
                            array('id'=>1002, 'title'=>'Servicios Varios', 'children'=>array()),
                        )),
                        array('id'=>1100, 'title'=>'Bebes', 'children'=>array(
                            array('id'=>1101, 'title'=>'Accesorios', 'children'=>array()),
                            array('id'=>1102, 'title'=>'Artículos para Bebé', 'children'=>array()),
                            array('id'=>1103, 'title'=>'Indumentaria', 'children'=>array()),
                        )),
                        array('id'=>1200, 'title'=>'Belleza y Salud', 'children'=>array(
                            array('id'=>1201, 'title'=>'Optica', 'children'=>array()),
                            array('id'=>1202, 'title'=>'Perfumería y Cosmética', 'children'=>array()),
                            array('id'=>1203, 'title'=>'Peluquería', 'children'=>array()),
                            array('id'=>1204, 'title'=>'Salud', 'children'=>array()),
                            array('id'=>1205, 'title'=>'Servicios para Ella y Él', 'children'=>array()),
                            array('id'=>1206, 'title'=>'Tratamientos', 'children'=>array()),
                        )),    
                        array('id'=>1300, 'title'=>'Calzados', 'children'=>array(    
                            array('id'=>1301, 'title'=>'Dama', 'children'=>array()),
                            array('id'=>1302, 'title'=>'Hombre', 'children'=>array()),
                            array('id'=>1303, 'title'=>'Niños', 'children'=>array()),
                            )),
                        array('id'=>1400, 'title'=>'Cultura, Ocio y Entretenimiento', 'children'=>array(    
                            array('id'=>1401, 'title'=>'Cds de Música', 'children'=>array()),
                            array('id'=>1402, 'title'=>'Instrumentos Musicales', 'children'=>array()),
                            array('id'=>1403, 'title'=>'Juguetería', 'children'=>array()),
                            array('id'=>1404, 'title'=>'Libros y Revistas', 'children'=>array()),
                            array('id'=>1405, 'title'=>'Películas', 'children'=>array()),
                            )),
                        array('id'=>1500, 'title'=>'Deportes', 'children'=>array(    
                            array('id'=>1501, 'title'=>'Artículos Deportivos', 'children'=>array()),
                            array('id'=>1502, 'title'=>'Indumentaria', 'children'=>array()),
                            array('id'=>1503, 'title'=>'Instalaciones Deportivas', 'children'=>array()),
                            )),
                        array('id'=>1600, 'title'=>'Educación y Oficina', 'children'=>array(
                            array('id'=>1601, 'title'=>'Artículos de Oficina', 'children'=>array()),    
                            array('id'=>1602, 'title'=>'Librería', 'children'=>array()),
                            array('id'=>1603, 'title'=>'Libros Escolares', 'children'=>array()),
                            array('id'=>1604, 'title'=>'Uniformes Escolares', 'children'=>array()),
                            )),
                        array('id'=>2300, 'title'=>'Fiestas y Eventos', 'children'=>array(
                            array('id'=>2301, 'title'=>'Eventos', 'children'=>array()),    
                            array('id'=>2302, 'title'=>'Disfraces y Cotillón', 'children'=>array()),
                            )),
                        array('id'=>1700, 'title'=>'Gastronomía', 'children'=>array(
                            array('id'=>1701, 'title'=>'Artículos Comestibles', 'children'=>array()),
                            array('id'=>1702, 'title'=>'Bebidas y Delicatessen', 'children'=>array()),    
                            array('id'=>1703, 'title'=>'Delivery', 'children'=>array()),
                            array('id'=>1704, 'title'=>'Dietética y Herboristería', 'children'=>array()),
                            array('id'=>1705, 'title'=>'Heladería', 'children'=>array()),
                            array('id'=>1706, 'title'=>'Restaurantes', 'children'=>array()),
                            )),
                        array('id'=>1800, 'title'=>'Hogar, Muebles y Jardín', 'children'=>array(    
                            array('id'=>1801, 'title'=>'Bazar', 'children'=>array()),
                            array('id'=>1802, 'title'=>'Construcción y ferretería', 'children'=>array()),
                            array('id'=>1803, 'title'=>'Decoración', 'children'=>array()),
                            array('id'=>1804, 'title'=>'Electricidad', 'children'=>array()),
                            array('id'=>1805, 'title'=>'Jardinería', 'children'=>array()),
                            array('id'=>1806, 'title'=>'Iluminaciónción', 'children'=>array()),
                            array('id'=>1807, 'title'=>'Muebles', 'children'=>array()),
                            array('id'=>1808, 'title'=>'Pinturas', 'children'=>array()),
                            array('id'=>2205, 'title'=>'Electrodomésticos', 'children'=>array()),
                            )),
                        array('id'=>1900, 'title'=>'Indumentaria y Accesorios', 'children'=>array(    
                            array('id'=>1901, 'title'=>'Hombre', 'children'=>array()),
                            array('id'=>1902, 'title'=>'Dama', 'children'=>array()),
                            array('id'=>1903, 'title'=>'Niños', 'children'=>array()),
                            array('id'=>1904, 'title'=>'Marroquinería y Bolsos', 'children'=>array()),
                            array('id'=>1905, 'title'=>'Mercería', 'children'=>array()),
                            array('id'=>1906, 'title'=>'Joyas y Relojes', 'children'=>array()),    
                            )),
                        array('id'=>2000, 'title'=>'Mascotas', 'children'=>array(
                            array('id'=>2001, 'title'=>'Alimentos', 'children'=>array()),
                            array('id'=>2002, 'title'=>'Accesorios', 'children'=>array()),
                            array('id'=>2003, 'title'=>'Servicios', 'children'=>array()),
                        )),    
                        array('id'=>2100, 'title'=>'Musica', 'children'=>array(    
                            array('id'=>2101, 'title'=>'Instrumentos', 'children'=>array()),
                            array('id'=>2102, 'title'=>'Producciones musicales', 'children'=>array()),
                            )),
                        array('id'=>2200, 'title'=>'Tecnología', 'children'=>array(
                            array('id'=>2201, 'title'=>'Audio y Electrónica', 'children'=>array()),
                            array('id'=>2202, 'title'=>'Cámaras y Accesorios', 'children'=>array()),
                            array('id'=>2203, 'title'=>'Celulares y Telefonía', 'children'=>array()),
                            array('id'=>2204, 'title'=>'Computación y Consolas', 'children'=>array()),
                        )),
        );
        return $arrCategories;
    }
     
    public static function getAllBackend(){
        $arrCategories = array(
                        1000 => array('title'=>'Automotor', 'children'=>array(
                            1001 => array('title'=>'Accesorios y Repuestos', 'children'=>array()),
                            1002 => array('title'=>'Servicios Varios', 'children'=>array()),
                        )),
                        1100 => array('title'=>'Bebes', 'children'=>array(
                            1101 => array('title'=>'Accesorios', 'children'=>array()),
                            1102 => array('title'=>'Artículos para Bebé', 'children'=>array()),
                            1103 => array('title'=>'Indumentaria', 'children'=>array()),
                        )),
                        1200 => array('title'=>'Belleza y Salud', 'children'=>array(
                            1201 => array('title'=>'Optica', 'children'=>array()),
                            1202 => array('title'=>'Perfumería y Cosmética', 'children'=>array()),
                            1203 => array('title'=>'Peluquería', 'children'=>array()),
                            1204 => array('title'=>'Salud', 'children'=>array()),
                            1205 => array('title'=>'Servicios para Ella y Él', 'children'=>array()),
                            1206 => array('title'=>'Tratamientos', 'children'=>array()),
                        )),    
                        1300 => array('title'=>'Calzados', 'children'=>array(    
                            1301 => array('title'=>'Dama', 'children'=>array()),
                            1302 => array('title'=>'Hombre', 'children'=>array()),
                            1303 => array('title'=>'Niños', 'children'=>array()),
                            )),
                        1400 => array('title'=>'Cultura, Ocio y Entretenimiento', 'children'=>array(    
                            1401 => array('title'=>'Cds de Música', 'children'=>array()),
                            1402 => array('title'=>'Instrumentos Musicales', 'children'=>array()),
                            1403 => array('title'=>'Juguetería', 'children'=>array()),
                            1404 => array('title'=>'Libros y Revistas', 'children'=>array()),
                            1405 => array('title'=>'Películas', 'children'=>array()),
                            )),
                        1500 => array('title'=>'Deportes', 'children'=>array(    
                            1501 => array('title'=>'Artículos Deportivos', 'children'=>array()),
                            1502 => array('title'=>'Indumentaria', 'children'=>array()),
                            1503 => array('title'=>'Instalaciones Deportivas', 'children'=>array()),
                            )),
                        1600 => array('title'=>'Educación y Oficina', 'children'=>array(
                            1601 => array('title'=>'Artículos de Oficina', 'children'=>array()),    
                            1602 => array('title'=>'Librería', 'children'=>array()),
                            1603 => array('title'=>'Libros Escolares', 'children'=>array()),
                            1604 => array('title'=>'Uniformes Escolares', 'children'=>array()),
                            )),
                         2300 => array('title'=>'Fiestas y Eventos', 'children'=>array(
                            2301 => array('title'=>'Eventos', 'children'=>array()),    
                            2302 => array('title'=>'Disfraces y Cotillón', 'children'=>array()),
                            )),
                        1700 => array('title'=>'Gastronomía', 'children'=>array(
                            1701 => array('title'=>'Artículos Comestibles', 'children'=>array()),
                            1702 => array('title'=>'Bebidas y Delicatessen', 'children'=>array()),    
                            1703 => array('title'=>'Delivery', 'children'=>array()),
                            1704 => array('title'=>'Dietética y Herboristería', 'children'=>array()),
                            1705 => array('title'=>'Heladería', 'children'=>array()),
                            1706 => array('title'=>'Restaurantes', 'children'=>array()),
                            )),
                        1800 => array('title'=>'Hogar, Muebles y Jardín', 'children'=>array(    
                            1801 => array('title'=>'Bazar', 'children'=>array()),
                            1802 => array('title'=>'Construcción y ferretería', 'children'=>array()),
                            1803 => array('title'=>'Decoración', 'children'=>array()),
                            1804 => array('title'=>'Electricidad', 'children'=>array()),
                            1805 => array('title'=>'Jardinería', 'children'=>array()),
                            1806 => array('title'=>'Iluminaciónción', 'children'=>array()),
                            1807 => array('title'=>'Muebles', 'children'=>array()),
                            1808 => array('title'=>'Pinturas', 'children'=>array()),
                            1809 => array('title'=>'Electrodomésticos', 'children'=>array()),
                            )),
                        1900 => array('title'=>'Indumentaria y Accesorios', 'children'=>array(    
                            1901 => array('title'=>'Hombre', 'children'=>array()),
                            1902 => array('title'=>'Dama', 'children'=>array()),
                            1903 => array('title'=>'Niños', 'children'=>array()),
                            1904 => array('title'=>'Marroquinería y Bolsos', 'children'=>array()),
                            1905 => array('title'=>'Mercería', 'children'=>array()),
                            1906 => array('title'=>'Joyas y Relojes', 'children'=>array()),    
                            )),
                        2000 => array('title'=>'Mascotas', 'children'=>array(
                            2001 => array('title'=>'Alimentos', 'children'=>array()),
                            2002 => array('title'=>'Accesorios', 'children'=>array()),
                            2003 => array('title'=>'Servicios', 'children'=>array()),
                        )),    
                        2100 => array('title'=>'Musica', 'children'=>array(    
                            2101 => array('title'=>'Instrumentos', 'children'=>array()),
                            2102 => array('title'=>'Producciones musicales', 'children'=>array()),
                            )),
                        2200 => array('title'=>'Tecnología', 'children'=>array(
                            2201 => array('title'=>'Audio y Electrónica', 'children'=>array()),
                            2202 => array('title'=>'Cámaras y Accesorios', 'children'=>array()),
                            2203 => array('title'=>'Celulares y Telefonía', 'children'=>array()),
                            2204 => array('title'=>'Computación y Consolas', 'children'=>array()),
                        )),
        );
        return $arrCategories;    
    }
    
    public function getFrom($date){
        $config = new PAP_Helper_Config();
        $categoryUpdate = $config->getCategoryUpdate();
        if($date < $categoryUpdate){
            return  $this->getAll();
        }
        return array();
            
    }
    
    public function loadById($id){
        $categoryMapper = new PAP_Model_CategoryMapper();
        $categoryMapper->find($id, $this);
    }
    
    public function setId($text)
    {
        $this->_category_id = (string) $text;
        return $this;
    }
 
    public function getId()
    {
        return $this->_category_id;
    }
    
    public function setName($text)
    {
        $this->_name = (string) $text;
        return $this;
    }
 
    public function getName()
    {
        return $this->_name;
    }
    
    public function setParent($text)
    {
        $this->_parent_id = (string) $text;
        return $this;
    }
 
    public function getParent()
    {
        return $this->_parent_id;
    }

}

