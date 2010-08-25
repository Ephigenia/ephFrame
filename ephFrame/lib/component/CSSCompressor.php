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

class_exists('Compressor') or require dirname(__FILE__).'/Compressor.php';

/**
 * CSS Compressor
 *
 * Use this clas to compress css files and css strings. You can also pack
 * CSS Files into one single file by using the {@link CSSPacker}
 * 
 * The Compresser does:
 * # strip any comments, singleline, multiline, whatever
 * # trim new lines and space
 * 
 * The Compressor can't ...
 * # handle css hacks that use comments
 * 
 * <code>
 * // compress the content of a file
 * echo $cssCompressor->compressFile('test.css');
 * </code>
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * @since 12.05.2008
 */
class CSSCompressor extends Compressor 
{
	/**
	 * Compresses the incoming css string and returns it
	 * @param string $css
	 * @return string
	 */
	public function compress($css) 
	{
		$compressed = preg_replace('!
			# comments
			(
				/\*[^*]*\*+([^/][^*]*\*+)*/
			)
			|
			(
				# spaces, new lines, returns n tabs
				\s{2,}|\n|\t|\r|
				# spaces before {
				\s(?={)|
				# spaces in font-family: Arial, Verdana ...
				(?<=,)\s|
				# spaces after :
				(?<=:)\s
			)
			!mix', '', $css);
		return $compressed;
	}	
}