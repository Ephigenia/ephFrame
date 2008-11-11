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

/**
 * 	Simple CURL integration
 * 	
 * 	
 * 	
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 11.11.2008
 */
class CURL extends Object {
	
	private $handle;
	
	public $method = 'POST';
	
	public $data = array();
	
	public $cookie = array();
	
	public $port = 80;
	
	public $referer;
	
	public $url;
	
	public $userAgent = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_5; de-de) AppleWebKit/525.18 (KHTML, like Gecko) Version/3.1.2 Safari/525.20.1';
	
	public $auth = array();
	
	public $headers = array();
	
	public function __construct($url) {
		$this->handle = curl_init($url);
		$this->url = $url;
		return $this;
	}
	
	private function implodeData($data) {
		$data = '';
		foreach($this->data as $key => $value) {
			$data .= sprintf('%s=%s&', $key, urlencode($value));
		}
		return substr($data, 0, -1);
	}
	
	public function exec($return = true, $headers = false) {
		if (!empty($this->data)) {
			if (strtolower($this->method) === 'post') {
				curl_setopt($this->handle, CURLOPT_POST, true);
				curl_setopt($this->handle, CURLOPT_POSTFIELDS, http_build_query($this->data));
			} else {
				$this->url .= '?'.http_build_query($this->data);
				curl_setopt($this->handle, CURLOPT_HTTPGET, true);
			}
		}
		if (!empty($this->cookie)) {
			curl_setopt($this->handle, CURLOPT_COOKIE, http_build_cookie($this->cookie));
		}
		if ($this->port !== 80) {
			curl_setopt($this->handle, CURLOPT_PORT, (int) $this->port);
		}
		if (!empty($this->referer)) {
			curl_setopt($this->handle, CURLOPT_REFERER, $this->referer);
		}
		if (!empty($this->userAgent)) {
			curl_setopt($this->handle, CURLOPT_USERAGENT, $this->userAgent);
		}
		if (!empty($this->auth)) {
			curl_setopt($this->handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($this->handle, CURLOPT_USERPWD, implode(':', $this->auth));
		}
		if (count($this->headers) > 0) {
			curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->headers);
		}
		if ($return) {
			curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, $return);
		}
		if ($headers) {
			curl_setopt($this->handle, CURLOPT_HEADER, true);
		}
		curl_setopt($this->handle, CURLOPT_URL, $this->url);
		curl_setopt($this->handle, CURLOPT_COOKIESESSION, true);
		return curl_exec($this->handle);
	}
	
	public function __destroy() {
		curl_close($this->handle);
	}
	
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class CURLException extends BasicException {}
?>