<?php
     
 class PAP_Form_Element_Div extends Zend_Form_Element {
     /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formDiv';

    public function __construct($spec, $options = null) {
        parent::__construct($spec, $options);
        $this->removeDecorator('label');
        $this->removeDecorator('htmlTag');

    }

}
