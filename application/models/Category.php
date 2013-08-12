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
                        array('id'=>1700, 'title'=>'Gastronomía', 'children'=>array(
                            array('id'=>1701, 'title'=>'Artículos Comestibles', 'children'=>array()),
                            array('id'=>1702, 'title'=>'Bebidas y Delicatessen', 'children'=>array()),    
                            array('id'=>1703, 'title'=>'Delivery', 'children'=>array()),
                            array('id'=>1704, 'title'=>'Dietética y Herboristería', 'children'=>array()),
                            array('id'=>1705, 'title'=>'Heladería', 'children'=>array()),
                            array('id'=>1706, 'title'=>'Restaurantes', 'children'=>array()),
                            )),
                        array('id'=>1800, 'title'=>'Hogar', 'children'=>array(    
                            array('id'=>1801, 'title'=>'Bazar', 'children'=>array()),
                            array('id'=>1802, 'title'=>'Construcción y ferretería', 'children'=>array()),
                            array('id'=>1803, 'title'=>'Decoración', 'children'=>array()),
                            array('id'=>1804, 'title'=>'Electricidad', 'children'=>array()),
                            array('id'=>1805, 'title'=>'Iluminaciónción', 'children'=>array()),
                            array('id'=>1806, 'title'=>'Muebles', 'children'=>array()),
                            array('id'=>1807, 'title'=>'Pinturas', 'children'=>array()),
                            )),
                        array('id'=>1900, 'title'=>'Indumentaria y Accesorios', 'children'=>array(    
                            array('id'=>1901, 'title'=>'Hombre', 'children'=>array()),
                            array('id'=>1902, 'title'=>'Dama', 'children'=>array()),
                            array('id'=>1903, 'title'=>'Niños', 'children'=>array()),
                            array('id'=>1904, 'title'=>'Marroquinería y Bolsos', 'children'=>array()),
                            array('id'=>1905, 'title'=>'Joyas y Relojes', 'children'=>array()),    
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
                            array('id'=>2201, 'title'=>'Audio', 'children'=>array()),
                            array('id'=>2202, 'title'=>'Computación', 'children'=>array()),
                            array('id'=>2203, 'title'=>'Electrodomésticos', 'children'=>array()),
                            array('id'=>2204, 'title'=>'Telefonía', 'children'=>array()),
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

