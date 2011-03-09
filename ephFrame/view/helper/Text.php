<?php

namespace ephFrame\view\helper;

class Text extends \ephFrame\view\Helper
{
	public function autoURL($text, $attributes = '')
	{
		if (!empty($attributes)) {
			$attributes = ' '.trim((string) $attributes);
		}
		return preg_replace('@(?<!href="|">|src=")((?:http|https|ftp|nntp)://[^ <]+)@i', '<a href="\1"'.$attributes.'>\1</a>', $text);
	}
	
	public function autoEmail($text, $attributes = '')
	{
		if (!empty($attributes)) {
			$attributes = ' '.trim((string) $attributes);
		}
		return preg_replace(
			'/([0-9a-zA-Z]([-.\w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,9})/im',
			'<a href="mailto:\1"'.$attributes.'>\1</a>',
			$text
		);
	}
	
	/**
	 * Normalizing the brakes in a string to UNIX Brakes
	 * they are displayable on Mac, Linux and PC. All Line Brakes are
	 * converted to UNIX line brakes - \n
	 * @param string	$string
	 * @return string
	 */
	public function normalizeBrakes($string)
	{
		return preg_replace('!(\r\n|\r)!', PHP_EOL, $string);
	}
	
	
	/**
	 * Counts all words in a text and returns the words and their count
	 * as indexed array. This will cut every html code and ignores it.
	 * <code>
	 * $text = 'I\'m a text that has some words!';
	 * var_dump(String::countWords($text, 3));
	 * // ['I\'m'] = 1, ['text'] = 1, ['that'] = 1 ... 
	 * </code>
	 * // todo this does not seem to work like the docu sais!? wtf?
	 * @param string $input
	 * @param integer $minLenght Minimum length of words to be counted
	 * @return array(mixed)
	 */
	public static function countWords($input, $minLength = null)
	{
		$string = strip_tags($input);
		if ($minLength === null) {
			$regexp = '/([\p{L}\p{N}]+-?(\p{L}\p{N}+)?)/';
		} else {
			$regexp = '/([\p{L}\p{N}]{'.$minLength.',}-?(\p{L}\p{N}+)?)/';
		}
		$foundWords = array();
		$foundKeywordsNum = preg_match_all($regexp, $string, $foundWords);
		if (!isset($foundWords[0])) {
			return $foundWords;
		}
		return $foundWords;
	}
	
	/**
	 * Counts the number of paragraphs in a text, just counting line breaks
	 * is the simpler explaination
	 * @param string $string
	 * @return integer Number of paragraphs found
	 */
	public static function countParagraphs($string)
	{
		return substr_count($string, PHP_EOL);
	}
}