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

/**
 * HTTP Status Codes
 * 
 * Stores all known http 1.1 status codes I was able to find
 * 
 * Retreive HTTP Status Message from HTTP Status code
 * <code>
 * echo HTTPStatusCode::message(404);
 * </code>
 *
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 16.12.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @static
 */
class HTTPStatusCode extends Object {
	
	/**
	 * Collected StatusCode -> Message String collection
	 * @var array(string)
	 */
	public static $statusCodes = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Time-out',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Large',
		415 => 'Unsupported Media Type',
		416 => 'Requested range not satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Time-out'
	);
	
	/**
	 * Returns the status message if found in the status code array. If the
	 * status code is not found false is returned
	 * @param string|integer $code
	 * @return string|boolean
	 */
	public static function message($code) {
		$code = (int) $code;
		if (isset(self::$statusCodes[$code])) {
			return self::$statusCodes[$code];
		}
		return false;
	}
	
	/**
	 * Returns a valid header message for the http status code passed
	 * 
	 * Create a 404 response header status message
	 * <code>
	 * // should echo 'HTTP/1.0 404 Not Found
	 * echo HttpStatusCode::header(404, '1.0');
	 * </code>
	 * 
	 * @param string|integer $code
	 * @param string|integer|float $httpVersion
	 * @return string
	 */
	public static function header($code, $httpVersion = '1.x') {
		if (!$msg = self::message($code)) {
			return false;
		}
		return 'HTTP/'.$httpVersion.' '.$code.' '.$msg;
	}
	
	/**
	 * Send a header message with the passed status code to the client
	 * @param string|integer $code
	 * @return boolean
	 */
	public static function send($code) {
		if (!$headerMessage = self::header($code)) {
			return false;
		}
		header($headerMessage, true, $code);
		return true;
	}
	
}