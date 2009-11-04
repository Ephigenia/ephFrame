<?php

class_exists('ConsoleDrawing') or require dirname(__FILE__).'/ConsoleDrawing.php';
class_exists('ConsoleWindow') or require dirname(__FILE__).'/ConsoleWindow.php';
class_exists('IndexedArray') or require dirname(__FILE__).'/../IndexedArray.php';

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
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

/** 
 * Draws a Progress-Bar into the Console
 * 
 * Example Output, 40/60 Done, the current location indicator rotates:
 * <code>
 * 40/60 [============================/--------------------]
 * </code>
 * 
 * @since 19.04.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.console
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 */
class ConsoleProgressBar extends ConsoleWindow
{	
	protected $width = 30;
	protected $height = 1;
	public $max = 1.;
	public $value = .0;
	
	public $drawBorder = false;
	public $background = ' ';
	
	const TYPE_COUNTER = 'counter';
	const TYPE_PERCENT = 'percent';
	public $type = self::TYPE_COUNTER;
	
	public $drawChars = array(
		'[', // left delimeter of progressbar 
		'.', // progressbar off str
		'=', // progressbar on str
		']', // right delimeter of progressbar
		''	// indicator of current value
	);
	
	public function __construct($x, $y, $width, $max) {
		$this->x = $x;
		$this->y = $y;
		$this->width = $width;
		$this->max = $max;
		$this->drawChars[4] = new IndexedArray('-', '\\', '|', '/');
		$this->redraw();
		return $this;
	}
	
	public function value($value) {
		$this->value = $value;
		if ($this->max < $this->value) {
			$this->max = $this->value;
		}
		return $this->redraw();
	}
	
	public function increase() {
		return $this->value($this->value++);
	}
	
	public function redraw() {
		if ($this->max > 0) {
			$percent = $this->value / $this->max;
		} else {
			$percent = 0.0;
		}
		// render content
		$rendered = '';
		// percentage display
		if ($this->type == self::TYPE_PERCENT) {
			$rendered .= str_pad(floor($percent * 100), 3, $this->background, STR_PAD_LEFT).'%'.$this->background;
		}
		// counter display
		if ($this->type = self::TYPE_COUNTER) {
			$rendered .= str_pad($this->value, strlen($this->max), $this->background, STR_PAD_LEFT).'/'.$this->max.$this->background;
		}
		$progressBarWidth = $this->width - strlen($rendered) - 3;
		$progressBarOnWidth = floor($progressBarWidth * $percent);
		// progressbar
		$rendered .= $this->drawChars[0];
		if ($progressBarOnWidth > 0) {
			$rendered .= str_repeat($this->drawChars[2], $progressBarOnWidth);
		}
		if ($percent != 1) {
			if ($this->drawChars[4] instanceof IndexedArray) {
				$rendered .= $this->drawChars[4]->cycle();
			} else {
				$rendered .= $this->drawChars[4];
			}
		} else {
			$rendered .= $this->drawChars[2];
		}
		$rendered .= str_repeat($this->drawChars[1], $progressBarWidth-$progressBarOnWidth);
		$rendered .= $this->drawChars[3];
		$this->content = $rendered;
		parent::redraw();
	}
	
}