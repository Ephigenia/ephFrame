<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

class_exists('FormField') or require dirname(__FILE__).'/FormField.php';

/**
 * Textarea Form Field
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.component.Form
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 04.11.2008
 */
class FormFieldTextarea extends FormField {
	
	public $type = 'textarea';
	
	/**
	 * Default attributes
	 * @var array(string)
	 */
	public $attributes = array(
		'rows' => 5, 'cols' => 55
	);

	public function __construct($name, $value = null, Array $attributes = array()) {
		parent::__construct($name, null, $attributes);
		unset($this->attributes['type']);
		$this->tagValue = $value;
		$this->tagName = $this->type;
		return $this;
	}
	
	public function rows($rows) {
		$this->attribute('rows', (int) $rows);
		return $this;
	}
	
	public function cols($cols) {
		$this->attribute('cols', (int) $cols);	
	}
	
	public function value($value = null) {
		if (func_num_args() > 0) {
			$this->tagValue = $value;
			return $this;
		} else {
			return parent::value();
		}
	}
	
}