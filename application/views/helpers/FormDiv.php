<?php
  
class Zend_View_Helper_FormDiv extends Zend_View_Helper_FormElement {


    public function formDiv($name, $value = null, $attribs = null) {  

        $class = '';

        if (isset($attribs['class'])) {
             $class = 'class = "'. $attribs['class'] .'"';
        }

        return "<div $class>$value</div>";
    }

}