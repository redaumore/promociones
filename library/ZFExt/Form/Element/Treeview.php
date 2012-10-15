<?php
class ZFExt_Form_Element_Treeview extends Zend_Form_Element_Multi
{
	public $helper = 'formTreeView';

    /**
     * MultiCheckbox is an array of values by default
     * @var bool
     */
    protected $_isArray = true;

	public function _getOptionIds($options)
	{
		$result = array();
		foreach ($options as $current_option_id=>$current_option) {
			$result[] = $current_option_id;
			if (isset($current_option['children'])) {
				$result = array_merge($result, $this->_getOptionIds($current_option['children']));
			}
		}
		return $result;
	}

    public function isValid($value, $context = null)
    {
        if ($this->registerInArrayValidator()) {
            if (!$this->getValidator('InArray')) {
                $multi_options = $this->getMultiOptions();
				
				$option_ids = $this->_getOptionIds($multi_options);
                $this->addValidator(
                    'InArray',
                    true,
                    array($option_ids)
                );
            }
        }
        return parent::isValid($value, $context);
    }

	
}