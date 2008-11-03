<?php

require_once dirname(__FILE__).'/FormField.php';

class FormFieldTextarea extends FormField {
	
	public $type = 'textarea';
	
	public $attributes = array(
		'rows' => 3, 'cols' => 55
	);

	public function __construct($name, $value = null, Array $attributes = array()) {
		parent::__construct($name, null, $attributes);
		unset($this->attributes['type']);
		$this->tagValue = $value;
		$this->tagName = $this->type;
		return $this;
	}
	
	public function value($value = null) {
		if (func_num_args() > 0) {
			$this->tagValue = $value;
			return $this;
		} else {
			return parent::value($value);
		}
	}
	
}

?>