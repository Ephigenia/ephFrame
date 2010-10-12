<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Brunnenstr. 10
 *                      10119 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

class_exists('FormField') or require(dirname(__FILE__).'/FormField.php');

/**
 * DropDown (select) input field
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 04.12.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.component.form.Field
 */
class FormFieldDropDown extends FormField 
{
	public $type = 'dropDown';
	
	public $multiple = false;
	
	public $tagName = 'select';
	
	public $options = array();
	
	public $size = 1;
	
	public function __construct($name, $value = null, Array $attributes = array()) 
	{
		if (!isset($attributes['size'])) {
			$attributes['size'] = &$this->size;
		} else {
			$this->size = &$attributes['size'];
		}
		if (isset($attributes['options'])) {
			$this->addOptions($attributes['options']);
		}
		unset($attributes['label'], $attributes['options']);
		parent::__construct($name, $value, $attributes);
		$this->attributes->remove('type');
		$this->value($value);
		$this->afterConstruct();
		return $this;
	}
	
	public function addOption($value = null, $label = null) 
	{
		if ($value == null) {
			$value = $label;
		}
		if ($label == null) {
			$label = $value;			
		}
		$childOption = new HTMLTag('option', array('value' => $value), $label);
		$this->addChild($childOption);
		return $this;
	}
	
	public function beforeRender() 
	{
		if ($this->attributes->multiple && substr($this->attributes->name, -2) != '[]') {
			$this->attributes->name .= '[]';
		}
		return parent::beforeRender();
	}
	
	public function value($value = null) 
	{
		if (func_num_args() == 0) {
			return parent::value();
		} elseif (func_num_args() > 1) {
			$value = func_get_args();
		}
		if (is_array($value)) {
			foreach($value as $key => $val) {
				foreach($this->children as $child) {
					if (!$this->attributes->multiple && $child->attributes->value != $val) {
						$child->attributes->remove('selected');
						continue;
					}
					$child->attributes->set('selected', 'selected');
				}
			}	
		} else {
			foreach($this->children as $child) {
				if (!$this->attributes->multiple && $child->attributes->value != $value) {
					$child->attributes->remove('selected');
				}
				if ($child->attributes->value == $value) {
					$child->attributes->set('selected', 'selected');
				}
			}
		}
		return $this;
	}
	
	public function select($value) 
	{
		return $this->value($value);
	}
	
	public function options(Array $options = array()) 
	{
		$this->options = array();
		foreach($options as $value => $label) {
			$this->addOption($value, $label);
		}
		return $this;
	}
	
	public function addOptions(Array $options = array()) 
	{
		return $this->options($options);
	}	
}