<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright 2007+, Ephigenia M. Eichner, Kopernikusstr. 8, 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 	@license http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright Copyright 2007+, Ephigenia M. Eichner
 * 	@link http://code.ephigenia.de/projects/ephFrame/
 * 	@filesource
 */

loadInterface('ephFrame.lib.StringFilter');

/**
 * 	A Class for beautifying SGML strings
 *
 * 	This is based on Slawomir Jasinski class http://www.jasinski.us
 * 
 * 	THIS IS NOT FINISHED CLASS
 *
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de
 * 	@since 18.03.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class SGMLBeautifierFilter extends Object implements StringFilter {

	public $indentString = TAB;
	
	/**
	 * 	This method can be somehow improved, splitting the string only one time
	 *
	 * 	@param string $string input string (the sgml code)
	 * 	@param integer $wrap wrap lines after $wrap characters
	 * 	@return string
	 */
	function apply($string, $wrap = null) {
		
		// prepare string for beautifying
		// what does this line do?
		$string = preg_replace('/<\?[^>]+>/', '', $string);
		$string = String::trimEveryLine($string);
		// what happens here?
		$string = preg_replace('/>([\s]+)<\//', '></', $string);
		// adding \n lines where tags ar near
		$string = str_replace('><', '>'.LF.'<', $string);
		
		// exploding - each line is one XML tag
		$tmp = explode(LF, $string);
		$lineCount = count($tmp);
		
		// array storing the capsulated tags
		$stab = array('');
		
		// indent
		for ($i = 0; $i <= $lineCount; $i++) {
			$line =& $tmp[$i];
			$add = true;
			if (preg_match('/<([^\/\s>]+)/', $line, $match)) {
				$lan = trim(strtr($match[0], '<>', '  '));
			} else {
				$lan = false;
			}
			$level = count($stab);
			// closing any tag
			if (in_array($lan, $stab) && substr_count($line, '</'.$lan) == 1) {
				$level--;
				$s = array_pop($stab);
				$add = false;
			}
			if (substr_count($line, '<'.$lan) == 1 && substr_count($line, '</'.$lan) == 1) $add = false;
			if (preg_match('/\/>$/', $line, $match)) $add = false;
			// indent depending on tag level
			$line = str_repeat($this->indentString, $level-1).$line;
			// wrap lines
			if (!is_null($wrap)) {
				$line = wordwrap($line, $wrap, LF.str_repeat($this->indentString, $level-1));
			}
			if ($add && !@in_array($lan, $stab) && $lan != '') array_push($stab, $lan);
		}
		return trim(join(LF, $tmp));
	}

}

?>