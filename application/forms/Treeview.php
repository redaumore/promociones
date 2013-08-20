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
                    'multioptions' => PAP_Model_Category::getAllBackend() ,
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


