<?php

class PAP_Model_Image
{
       protected $_path;
       
       public function __construct($path){
           $this->_path = $path;
       }
       
       public function setImage($text){
            $this->_path = (string) $text;
            return $this;
       }
       
       public function getPath(){
            return $this->_path;
       }

}

