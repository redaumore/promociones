<?php
  class PAP_View_Helper_ErrorMessage extends Zend_View_Helper_Abstract
{
    public function errorMessage(Zend_Form $form = null, $messages)
    {
        $html = '';
        if(count($messages)){
            $html = '<div id="div_message" class="span6 offset3 alert alert-error">'
                    .'  <button type="button" class="close" data-dismiss="alert">&times;</button>'
                    .'      <ul>';
            $elements = count($messages);
            for($i = 0; $i < $elements; $i++){
                $key = array_keys($messages);
                $mess = $messages[$key[$i]];
                $label = $form->getElement($key[$i]);
                if(isset($label)){
                    $label = $label->getLabel();
                    $key = array_keys($mess);
                    $html .= '               <li><b>'.$label.': </b>'.$mess[$key[0]].'</li>';
                }
                else{
                    $html .= '               <li>'.$mess.'</li>';
                }
            }
            $html .= '      </ul>'
                    .'</div>';
        }
        return $html;
    }
}
?>
