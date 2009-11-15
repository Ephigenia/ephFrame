<?php

class_exists('ConsoleDrawing') or require dirname(__FILE__).'/../ConsoleDrawing.php';

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

/**
 * Draws a histogram-like graph into the console
 * 
 * <code>
 * 
 * </code>
 * 
 * @since 19.04.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.console
 */
class ConsoleHistogram extends ConsoleDrawing
{	
	public $x;
	public $y;
	public $width = 60;
	public $height = 5;
	public $data = array();
	public $indicator = '+';
	public $emptyIndicator = ' ';
	public $title;
	public $titleFormat = '[ %title% ]';
	public $relativeMax = true;
	
	public function __construct($x, $y, $width = null, $height = null) {
		foreach(array('x','y','width','height') as $index => $val) {
			if (${$val} !== null || func_num_args() > $index+1) {
				$this->{$val} = ${$val};
			}
		}
		$this->data = array_pad($this->data, $width, 0);
		return $this;
	}
	
	public function draw($value = null) {
		if ($value !== null) {
			$this->data[] = $value;
		}
		$lastValues = array_slice($this->data, -$this->width);
		if ($this->relativeMax) {
			$max = max($lastValues);
		} else {
			$max = max($this->data);
		}
		$i = 0;
		foreach($lastValues as $index => $value) {
			if ($max != 0) {
				$height = round(($value / $max) * $this->height);
			} else {
				$height = 0;
			}
			$this->drawVerticalBar($this->x + $i + 1, $this->y + $this->height, $height, $this->height, $this->indicator);
			$i++;
		}
		// render title on the chart
		if ($this->title) {
			$this->drawTitle($this->title);
		}
		return true;
	}
	
	public function drawTitle($title) {
		$this->moveCursor($this->x + 3, $this->y + 1);
		$this->out(strtr($this->titleFormat, array('%title%' => $title)));
		return true;
	}
	
	private function drawVerticalBar ($x, $y, $value, $height, $char = '#') {
		if ($value > $height) {
			$value = $height;
		}
		if ($value == 0) {
			for ($yi = 0; $yi < $height; $yi++) {
				$this->moveCursor($x, $y - $yi);
				$this->out($this->emptyIndicator);
			}
			$this->moveCursor($x, $y);
			$this->out('_');
			return true;
		}
		$onHeight = $value;
		$offHeight = $height-$value;
		$this->drawLine($x, $y - $height + 1, $x, $y + 1, $this->emptyIndicator);
		$this->drawLine($x, $y - $onHeight + 1, $x, $y + 1, $char);
		return true;
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ConsoleHistogramException extends ConsoleDrawingException {}