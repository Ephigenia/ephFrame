<?php

require_once dirname(__FILE__).'/FormFieldText.php';

class FormFieldEmail extends FormFieldText {

	public $validate = array(
		'invalid' => array(
			'regexp' => Validator::EMAIL,
			'message' => 'Invalid Email'
		)
	);
	
}

?>