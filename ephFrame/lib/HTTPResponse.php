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
class HTTPResponse extends Object implements Renderable {
	
	/**
	 * Array of header statements in the response
	 * @var HTTPHeader
	 */
	public $header;
	
	/**
	 * Turn the rendereing of the header in the response 
	 * @var boolean
	 */
	public $enableRenderHeaders = true;
	
	/**
	 * Enable GZip Compression
	 * @todo extract this to some var called encoding and make it possible to use any class that implements Compressor Interface
	 * @var boolean
	 */
	public $enableGZipCompression = false;
	
	/**
	 * Minimum body length that is needed that the response will be compressed
	 * if {@link enableGZipCompression} is true
	 * @var integer
	 */
	public $gZipMinBodySize = 1000;
	
	/**
	 * Raw rendered Data
	 * @var string
	 */
	public $rawData = '';
	
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
	public function __construct($rawDataOrStatusCode = null) {
		$this->header = new HTTPHeader();
		if (is_string($rawDataOrStatusCode)) {
			$this->rawData = $rawDataOrStatusCode;
			// parse takes care of the body and the headers
			$this->parse($rawDataOrStatusCode);
		} elseif (is_int($rawDataOrStatusCode)) {
			$this->statusCode = $rawDataOrStatusCode;
		}
		return $this;
	}
	
	public function hasHeader() {
		return count($this->header) > 0;
	}
	
	/**
	 * Parses a (valid) HTTP Response. Extracting the content part and all headers
	 * from the response if found. Otherwise the raw data is interpreted as the
	 * hole response.
	 *
	 * @param string $raw
	 * @return string Content part of the message
	 */
	private function parse($raw) {
		// get the content part from the response if found, otherwise
		// the hole raw data is the content
		if (!preg_match('/[\r\n]{3,}(.*)/s', $raw, $found)) {
			$this->body = $raw;
			return $raw;
		}
		$this->body = trim($found[1]);
		// okay, we know that we got some header infos here, parse 'em!
		$this->header->parse($raw);
		return $this->body;
	}
	
	/**
	 * Renders the HTTP Response with all headers
	 * @return string
	 */
	public function render() {
		if (!$this->beforeRender()) return false;
		// add http status code to header
		$rendered = '';
		if ($this->enableRenderHeaders) {
			$rendered = $this->header->render();
			$rendered .= RT.LF.RT.LF;
		}
		$rendered .= $this->body;
		return $this->afterRender($rendered);
	}
	
	/**
	 * Callback before HTTP Response is rendered, returns false if the
	 * HTTP Response should not be rendered. Place your code here.
	 * 
	 * @return boolean
	 */
	public function beforeRender() {
		// enable GZip Compression
		loadClass('ephFrame.lib.component.GZipCompressor');
		if ($this->enableGZipCompression) {
			$GZipCompressor = new GZipCompressor();
			if ($GZipCompressor->gZipAvailable && strlen($this->body) > $this->gZipMinBodySize) {
				$this->header->add('Content-Encoding', 'gzip');
				$this->body = $GZipCompressor->compress($this->body);
			}
		}
		return true;
	}
	
	/**
	 * After Render Callback, hook in your custom code that manipulates the
	 * rendered content.
	 * @param string $rendered
	 * @return string
	 */
	public function afterRender($rendered) {
		return $rendered;
	}
	
	/**
	 * Returns the rendered HTTP Response
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class HTTPResponseException extends BasicException {}