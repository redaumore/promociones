<?php

require_once 'Zend/Form/Element/Xhtml.php';
    
class PAP_Form_Element_Img extends Zend_Form_Element_Xhtml
{
    
    public $helper = 'formImg';

    public function loadDefaultDecorators ()
    {
        parent::loadDefaultDecorators ();
        $this->removeDecorator ('Label');
        $this->removeDecorator ('HtmlTag');

        $this->addDecorator('HtmlTag', array (
        'tag'   => 'span',
        'class' => 'spanImg',
        ));
    }
}