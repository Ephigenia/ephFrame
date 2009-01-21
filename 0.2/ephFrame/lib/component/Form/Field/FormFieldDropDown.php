<?php

//require_once dirname(__FILE__).'/FormFieldMultipleOptions.php';

/**
 * 	DropDown (select) input field
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 04.12.2008
 * 	@package ephFrame
 *	@subpackage ephFrame.lib.component.Form.Field
 */
class FormFieldDropDown extends FormField {
	
	public $multiple = false;
	
	public $tagName = 'select';
	
	public $options = array();
	
	public $size = 1;
	
	public function __construct($name, $value = null, Array $attributes = array()) {
		if (!isset($attributes['size'])) {
			$attributes['size'] = &$this->size;
		} else {
			$this->size = &$attributes['size'];
		}
		parent::__construct($name, $value, $attributes);
		$this->attributes->remove('type');
		$this->value($value);
		$this->afterConstruct();
		return $this;
	}
	
	public function addOption($value = null, $label = null) {
		if ($value == null) {
			$value = $label;
		}
		if ($label == null) {
			$label = $value;			
		}
		$childOption = new HTMLTag('option', array('value' => $value), $label);
		$this->addChild($childOption);
	}
	
	public function value($value = null) {
		if (func_num_args() == 1) {
			foreach($this->children as $child) {
				if ($child->attributes->value != $value) continue;
				$child->attributes->set('selected', 'selected');
			}
			return $this;
		}
		return parent::value();
	}
	
	public function select($value) {
		return $this->value($value);
	}
	
	public function options(Array $options = array()) {
		$this->options = array();
		foreach($options as $value => $label) {
			$this->addOption($value, $label);
		}
		return $this;
	}
	
	public function addOptions(Array $options = array()) {
		return $this->options($options);
	}
	
}

?>