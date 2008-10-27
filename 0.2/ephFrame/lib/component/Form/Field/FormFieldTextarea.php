<?php

require_once dirname(__FILE__).'/FormField.php';

class FormFieldTextarea extends FormField {
	
	public $type = 'textarea';

	public function __construct($name, $value = null, Array $attributes = array()) {
		parent::__construct($name, null, $attributes);
		$this->tagValue = $value;
		$this->tagName = $this->type;
		return $this;
	}
	
	public function value($value = null) {
		
	}
	
}

?>