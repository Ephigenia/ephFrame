<?php

namespace ephFrame\HTML\Form\Element;

class URL extends Element
{
	public $attributes = array(
		'type' => 'url',
		'maxlength' => 255,
	);
	
	public $defaultProtocol = 'http';
	
	public function submit($data)
	{
		if (!empty($data) && $this->defaultProtocol && !preg_match('~^\w+://~', $data)) {
			$data = $this->defaultProtocol.'://'.$data;
		}
		parent::submit($data);
	}
	
	protected function defaultValidators()
	{
		return array(
			new \ephFrame\validator\URL(),
		);
	}
}