<?php

namespace ephFrame\HTTP;

class StatusCode
{
	const CONTINUE_ = 100;
	const SWITCHED_PROTOCOLS = 101;
	
	const OK = 200;
	const CREATED = 201;
	const ACCEPTED = 202;
	const NON_AUTHORATIVE_INFORMATION = 203;
	const NO_CONTENT = 204;
	const RESET_CONTENT = 205;
	const PARTIAL_CONTENT = 206;
	
	const MULTIPLE_CHOICES = 300;
	const MOVED_PERMANENTLY = 301;
	const FOUND = 302;
	const SEE_OTHER = 303;
	const NOT_MODIFIED = 304;
	const USE_PROXY = 305;
	const TEMPORARARY_REDIRECT = 306;
	
	const BAD_REQUEST = 400;
	const UNAUTHORIZED = 401;
	const PAYMENT_REQUIRED = 402;
	const FORBIDDDDEN = 403;
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const NOT_ACCEPTABLE = 406;
	const PROXY_AUTHENTIFICATION_REQUIRED = 407;
	const REQUEST_TIME_OUT = 408;
	const CONFLICT = 409;
	const GONE = 410;
	const LENGTH_REQUIRED = 411;
	const PRECONDITION_FAILED = 412;
	const REQUEST_ENTITY_TOO_LARGE = 413;
	const REQUEST_URI_TOO_LARGE = 414;
	const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
	const REQUESTED_RANGE_NOT_SATISFIABLE = 416;
	const EXPECTATION_FAILED = 417;
	
	const INTERNAL_SERVER_ERROR = 500;
	const NOT_IMPLEMENTED = 501;
	const BAD_GATEWAY = 502;
	const SERVICE_UNAVAILABLE = 503;
	const GATEWAY_TIMEOUT = 504;
	
	private static $messages = array(
		self::CONTINUE_ => 'Continue',
		self::SWITCHED_PROTOCOLS => 'Switching Protocols',
		self::OK => 'OK',
		self::CREATED => 'Created',
		self::ACCEPTED => 'Accepted',
		self::NON_AUTHORATIVE_INFORMATION => 'Non-Authoritative Information',
		self::NO_CONTENT => 'No Content',
		self::RESET_CONTENT => 'Reset Content',
		self::PARTIAL_CONTENT => 'Partial Content',
		self::MULTIPLE_CHOICES => 'Multiple Choices',
		self::MOVED_PERMANENTLY => 'Moved Permanently',
		self::FOUND => 'Found',
		self::SEE_OTHER => 'See Other',
		self::NOT_MODIFIED => 'Not Modified',
		self::USE_PROXY => 'Use Proxy',
		self::TEMPORARARY_REDIRECT => 'Temporary Redirect',
		self::BAD_REQUEST => 'Bad Request',
		self::UNAUTHORIZED => 'Unauthorized',
		self::PAYMENT_REQUIRED => 'Payment Required',
		self::FORBIDDDDEN => 'Forbidden',
		self::NOT_FOUND => 'Not Found',
		self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
		self::NOT_ACCEPTABLE => 'Not Acceptable',
		self::PROXY_AUTHENTIFICATION_REQUIRED => 'Proxy Authentication Required',
		self::REQUEST_TIME_OUT => 'Request Time-out',
		self::CONFLICT => 'Conflict',
		self::GONE => 'Gone',
		self::LENGTH_REQUIRED => 'Length Required',
		self::PRECONDITION_FAILED => 'Precondition Failed',
		self::REQUEST_ENTITY_TOO_LARGE => 'Request Entity Too Large',
		self::REQUEST_URI_TOO_LARGE => 'Request-URI Too Large',
		self::HTTP_UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
		self::REQUESTED_RANGE_NOT_SATISFIABLE => 'Requested range not satisfiable',
		self::EXPECTATION_FAILED => 'Expectation Failed',
		self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
		self::NOT_IMPLEMENTED => 'Not Implemented',
		self::BAD_GATEWAY => 'Bad Gateway',
		self::SERVICE_UNAVAILABLE => 'Service Unavailable',
		self::GATEWAY_TIMEOUT => 'Gateway Time-out'
	);

	public static function message($code)
	{
		if (array_key_exists($code, self::$messages)) {
			return self::$messages[$code];
		}
		return false;
	}
	
	public static function isError($code)
	{
		return is_numeric($code) && $code >= self::BAD_REQUEST;
	}
}