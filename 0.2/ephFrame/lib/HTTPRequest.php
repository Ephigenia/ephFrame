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

ephFrame::loadClass('ephFrame.lib.helper.ArrayHelper');
ephFrame::loadClass('ephFrame.lib.HTTPResponse');
ephFrame::loadClass('ephFrame.lib.HTTPHeader');

/**
 *	Http Request Class
 * 	
 * 	A Class that can handle the current HTTP Request (with __construct(true))
 * 	or send HTTP Requests to other hosts and Return a {@link HTTPResponse}.
 * 
 * 	How to read from the actual HTTP Request
 * 	<code>
 * 	$request = new HTTPRequest(true);
 * 	if ($request->get('id')) {
 * 		// do something with the catched id
 * 	}
 * 	</code>
 * 
 * 	Use the HTTP Request class for retreiving information from google maps
 * 	<code>
 * 	$request = new HTTPRequest(array('apicode' => 'asdlkj', 'output' => 'xml'));
 * 	$request->set('q', 'Kopernikusstr. 8, Berlin');
 * 	$response = $request->send('maps.google.com/maps/geo');
 * 	echo $response->dump();
 * 	</code>
 * 
 * 	Use HTTP Request class for reading a website
 * 	<code>
 * 	$request = new HTTPRequest();
 * 	$response = $request->send('http://code.ephigenia.de');
 * 	// map it to __toString();
 * 	echo $response;
 * 	</code>
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 06.05.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@uses ArrayHelper
 * 	@uses HTTPResponse
 * 	@version 0.1
 */
class HTTPRequest extends Component {

	const METHOD_GET = 'GET';
	const METHOD_POST = 'POST';
	
	/**
	 * 	Concrete Request Method
	 * 	@var string
	 */
	public $method;
	
	public $uri;
	public $host;
	public $port = 80;
	
	/**
	 * 	@var HTTPHeader
	 */
	private $header;
	
	/**
	 * 	Timeout in seconds for outgoing request
	 * 	@var integer
	 */
	public $timeout = 5;
	
	public $data = array();
	
	/**
	 *	Creates a HTTPRequest that can be send away
	 * 	@param boolean|array(string) $autofillOrData
	 * 	@return HTTPRequest
	 */
	public function __construct($autofillOrData = false) {
		$this->header = new HTTPHeader();
		if (is_array($autofillOrData)) {
			$this->data = $autofillOrData;
		} elseif ($autofillOrData == true) {
			$this->autofill();
		}
		return $this;
	}
	
	public function isPost() {
		return $this->method == self::METHOD_POST;
	}
	
	public function isGet() {
		return $this->method == self::METHOD_GET;
	}
	
	private function autofill() {
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->uri = $_SERVER['REQUEST_URI'];
		$this->host = $_SERVER['HTTP_HOST'];
		if ($this->method == self::METHOD_GET) {
			$this->data = &$_GET;
			$this->data = array_merge($_POST, $this->data);
		} else {
			$this->data = &$_POST;
			$this->data = array_merge($_GET, $this->data);
		}
		// strip slashes from all values if magic quotes are on
		if (ini_get('magic_quotes_gpc') == 1) {
			$this->data = ArrayHelper::stripslashes($this->data);
		}
	}
	
	/**
	 * 	Read from the HTTPRequest using the ArrayHelper::extract method to
	 * 	support reading from arrays
	 * 	<code>
	 * 	$a = $request->read('projectname/user/id');
	 * 	</code>
	 * 	@param string $name
	 * 	@return mixed
	 */
	public function get($name) {
		return ArrayHelper::extract($this->data, $name);
	}
	
	/**
	 * 	Send the request with the current method, data and uri to 
	 * 	the current host, or you specify a custom url and pass it to the method
	 * 	@param string $url
	 * 	@return HTTPResponse
	 */
	public function send($url = null) {
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
	 * 	Basically builds a valid HTTP Request to send of to a Web Server
	 *
	 * 	@param string $host ip or host that should be harmed with the request
	 * 	@param strign $uri requested uri on that host
	 * 	@param unknown_type $query
	 * 	@return unknown
	 */
	private function buildRequest($host, $uri = '/', $query = '') {
		if (empty($uri)) {
			$uri = '/';
		}
		$requestRaw = 'GET '.$uri.$query.' HTTP/1.1'.RT.LF;
		$defaultRequestArr = array(
			'Host' => $host,
			'Connection' => 'Close'
		);
		// merge with the header from the request initially set
		$reqArr = array_merge($defaultRequestArr, $this->header);
		// render request
		foreach ($reqArr as $key => $value) {
			$requestRaw .= $key.': '.$value.RT.LF;
		}
		return $requestRaw.LF.RT.LF;
	}
	
	private function FSockOpenRead($host, $port, $timeout) {
		$fp = fsockopen($host, $port, $errno, $errostr);
		if (!$fp) {
			throw new HTTPRequestFSockError($errno.': '.$errorstr);
		}
		return $fp;
	}
	
	/**
	 * 	Build a request string from the data for this request or use the passed
	 * 	array to generate the request string.
	 * 
	 *  The Request string is the part of the url that is added to the requested
	 * 	URI - Suche as for example:
	 * 	<code>
	 * 	?id=245&mode=list
	 * 	</code>
	 * 
	 * 	This method can also handle multiple dimension arrays, rendering them
	 * 	as php would do in forms:
	 * 	<strong>not finished</strong>
	 * 	<code>
	 * 	?id=234&mode=edit&categories[]=1&categories[]=34
	 * 	</code>
	 * 
	 * 	All Strings will be urlencoded
	 *  
	 *	@param array $data
	 *	@return string
	 */
	public function buildRequestQuery($data = null) {
		if (!$data) {
			$data = $this->data;
		}
		if (empty($data)) {
			return '';
		}
		if (!is_array($data)) {
			return $data;
		}
		$queryString = '?';
		foreach($data as $key => $value) {
			if (is_array($value)) {
				// missing stuff
			} else {
				$queryString .= $key.'='.urlencode($value).'&';
			}
		}
		return $queryString;
	}
	
	/**
	 * 	Dumps the data from the request and returns it
	 * 	@return string
	 */
	public function dump() {
		$rendered = '';
		foreach ($this->data as $key => $var) {
			$rendered .= htmlentities($key).': '.htmlentities(var_export($var, true)).LF;
		}
		return $rendered;
	}

}

/**
 *	@package ephFrame
 *	@subpackage ephFrame.lib.exception
 */
class HTTPRequestException extends BasicException {}

/**
 * 	@package ephFrame
 *	@subpackage ephFrame.lib.exception
 */
class HTTPRequestFSockError extends HTTPRequestException {
}

?>