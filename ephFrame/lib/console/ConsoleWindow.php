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

class_exists('ConsoleDrawing') or require dirname(__FILE__).'/ConsoleDrawing.php';

/**
 * Box Drawing class in Console
 * 
 * This class will help you create boxes in the console with content in it. The
 * content will auto scroll if it's to long for the box. The borders of the box
 * can be custumized by changing the {@link boxChars} propterty of this class.
 * 
 * See this example, that creates a box in the console and fills it with random
 * characters.
 * <code>
 * $b = new ConsoleWindow(5, 5, 20, 10);
 * for ($i = 0; $i < 100; $i++) {
 * $b->content(chr(rand(34,120)), true);
 * usleep(0.02 * 1000000); // ms
 * }
 * </code>
 * 
 * Example Output:
 * <code>
 * ┌─[ Drawing ]──────┐
 * │ very long        │
 * │ character shit   │
 * │ Automatic        │
 * │ Console Drawing  │
 * │ x Drawing        │
 * │                  │
 * │                  │
 * │                  │
 * └──────────────────┘
 * </code>
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.console
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 28.07.2008
 */
class ConsoleWindow extends ConsoleDrawing
{	
	/**
	 * Characters, used for box drawing
	 * @var array(string)
	 */
	public $boxChars = array(
		WACS_HLINE,		// horizontal line
		WACS_VLINE,		// vertical line
		WACS_ULCORNER,	// upper left corner
		WACS_URCORNER,	// upper right corner
		WACS_LLCORNER,	// lower left corner
		WACS_LRCORNER
	);
	public $drawBorder = true;
	public $background = ' ';
	
	protected $x = 0;
	protected $y = 0;
	protected $width = 0;
	protected $height = 0;
	
	public $title;
	protected $titleFormat = '[ %s ]';
	
	/** 
	 * Stores the current content of the box.
	 * Please notice that if you change the content string on your own, you
	 * need to call redrawContent for display.
	 * @var string
	 */
	public $content = '';
	
	/**
	 * Creates a new {@link ConsoleBox} in the Terminal window with the passed
	 * propterties.
	 * @param integer $x
	 * @param integer $y
	 * @param integer $width
	 * @param integer $height
	 * @param string $content initial content of the box
	 */
	public function __construct($x, $y, $width, $height, $content = '', $drawBorder = null, $background = null) {
		$this->x = $x;
		$this->y = $y;
		$this->width = $width;
		$this->height = $height;
		$this->content = $content;
		if ($drawBorder !== null) {
			$this->drawBorder = $drawBorder;
		}
		if ($background !== null) {
			$this->background = $background;
		}
		$this->redraw();
		return $this;
	}
	
	/**
	 * Change the $content of the box or $append something to the content.
	 * Right after the content is set the box content will redraw.
	 * @param string $content
	 * @param boolean $append
	 * @return ConsoleBox
	 */
	public function content($content, $append = false) {
		if ($append) {
			$this->content .= $content;
		} else {
			$this->content = $content;
		}
		$this->redraw();
		return $this;
	}
	
	/**
	 * Sets a new window title, the title is automatically shortened if it's
	 * to long for the current window {@link width}
	 * @param string $title
	 * @return ConsoleWindow
	 */
	public function title($title) {
		$this->title = $title;
		$this->redraw();
		return $this;
	}
	
	/**
	 * Redraw the hole box including border and content
	 * @return ConsoleBox
	 */
	public function redraw() {
		if ($this->drawBorder) {
			$this->drawBorder();
		}
		$this->redrawContent();
		$this->redrawTitle();
		return $this;
	}
	
	private function redrawContent() {		
		if ($this->drawBorder) {
			$width = $this->width - 4;
			$height = $this->height - 2;
			$x = $this->x + 2;
			$y = $this->y + 1;
		} else {
			$width = $this->width;
			$height = $this->height;
			$x = $this->x;
			$y = $this->y;
		}
		// draw background (only in the area where no border is)
		if (!empty($this->background)) {
			if ($this->drawBorder) {
				$xBg = $x - 1;
				$bgWidth = $width + 2;
			} else {
				$bgWidth = $width;
				$xBg = $x;
			}
			for($i = 0; $i < $height; $i++) {
				$this->drawLine($xBg, $y + $i, $xBg + $bgWidth, $y + $i, $this->background);
			}
		}
		if (!$this->drawBorder && !empty($this->title)) {
			$height -= 1;
			$y += 1;
		}
		// don't draw content if there's none
		if (empty($this->content)) return $this;
		// auto scroll content
		$content = wordwrap($this->content, $width, "\n", true);
		$lines = explode(LF, $content);
		if (count($lines) >= $height) {
			$lines = array_slice($lines, -$height);	
		}
		foreach($lines as $i => $line) {
			$this->moveCursor($x, $y + $i);
			$this->out(str_pad($line, $width, $this->background, STR_PAD_RIGHT));
		}
		return $this;
	}
	
	private function redrawTitle() {
		// do nothing if the title is empty
		if (empty($this->title)) {
			return $this;
		}
		$titleMaxLength = $this->width - strlen($this->titleFormat) + 2;
		// set x/y coords for the title display
		if ($this->drawBorder) {
			$this->moveCursor($this->x + 2, $this->y);
			$titleMaxLength -= 4;
		} else {
			$this->moveCursor($this->x, $this->y);
		}
		// auto shorten title if it's to long for the current window width
		$title = $this->title;
		if (strlen($title) > $titleMaxLength) {
			$floorMiddle = floor($titleMaxLength / 2);
			if ($floorMiddle * 2 != $titleMaxLength) {
				$ceilMiddle = $floorMiddle-1;
			} else {
				$ceilMiddle = $floorMiddle-2;
			}
			$title = substr($title, 0, $floorMiddle).'..'.substr($title, - $ceilMiddle);
		}
		// finally print the title to the window
		$this->out(sprintf($this->titleFormat, $title));
		$this->moveCursor($this->x + $this->width, $this->y + $this->height - 1);
		return $this;
	}
	
	private function drawBorder() {
		$x1 = $this->x;
		$y1 = $this->y;
		$x2 = $this->x + $this->width - 1;
		$y2 = $this->y + $this->height - 1;
		// do nothing if box is too small for drawing anything
		if ($x1 == $x2 || $y1 == $y2) return true;
		// lines
		$this->drawLine($x1, $y1, $x2, $y1, $this->boxChars[0]);
		$this->drawLine($x2, $y1, $x2, $y2, $this->boxChars[1]);
		$this->drawLine($x1, $y2, $x2, $y2, $this->boxChars[0]);
		$this->drawLine($x1, $y1, $x1, $y2, $this->boxChars[1]);
		// corners
		// ul
		$this->moveCursor($x1, $y1);
		$this->out($this->boxChars[2]);
		// ur
		$this->moveCursor($x2, $y1);
		$this->out($this->boxChars[3]);
		// ll
		$this->moveCursor($x1, $y2);
		$this->out($this->boxChars[4]);
		// lr
		$this->moveCursor($x2, $y2);
		$this->out($this->boxChars[5]);
		return true;
	}

}


/**
 * Automatic Test if ConsoleDrawing is called directly, so this file should
 * create 4 windows and fill it with random words. If that doesn't work
 * something must be wrong :D
 */
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
	$randomWords = array('Automatic', 'Console', 'Drawing', 'Test', 'x', 'very long character shit');
	$window1 = new ConsoleWindow(1, 1, 20, 10, implode(' ', $randomWords)."\n");
	$window2 = new ConsoleWindow(23, 1, 19, 10, implode(' ', $randomWords)."\n", false, '.');
	$window3 = new ConsoleWindow(1, 12, 20, 10);
	$window4 = new ConsoleWindow(23, 12, 19, 10, null, false, '.');
	$window5 = new ConsoleWindow(44, 12, 20, 10, null, false, '.');
	usleep(1 * 1000000); // ms
	for($i = 0; $i < 100; $i++) {
		$word = $randomWords[array_rand($randomWords)];
		$window1->content($word.' ', true);
		$window2->content($word.' ', true);
		$window3->content($word.' ', true);
		$window4->content($word.' ', true);
		$window5->content($word.' ', true);
		$window3->title($word);
		$window4->title($word);
		$window5->title($word);
		
		usleep(1.00 * 1000000); // ms 
	}
	exit;
}