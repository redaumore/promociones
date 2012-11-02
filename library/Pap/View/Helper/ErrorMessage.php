<?php
  class PAP_View_Helper_ErrorMessage extends Zend_View_Helper_Abstract
{
    public function errorMessage(Zend_Form $form = null, $messages)
    {
        $html = '';
        if(count($messages)){
            $html = '<div class="ui-widget">'
                    .'  <div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">'
                    //.'      <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>Errores de Validación.</p>'
                    .'      <ul>';
            $elements = count($messages);
            for($i = 0; $i < $elements; $i++){
                $key = array_keys($messages);
                $mess = $messages[$key[$i]];
                $label = $form->getElement($key[$i]);
                if(isset($label)){
                    $label = $label->getLabel();
                    $key = array_keys($mess);
                    $html .= '               <li style="font-size: .6em;"><b>'.$label.': </b>'.$mess[$key[0]].'</li>';
                }
                else{
                    $html .= '               <li style="font-size: .6em;">'.$mess.'</li>';
                }
            }
            $html .= '      </ul>'
                    .'   </div>'
                    .'</div>';
        }
        return $html;
    }
}
?>
