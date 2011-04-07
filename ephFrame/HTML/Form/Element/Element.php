<?php

namespace ephFrame\HTML\Form\Element;

class Element extends \ephFrame\HTML\Tag
{
	public $required;
	
	public $decorators = array();
	
	public $name = 'input';
	
	public $description;
	
	public function __construct($name, $value = null, Array $options = array())
	{
		$options = array('attributes' => array(
			'name' => $name,
			'value' => $value,
		)) + $options;
		parent::__construct($this->name, false, $options['attributes']);
		$this->decorators = array(
			'prepend' => array(
				'label' => new ephFrame\HTML\Form\Decorator\Label($this, $options),
			),
			'append' => array(
				'description' => new ephFrame\HTML\Form\Decorator\Description($this, $options),
			),
			'wrap' => array(
				new ephFrame\HTML\Form\Decorator\HTMLTag($this, $options),
			),
		);
		foreach($options as $k => $v) {
			if (property_exists($this, $k) && $k !== 'attributes') $this->{$k} = $v;
		}
	}
	
	public function __toString()
	{
		$rendered = parent::__toString();
		foreach($this->decorators as $placement => $decorators) {
			switch($placement) {
				default:
				case 'prepend':
					$rendered = implode(PHP_EOL, $decorators).$rendered;
					break;
				case 'wrap':
					foreach($decorators as $decorator) {
						$decorator->options['value'] = $rendered;
						$rendered = (string) $decorator;
					}
					break;
				case 'append':
					$rendered .= implode(PHP_EOL, $decorators);
					break;
			}
		}
		return $rendered;
	}
}