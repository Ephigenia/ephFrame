<?php

require_once dirname(__FILE__).'/FormField.php';

class FormFieldEmail extends FormField {

	public $validate = array(
		'invalid' => array(
			'regexp' => Validator::EMAIL,
			'message' => 'Invalid Email'
		)
	);
	
}

?>