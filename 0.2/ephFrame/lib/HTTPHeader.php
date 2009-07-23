<?php

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

class_exists('Hash') or require dirname(__FILE__).'/Hash.php';
class_exists('HTTPStatusCode') or require dirname(__FILE__).'/HTTPStatusCode.php';

/**
 * HTTP header
 * 
 * Create/Parse/Edit HTTP Header Data
 * 
 * Create a header and add Content-Encoding statement
 * <code>
 * $header = new HTTPHeader();
 * $header->set('Content-Encoding', 'gzip');
 * </code>
 * 
 * @author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * @since 22.05.2008
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @uses HTTPStatusCode
 */
class HTTPHeader extends Hash {
	
	/**
	 * HTTP Response Code
	 * @var integer
	 */
	public $statusCode = 200;
	
	/**
	 * Delimeter that splits the header messages
	 * @var string
	 */
	protected $delimeter = LF;
	
	/**
	 * Stores the regular expression that is used when parsing raw header
	 * data in {@link parse}
	 * @var string
	 */
	protected $headerRegExp = '/^([-_\w]+):\s*(.*)$/m';
	
	/**
	 * HTTPHeader Constructor
	 * 
	 * parses a string passed to the constructor or creates a init data set
	 * from the array you pass.
	 *
	 * @param string|array(string) $initialHeaderDataOrString
	 * @return HTTPHeader
	 */
	public function __construct($initialHeaderDataOrString = null) {
		if (is_array($initialHeaderDataOrString)) {
			parent::__construct($initialHeaderDataOrString);
		} elseif (is_string($initialHeaderDataOrString)) {
			$this->parse($initialHeaderDataOrString);
		}
		return $this;
	}
	
	/**
	 * Overwrite {@link Hash}s add method for enabling cool value parsind.
	 * So you can set the expire date by passing a timestamp:
	 * <code>
	 * $HTTPHeader->add('Expires', time() + 3600 * 24);
	 * </code>
	 *
	 * @param string $key
	 * @param string|integer $value
	 */
	public function add($key, $value = null) {
		if (in_array(strtolower($key), array('expires', 'last-modified')) && is_numeric($value)) {
			$value = gmdate('D, d M Y H:i:s', $value).' GMT';
		}
		return parent::add($key, $value);
	}
	
	/**
	 * Parses a raw header string and returns it.
	 * 
	 * Will try to parse the raw header data into an array and returns the
	 * array as key=>value pairs. Returns false if no string was passed.
	 * If no header data is found it will return an empty array.
	 * All key and value values are trimmed of whitespace.
	 * 
	 * @param string $rawHeader
	 * @return array|boolean
	 */
	public function parse($rawHeader) {
		if (!is_string($rawHeader)) return false;
		// extract status and response code
		if (preg_match('@HTTP/1.\d\s(\d{1,3})\s([^\n]+)@', $rawHeader, $found)) {
			$this->statusCode = (int) $found[1];
		}
		// parse header parts of raw message if there are any:
		$parsedHeader = array();
		if (preg_match_all($this->headerRegExp, $rawHeader, $foundHeaders, PREG_SET_ORDER)) {
			foreach($foundHeaders as $index => $headerData) {
				$headerData[2] = trim($headerData[2]);
				// strip quotes
				preg_replace('/^["\']|["\']$/', '', $headerData[2]);
				$parsed[$headerData[1]] = trim($headerData[2]);
			}
		}
		return $parsedHeader;
	}
	
	/**
	 * Send all $headerData or data from this object directly using header()
	 *
	 * @param array(string) $headerData
	 * @return boolean always true
	 */
	public function send($headerData = null) {
		if (func_num_args() == 0) {
			$headerDatas = $this;
		}
		foreach($this as $key => $value) {
			$rendered = $this->renderKey($key, $value);
			if (!empty($rendered)) {
				header($rendered, null, $this->statusCode);
			}
		}
		return true;
	}
	
	/**
	 * Renders one $key => $pair item of a header.
	 * 
	 * Renders a key, pair element of a header. If you pass out the second
	 * element it will try to search the key in this objects data and try to
	 * render this. If no key is found or value and key are empty false is
	 * returned.
	 *
	 * @param string $key
	 * @param string|mixed $value
	 */
	public function renderKey($key, $value = null) {
		switch($key) {
			case 'ETag':
				$value = '"'.$value.'"';
				break;
		}
		if (in_array($key, array('GET', 'POST'))) {
			$rendered = $key.' '.$value;
		} else {
			$rendered = $key.': '.$value;
		}
		return $rendered; 
	}
	
	public function render($headerData = null) {
		// if this class is used in direct use, use the internal data
		if ($headerData === null) {
			$headerData = $this->data;
		}
		// send headers to beforeRender callback first to get them manipulated
		if (!$this->beforeRender($headerData)) {
			return null;
		}
		// do nothing and return false if no header data at all
		if (!is_array($headerData)) {
			return false;
		}
		// send HTTP Header if set
		if ($this->statusCode > 0) {
			$rendered = array(HTTPStatusCode::header($this->statusCode));			
		}
		// render the header, finally
		foreach ($headerData as $key => $value) {
			$rendered[] = $this->renderKey($key, $value);
		}
		return $this->afterRender(implode($this->delimeter, $rendered));
	}
	
}