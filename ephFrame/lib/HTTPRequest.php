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

class_exists('ArrayHelper') or require dirname(__FILE__).'/helper/ArrayHelper.php';
class_exists('HTTPResponse') or require dirname(__FILE__).'/HTTPResponse.php';
class_exists('HTTPHeader') or require dirname(__FILE__).'/HTTPHeader.php';
class_exists('Charset') or require dirname(__FILE__).'/helper/Charset.php';

/**
 * Different Constants for HTTP Request Methods
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2010-03-12
 */
class HTTPRequestMethod
{
	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const DELETE = 'DELETE';
}

/**
 * Http Request Class
 * 
 * A Class that can handle the current HTTP Request (with __construct(true))
 * or send HTTP Requests to other hosts and Return a {@link HTTPResponse}.
 * 
 * How to read from the actual HTTP Request
 * <code>
 * $request = new HTTPRequest(true);
 * if ($request->get('id')) {
 * 	// do something with the catched id
 * }
 * </code>
 * 
 * Use the HTTP Request class for retreiving information from google maps
 * <code>
 * $request = new HTTPRequest(array('apicode' => 'asdlkj', 'output' => 'xml'));
 * $request->set('q', 'Kopernikusstr. 8, Berlin');
 * $response = $request->send('maps.google.com/maps/geo');
 * echo $response->dump();
 * </code>
 * 
 * Use HTTP Request class for reading a website
 * <code>
 * $request = new HTTPRequest();
 * $response = $request->send('http://code.marceleichner.de');
 * // map it to __toString();
 * echo $response;
 * </code>
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 06.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @uses ArrayHelper
 * @uses HTTPResponse
 * @version 0.1
 */
class HTTPRequest extends Component 
{
	/**
	 * Concrete Request Method
	 * @var string
	 */
	public $method = HTTPRequestMethod::GET;
	
	public $uri;
	public $hostname;
	public $host;
	public $port = 80;
	
	public $referer = false;
	
	/**
	 * @var HTTPHeader
	 */
	public $header;
	
	/**
	 * Timeout in seconds for outgoing request
	 * @var integer
	 */
	public $timeout = 5;
	
	/**
	 * Stores post and get variables
	 * @var array(string)
	 */
	public $data = array();
	
	/**
	 * Creates a HTTPRequest that can be send away
	 * @param boolean|array(string) $autofillOrData
	 * @return HTTPRequest
	 */
	public function __construct($autofillOrData = false) 
	{
		$this->header = new HTTPHeader();
		if (is_array($autofillOrData)) {
			$this->data = $autofillOrData;
		} elseif ($autofillOrData == true) {
			$this->autofill();
		}
		return $this;
	}
	
	public function isMethod($method) {
		return $this->method == $method;
	}
	
	public function isPost() 
	{
		return $this->isMethod(HTTPRequestMethod::POST);
	}
	
	public function isGet() 
	{
		return $this->isMethod(HTTPRequestMethod::GET);
	}
	
	public function isPut()
	{
		return $this->isMethod(HTTPRequestMethod::PUT);
	}
	
	public function isDelete()
	{
		return $this->isMethod(HTTPRequestMethod::DELETE);
	}
	
	/**
	 * Returns the name of the requesting host
	 * @return string
	 */
	public function hostname() 
	{
		if (empty($this->hostname)) {
			$this->hostname = gethostbyaddr($this->host);
		}
		return $this->hostname;
	}
	
	/**
	 * Returns the refererrer submitted by the client if found
	 * Set $local to true to only use internal urls (external urls will be
	 * ignored)
	 * @param string $default default referer returned on empty referers
	 * @param boolean $local Use only local referers
	 * @return string
	 */
	public function referer($default = false, $local = true) 
	{
		if ($this->referer) {
			// return refere only if in local domain
			if (!$local) {
				return $this->referer;
			} elseif (preg_match('/^(http:\/\/|)'.str_replace('.', '\.', $_SERVER['HTTP_HOST']).'/', $this->referer)) {
				return $this->referer;
			}
		}
		return $default;
	}
	
	private function autofill()
	{
		if (isset($_SERVER)) {
			$this->method = $_SERVER['REQUEST_METHOD'];
			$this->uri = $_SERVER['REQUEST_URI'];
			$this->host = $_SERVER['REMOTE_ADDR'];
		}
		if ($this->host == '::1') {
			$this->host = '127.0.0.1';
		}
		// set referer if ther's any
		if (isset($_SERVER['HTTP_REFERER'])) {
			$this->referer = $_SERVER['HTTP_REFERER'];
		}
		if ($this->method == HTTPRequestMethod::GET) {
			$this->data = &$_GET;
			$this->data = array_merge($_POST, $this->data);
		} else {
			$this->data = &$_POST;
			$this->data = array_merge($_GET, $this->data);
		}
		// fix wrong decoded utf8 entities
		$this->data = ArrayHelper::map(array('Charset', 'toUtf8'), $this->data);
	
		// strip slashes from all values if magic quotes are on
		if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
			if (!defined('ephFrameHTTPRequestAddSlashesOk')) {
				$this->data = ArrayHelper::map('stripslashes', $this->data);
				define('ephFrameHTTPRequestAddSlashesOk', true);
			}
		}
		// collect received header data
		// @todo include this to https ? or what? that is missing?
		if (isset($_SERVER) && is_array($_SERVER)) {
			foreach($_SERVER as $key => $value) {
				if (substr($key, 0, 5) == 'HTTP_') {
					$this->header->set(strtolower(substr($key,5)), $value);
				}
			}
		}
		return true;
	}
	
	/**
	 * Tests if a sended request is ajax (only works with jquery)
	 * @return boolean
	 */
	public function isAjax() 
	{
		return ($this->header->get('x_requested_with') == 'XMLHttpRequest');
	}
	
	/**
	 * Read from the HTTPRequest using the ArrayHelper::extract method to
	 * support reading from arrays
	 * <code>
	 * $a = $request->read('projectname/user/id');
	 * </code>
	 * @param string $name
	 * @return mixed
	 */
	public function get($name) 
	{
		return ArrayHelper::extract($this->data, $name);
	}
	
	/**
	 * Send the request with the current method, data and uri to 
	 * the current host, or you specify a custom url and pass it to the method
	 * @param string $url
	 * @return HTTPResponse
	 */
	public function send($url = null) 
	{
		// use passed url
		if ($url) {
			$host = parse_url($url, PHP_URL_HOST);
			$uri = parse_url($url, PHP_URL_PATH);
			$query = parse_url($url, PHP_URL_QUERY);
			$port = parse_url($url, PHP_URL_PORT);
		// use this request as data provider for the request
		} else {
			$host = $this->host;
		}
		if (!$port) {
			$port = $this->port;
		}
		if (empty($query)) {
			$query = $this->buildRequestQuery();
		}
		// from here we would need a socket class that handles this shit
		$requestRaw = $this->buildRequest($host, $uri, $query);
		$responseRaw = '';
		$socket = $this->FSockOpenRead($host, $port, $this->timeout);
		fwrite($socket, $requestRaw);
		while(!feof($socket)) {
			$responseRaw .= fgets($socket, 512);
		}
		fclose($socket);
		$response = new HTTPResponse($responseRaw);
		return $response;
	}
	
	/**
	 * Basically builds a valid HTTP Request to send of to a Web Server
	 *
	 * @param string $host ip or host that should be harmed with the request
	 * @param strign $uri requested uri on that host
	 * @param unknown_type $query
	 * @return unknown
	 */
	private function buildRequest($host, $uri = '/', $query = '')
	{
		if (empty($uri)) {
			$uri = '/';
		}
		// merge with the header from the request initially set
		$this->header->merge(array(
			'Host' => $host,
			'Connection' => 'Close'
		), false);
		$requestRaw =
			$this->method.' '.$uri.$query.' HTTP/1.1'.RT.LF.
			$this->header->render().LF.RT.LF;
		return $requestRaw;
	}
	
	private function FSockOpenRead($host, $port, $timeout)
	{
		$fp = fsockopen($host, $port, $errno, $errostr);
		if (!$fp) {
			throw new HTTPRequestFSockError($errno.': '.$errorstr);
		}
		return $fp;
	}
	
	/**
	 * Build a request string from the data for this request or use the passed
	 * array to generate the request string.
	 * 
	 * The Request string is the part of the url that is added to the requested
	 * URI - Suche as for example:
	 * <code>
	 * ?id=245&mode=list
	 * </code>
	 * 
	 * This method can also handle multiple dimension arrays, rendering them
	 * as php would do in forms:
	 * <strong>not finished</strong>
	 * <code>
	 * ?id=234&mode=edit&categories[]=1&categories[]=34
	 * </code>
	 * 
	 * All Strings will be urlencoded
	 * 
	 * @param array $data
	 * @return string
	 */
	public function buildRequestQuery($data = null) 
	{
		if (!$data) {
			$data = $this->data;
		}
		if (empty($data)) {
			return '';
		}
		if (!is_array($data)) {
			return $data;
		}
		return '?'.http_build_query($data);
	}
	
	/**
	 * Dumps the data from the request and returns it
	 * @return string
	 */
	public function dump() 
	{
		$rendered = '';
		foreach ($this->data as $key => $var) {
			$rendered .= htmlentities($key).': '.htmlentities(var_export($var, true)).LF;
		}
		return $rendered;
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class HTTPRequestException extends BasicException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class HTTPRequestFSockError extends HTTPRequestException 
{}