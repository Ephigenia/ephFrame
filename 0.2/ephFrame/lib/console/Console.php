<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright (c) 2007+, Ephigenia M. Eichner
 * 						 Kopernikusstr. 8
 * 						 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 
 * 	@license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright		copyright 2007+, Ephigenia M. Eichner
 * 	@link			http://code.ephigenia.de/projects/ephFrame/
 * 	@version		$Revision$
 * 	@modifiedby		$LastChangedBy$
 * 	@lastmodified	$Date$
 * 	@filesource		$HeadURL$
 */

class_exists('Object') or require dirname(__FILE__).'/../../Object.phpFormFieldText.php';

/**
 *	Terminal Class
 * 
 * 	Helpfull class for cli/command line- oriented applications
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 17.04.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class Console extends Object {

	/**
	 * 	Stores the current text format
	 * 	@var array(string)
	 */
	public $textFormat = array(
		 'color' => self::COL_DEFAULT
		,'background' => self::COL_DEFAULT
		,'underlined' => false
		,'blinking' => false
		,'bold' => false
	);
	
	protected $os;
	
	const COL_BLACK = 30;
	const COL_RED = 31;
	const COL_GREEN = 32;
	const COL_YELLOW = 33;
	const COL_BLUE = 34;
	const COL_MAGENTA = 35;
	const COL_CYAN = 36;
	const COL_GREY = 37;
	const COL_DEFAULT = 39;
	
	/**
	 * 	Creats a new Instance of console
	 * 	Pass optional default text and background color that should be used
	 * 	@param integer $textColor
	 * 	@param integer $backgroundColor
	 * 	@return boolean
	 */
	public function __construct($textColor = null, $backgroundColor = null) {
		$this->textFormat['color'] = $textColor == null ? self::COL_DEFAULT : $textColor;
		$this->textFormat['backgroundColor'] = $backgroundColor == null ? self::COL_DEFAULT : $backgroundColor;
		$this->os();
		return true;
	}
	
	/**
	 *	Tries to detect the current operating system and returns it.
	 * 	@return string
	 */
	public function os() {
		if (empty($this->os)) {
			$os = trim(strtolower(php_uname('s')));
			if (empty($os)) {
				$os = PHP_OS; 
			}
			$this->os = $os;
		}
		return $this->os;
	}
	
	public function textFormat($color = null, $backgroundColor = null, $bold = null, $blinking = null, $underlined = null) {
		if ($color === null || func_num_args() == 0) {
			return $this->textFormat;
		}
		if ($color !== null) {
			$this->textFormat['color'] = (int) $textColorOrFormatArray;
		}
		if ($backgroundColor !== null) {
			$this->textFormat['background'] = (int) $backgroundColor;
		}
		if ($bold !== null) {
			$this->textFormat['bold'] = (bool) $bold;
		}
		if ($blinking !== null) {
			$this->textFormat['blinking'] = (bool) $blinking;
		}
		if ($underlined !== null) {
			$this->textFormat['underlined'] = (bool) $underlined;
		}
		return $this;
	}
	
	/**
	 * 	Clears the screen in the console
	 * 	@param boolean $out
	 * 	@return string|boolean
	 */
	public static function clearScreen($out = true) {
		$command = chr(27).'c';
		if (!$out) {
			return $command;
		}
		fwrite(STDOUT, $command);
		return true;
	}
	
	/**
	 *	Prints a message to STDOUT with the given font format
	 * 	@param string $text
	 */
	public function write($text, $color = null, $background = null, $bold = null, $underlined = null, $blinking = null) {
		$colorCode = $this->colorCode($color, $background, $bold, $underlined, $blinking);
		// wrap text if to long
		$lineLength = 80;
		if (strlen($text) > $lineLength) {
			if (substr($text, -1, 1) == LF) {
				$splitWrappedText = split(LF, wordwrap($text, $lineLength));
				$newText = array();
				foreach($splitWrappedText as $line) {
					$newText[] = str_pad($line, $lineLength, ' ');
				}
				$text = implode(LF, $newText);
			} else {
				$text = wordwrap($text, $lineLength);
			}
		}
		$this->out($colorCode.$text);
		return $this;
	}
	
	/**
	 * 	Write the passed $message to the standard error output
	 * 	@param string $str
	 * 	@return Console
	 */
	public function error($str) {
		if (!empty($str)) {
			fputs(STDERR, $str);
		}
		return $this;
	}
	
	/**
	 *	Write $string to standard output
	 * 	@param string $str
	 * 	@return Console
	 */
	public function out($str) {
		fputs(STDOUT, $str);
		return $this;
	}
	
	/**
	 * 	Draws a vertical line into the output
	 *	@param integer $color
	 *	@param integer $backgroundColor
	 *	@param integer $length Length of the line
	 * 	@param string $character
	 */
	public function line($color = null, $backgroundColor = null, $length = 80, $character = '-') {
		return $this->write(str_repeat($character, $length), $color, $backgroundColor);
	}
	
	/**
	 *	Calculates the color code string for setting a new color in a tty console
	 * 	will return an empty string on windows like computers
	 * 	@param integer $textColor
	 * 	@param integer $backgroundColor
	 * 	@param boolean $bold
	 * 	@param boolean $underlined
	 * 	@param boolean $blinking
	 * 	@return string
	 */
	public function colorCode($textColor = null, $backgroundColor = null, $bold = null, $underlined = null, $blinking = null) {
		if ($this->os() == 'windows') {
			return '';
		}
		if ($textColor === null) $textColor = $this->textFormat['color'];
		if ($backgroundColor === null) $backgroundColor = $this->textFormat['background'];
		if ($underlined === null) $underlined = $this->textFormat['underlined'];
		if ($blinking === null) $blink = $this->textFormat['blinking'];
		$prefix = ($underlined) ? ($blink) ? 5 : 4 : ($bold) ? 1 : 0;
		return chr(27).'['.$prefix.';'.$textColor.';'.($backgroundColor + 10).'m';
	}
	
	/**
	 *	Sets the color to default
	 * 	@return boolean
	 */
	public function resetColor() {
		$this->out($this->colorCode(self::COL_DEFAULT, self::COL_DEFAULT, false, false));
		return true;
	}
	
	/**
	 *	Halts PHP waiting for Enter to be pressed and returns the characters
	 * 	@param string $message
	 * 	@param integer $bufferSize Size of the read buffer
	 * 	@return string
	 */
	public function read($message = null, $bufferSize = 2048) {
		if ($message !== null) {
			$this->out($message);
		}
		$string = '';
		while($string == '') {
			$string .= fgets(STDIN, $bufferSize);
		}
		return $string;
	}

	/**
	 *	Pauses execution until enter is pressed
	 * 	@return string String entered till Enter was pressed
	 */
	public function pause() {
		return $this->read();
	}
	
	/**
	 * 	On destruct, reconstruct default colors in console
	 */
	public function __destruct() {
		$this->resetColor();
		return $this;
	}
	
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ConsoleException extends Exception {}

?>