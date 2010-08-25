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

/**
 * Search Query Parser
 * 
 * This component can parse search query keywords such as "Marcel+Eichner".
 * Support for quoting is implemented. Support for boolean operators is still
 * missing.
 * 
 * This is partly tested in {@link TestSearchQueryParser}
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @version 1.0
 * @author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * @since 20.05.2008
 */
class SearchQueryParser extends AppComponent 
{
	/**
	 * @var string
	 */
	public $query;
	
	/**
	 * Contains all values from the string, no matter if they are
	 * substractive or not
	 * @var array(string)
	 */
	public $terms = array();
	
	/**
	 * Enable parsing with an eye on the +/- operators, if turned off, all
	 * search terms, also these with -searchterm are added to {@link terms}
	 * and {@link matchterms}
	 * @var boolean
	 */
	public $useOperators = true;
	
	/**
	 * Enables parsing with quoting, query strings like 'what if "evil betty"'
	 * will be parsed like 'what if evil betty' - without the quotes
	 * @var boolean
	 */
	public $useQuotes = true;
	
	/**
	 * @var array(string)
	 */
	public $matchTerms = array();
	
	/**
	 * @var array(string)
	 */
	public $noMatchTerms = array();
	
	/**
	 * Pass the query string this method
	 * @param string $queryString
	 */
	public function __construct($queryString) 
	{
		$this->query = $this->cleanUp($queryString);
		$this->terms = $this->parse($this->query);
		return $this;
	}
	
	private function parse($queryString)
	{
		$words = array();
		if ($this->useQuotes) {
			$quoting = '["\'‘’“”]';
			$regexp = '/(-|\+)?('.$quoting.'[^\p{C}]+'.$quoting.'|[^\p{C}\s]+)/iu';
		} else {
			$regexp = '/(-|\+)?([^\p{C}\s]+)/iu';
		}
		if (!preg_match_all($regexp, $queryString, $found, PREG_SET_ORDER)) {
			return $words;
		}
		foreach($found as $match) {
			$term = trim($match[2]);
			// strip the quotes (can be obmitted with better regexp above
			if ($this->useQuotes) {
				$term = preg_replace('/'.$quoting.'+/', '', $term);
			}
			// add every match to the terms, no matter of operators
			$words[] = $term;
			// search term operator, can be + for addition or - for substraction
			if ($this->useOperators) {
				$operator = $match[1];
				switch($operator) {
					default:
						$this->matchTerms[] = $term;
						break;
					case '-':
						$this->noMatchTerms[] = $term;
						break;
				}
			}
		}
		return $words;
	}
	
	/**
	 * Converts the a search query string with url-encoded chars to
	 * a string without encodings
	 * @param string $queryString
	 * @return string
	 */
	private function cleanUp($queryString) {
		return urldecode($queryString);
	}	
}