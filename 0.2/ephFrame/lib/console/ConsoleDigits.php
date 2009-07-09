<?php

class_exists('ConsoleWindow') or require dirname(__FILE__).'/ConsoleWindow.php';

/**
 * ephFrame: <http://code.moresleep.net/project/ephFrame/>
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
 * Console Drawing rather large digits
 * 
 * This class can draw you some 'large' digits.
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.console
 */
class ConsoleDigits extends ConsoleWindow
{	
	protected $digits = array(
		 0 	=> '#### ## ## ####' // 0
		,1 	=> '  # ### #  #  #' // 1
		,2 	=> ' # # #  ##  ###' // 2
		,3 	=> '###  # ##  ####' // 3
		,4 	=> '# ## ####  #  #' // 4
		,5 	=> '####  ###  ####' // 5
		,6 	=> '####  #### ####' // 6
		,7 	=> '###  ####  #  #' // 7
		,8 	=> '#### ##### ####' // 8
		,9 	=> '#### ####  ####' // 9
		,'/' => '  # #  #  # #  ' // /
	);
	
	protected $digitWidth = 3;
	protected $digitHeight = 5;
	protected $space = 1;
	public $background = '.';
	public $drawBorder = false;
	
	public function __construct($x, $y, $value) {
		parent::__construct($x, $y, strlen($value) * $this->digitWidth, $this->digitHeight);
		$this->value = $value;
		foreach($this->digits as $i => $v) {
			$v = str_replace(' ', $this->background, $v);
			$this->digits[$i] = str_split($v, $this->digitWidth);
		}
		return $this;
	}
	
	public function value($i) {
		$newWidth = $this->digitWidth * strlen($i) + strlen($i)-1 * $this->space;
		//if ($newwidth > $this->width) {
			$this->width = $newWidth;
		//}
		$chars = str_split(strval($i));
		$content = '';
		for ($line = 0; $line < $this->digitHeight; $line++) {
			foreach($chars as $charIndex => $char) {
				$content .= $this->digits[(int)$char][$line];
				if (count($chars) > 1 && $charIndex != count($chars)-1) {
					$content .= $this->background;
				}
			}
			if ($line < $this->digitHeight-1) {
				$content .= LF;
			}
		}
		$this->content($content);
		$this->redraw();
	}
	
}

/**
 * If you call this class directly by "php ConsoleDigits.php" you get a 
 * testing display of a countdown from 130 to 0
 */
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	$d = new ConsoleDigits(3,3,0);
	for ($i = 0; $i < 130; $i++) {
		$d->value($i);
		usleep(0.25 * 1000000); // ms
	}
	echo LF.LF.LF;
	exit;
}