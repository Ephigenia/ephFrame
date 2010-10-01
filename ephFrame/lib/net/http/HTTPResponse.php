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

class_exists('HTTPHeader') or require dirname(__FILE__).'/HTTPHeader.php';

/**
 * A Raw HTTP Response
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 14.02.2008
 * @version 0.1
 * @uses HTTPHeader
 */
class HTTPResponse extends Object
{
	/**
	 * Array of header statements in the response
	 * @var HTTPHeader
	 */
	public $header;
	
	/**
	 * Turn the rendereing of the header in the response 
	 * @var boolean
	 */
	public $includeHeaders = true;
	
	/**
	 * Content of the HTTPResponse
	 * @var string
	 */
	public $body = '';
	
	/**
	 * HTTP Response constructor
	 * 
	 * accepting a raw HTTP Response as a string
	 * @param string|integer $rawDataOrStatusCode
	 * @return HTTPResponse
	 */
	public function __construct($rawDataOrStatusCode = null, HTTPHeader $header = null) 
	{
		if ($header) {
			$this->header = $header;
		} else {
			$this->header = new HTTPHeader($header);
		}
		if (is_string($rawDataOrStatusCode)) {
			$this->header = new HTTPHeader($raw);
			$this->body = $this->parse($rawDataOrStatusCode);
		} elseif (is_int($rawDataOrStatusCode)) {
			$this->statusCode = $rawDataOrStatusCode;
		}
		return $this;
	}
	
	/**
	 * Parses a (valid) HTTP Response. Extracting the content part and all headers
	 * from the response if found. Otherwise the raw data is interpreted as the
	 * hole response.
	 *
	 * @param string $raw
	 * @return string Content part of the message
	 */
	private function parse($raw)
	{
		$parts = preg_split('/\x0D\x0A\x0D\x0A/s', $raw);
		if (count($parts) > 1) {
			return $parts[count($parts)-1];
		}
		return false;
	}
	
	/**
	 * Renders the HTTP Response with all headers
	 * @param $includeHeaders include the response headers in the rendering
	 * @return string
	 */
	public function render($includeHeaders = false) 
	{
		if ($this->includeHeaders || $includeHeaders) {
			return $this->header->render().RT.LF.RT.LF.$this->body;
		} else {
			return $this->body;
		}
	}
	
	public function __toString() 
	{
		return $this->render();
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class HTTPResponseException extends BasicException 
{}