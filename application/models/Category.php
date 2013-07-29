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
                            array('id'=>1001, 'title'=>'Accesorios y repuestos', 'children'=>array()),
                            array('id'=>1002, 'title'=>'Servicios', 'children'=>array()),
                            )),
                        array('id'=>1100, 'title'=>'Bebes', 'children'=>array()),    
                        array('id'=>1200, 'title'=>'Bebidas y Delicatessen', 'children'=>array()),    
                        array('id'=>1300, 'title'=>'Calzados', 'children'=>array(    
                            array('id'=>1301, 'title'=>'Hombre', 'children'=>array()),
                            array('id'=>1302, 'title'=>'Dama', 'children'=>array()),
                            array('id'=>1303, 'title'=>'Niños', 'children'=>array()),
                            )),
                        array('id'=>1400, 'title'=>'Cocina', 'children'=>array(    
                            array('id'=>1401, 'title'=>'Restaurant', 'children'=>array()),
                            array('id'=>1402, 'title'=>'Delivery', 'children'=>array()),
                            array('id'=>1403, 'title'=>'Artículos comestibles', 'children'=>array()),
                            )),
                        array('id'=>1500, 'title'=>'Computación', 'children'=>array()),    
                        array('id'=>1600, 'title'=>'Construcción y Ferretería', 'children'=>array()),    
                        array('id'=>1700, 'title'=>'Deportes', 'children'=>array(    
                            array('id'=>1701, 'title'=>'Indumentaria', 'children'=>array()),
                            array('id'=>1702, 'title'=>'Artículos deportivos', 'children'=>array()),
                            )),
                        array('id'=>1800, 'title'=>'Electrónica y Electrodomésticos', 'children'=>array()),    
                        array('id'=>1900, 'title'=>'Entretenimientos', 'children'=>array()),    
                        array('id'=>2000, 'title'=>'Hogar', 'children'=>array(    
                            array('id'=>2001, 'title'=>'Muebles', 'children'=>array()),
                            array('id'=>2002, 'title'=>'Decoración', 'children'=>array()),
                            )),
                        array('id'=>2100, 'title'=>'Indumentaria', 'children'=>array(    
                            array('id'=>2101, 'title'=>'Hombre', 'children'=>array()),
                            array('id'=>2102, 'title'=>'Dama', 'children'=>array()),
                            array('id'=>2103, 'title'=>'Niños', 'children'=>array()),
                            )),
                        array('id'=>2200, 'title'=>'Joyas y Relojes', 'children'=>array()),    
                        array('id'=>2300, 'title'=>'Juguetes', 'children'=>array()),    
                        array('id'=>2400, 'title'=>'Libros y Librería', 'children'=>array()),    
                        array('id'=>2500, 'title'=>'Marroquinería y Bolsos', 'children'=>array()),    
                        array('id'=>2600, 'title'=>'Mascotas', 'children'=>array()),    
                        array('id'=>2700, 'title'=>'Musica', 'children'=>array(    
                            array('id'=>2701, 'title'=>'Instrumentos', 'children'=>array()),
                            array('id'=>2702, 'title'=>'Producciones musicales', 'children'=>array()),
                            )),
                        array('id'=>2800, 'title'=>'Optica', 'children'=>array()),    
                        array('id'=>2900, 'title'=>'Perfumeria y Cosmética', 'children'=>array()),    
                        array('id'=>3000, 'title'=>'Pinturas', 'children'=>array()),    
                        array('id'=>3100, 'title'=>'Regalos', 'children'=>array()),    
                        array('id'=>3200, 'title'=>'Servicios Varios', 'children'=>array()),    
                        array('id'=>3300, 'title'=>'Telefonía', 'children'=>array()),
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

