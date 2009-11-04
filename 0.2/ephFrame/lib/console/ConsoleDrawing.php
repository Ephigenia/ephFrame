<?php

class_exists('Console') or require dirname(__FILE__).'/Console.php';

define('WACS_ULCORNER',	"\342\224\214");
define('WACS_URCORNER',	"\342\224\220");
define('WACS_LLCORNER',	"\342\224\224");
define('WACS_LRCORNER',	"\342\224\230");
define('WACS_HLINE',	"\342\224\200");
define('WACS_VLINE',	"\342\224\202");

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
 * Console Drawing class
 * 
 * This class can help you moving the cursor in the console and drawing lines.
 * The coordinate system starts in the upper left corner of the console window
 * with 1,1 as the first character in the upper left corner. So there'no position
 * like 0,0, 0,1 or 1,0 on the screen. Keep that in mind when you use all the
 * other classes that draw stuff in the console like {@link ConsoleHistogram},
 * {@link ConsoleList} or {@link ConsoleWindow}
 * 
 * So this example should draw a line of 10 characters in the second line of
 * the console with a padding from left of 1 character:
 * <code>
 * $c = new ConsoleDrawing();
 * $c->drawLine(2,1,12,1);
 * </code>
 * 
 * @since 19.04.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.console
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 */
class ConsoleDrawing extends Console
{	
	/**
	 * Move the cursor to a specific location in the terminal
	 * @param integer $x
	 * @param integer $y
	 */
	public function moveCursor($x, $y) {
		$this->out(chr(27).'['.$y.';'.$x.'f');
	}
	
	/**
	 * Draws a line into the console, horizontal and vertical, no diagonal
	 * @param integer $x1
	 * @param integer $y1
	 * @param integer $x2
	 * @param integer $y2
	 * @param string $char char used for line drawing, default is -
	 * @return boolean true on finish
	 */
	public function drawLine ($x1, $y1, $x2, $y2, $char = '─') {
		// horizontal lines
		if ($x2 > $x1) {
			$this->moveCursor($x1, $y1);
			$this->out(str_repeat($char, $x2 - $x1));
		// vertical lines
		} else if ($y2 > $y1) {
			$length = $y2 - $y1;
			for ($i = 0; $i < $length; $i++) {
				$this->moveCursor($x1, $y1 + $i);
				$this->out($char);
			}
		}
		return true;
	}

}