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

// load renderable renderable interface
interface_exists('Renderable') or require dirname(__FILE__).'/Renderable.php';

/**
 * URL Class
 * 
 * U can use this class for analyzing, creating, manipulating every kind
 * of URL-Like Strings. Also the {@link DBDSN} used in this Framework is a
 * child of this class.
 *
 * <code>
 * // parsing urls
 * $url = new URL("http://www.ephigenia.de/hello/#you");
 * echo $url->host; 	// gets 'www.ephigenia.de'
 * echo $url->anchor; 	// gets 'you'
 *
 * // creating new urls
 * $url = new Url();
 * $url->host("www.ephigenia.de");
 * $url->scheme("http");
 * echo $url; 			// get 'http://www.ephigenia.de'
 * echo $url->urlWithoutScheme(); // get www.ephgienia.de
 * </code>
 * 
 * Some other classes may use this one, the best example is the {@link DBDSN}
 * class which is used to establish a connection to Database Servers.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 02.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @version 0.1
 */
class URL extends Object implements Renderable 
{
	/**
	 * @var string
	 */
	public $url;
	
	/**
	 * Stores the parsed parts of the url
	 * @var array(string)
	 */
	protected $parsedUrl = array(
		'scheme' => null,
		'host' => null,
		'port' => null,
		'user' => null,
		'pass' => null,
		'path' => null,
		'query' => null,
		'fragment' => null,
		'subdomain' => null
	);
	
	/**
	 * Url Constructor
	 * @param string	$url
	 */
    public function __construct($url = null) 
	{
	    return $this->_url($url);
    }
    
	/**
     * Sets or returns the current url
     * if you set a new url the url is parsed and stored into {@link parsedUrl}
     * @param string $url
     * @return string
     */
	private function _url($url = null) {
		if ($url == null) return $this;
		if (func_num_args() == 0) return $this->url;
		if (!is_string($url)) throw new StringExpectedException();
		$this->url = trim($url);
		$this->parse();
		return $this;
	}
	
	/**
     * Parses the url and stores the parsed parts in {@link parsedUrl}
     * @param string $url
     * @throws StringExpectedException
     * @return boolean 
     */
    private function parse() {
    	$this->parsedUrl = array_merge($this->parsedUrl, parse_url($this->url));
    	// rename fragment to anchor
		if (isset($parsedUrl['fragment'])) {
			$this->parsedUrl['anchor'] = $this->parsedUrl['fragment']; 
		}
    	return true;
    }
    
    /**
     * get or set method, internally used
     * @param 
     */
	public function __call($method, $params) 
	{
		// overwriting this du to php 5.1.6 thinks that url function is the constructor
		if ($method == 'url') {
			return $this->_url($params[0]);
		}
		if (array_key_exists($method, $this->parsedUrl)) {
			if (count($params) == 0) {
				return $this->parsedUrl[$method];
			} else {
				return call_user_func_array(array($this, $method), $params);
			}
			$this->buildUrl();
		} elseif (method_exists($this, $method)) {
			return call_user_func_array(array($this, $method), $params);
			$this->buildUrl();
		}
		trigger_error('call to unexisting function '.$method, E_USER_ERROR);
	}
	
	/**
	 * Returns the builded url
	 * @return string
	 */
	public function __toString() 
	{
		return $this->render();
	}
	
	/**
	 * Callback called before {@link render} takes action
	 * you can overwrite this method in subclasses to avoid rendering
	 * @return boolean
	 */
	public function beforeRender() 
	{
		return true;
	}
	
	/**
	 * @param string $url
	 * @return string
	 */
	public function afterRender($url) 
	{
		return $url;
	}
	
	/**
	 * Sets or returns the path in the url
	 * @param string $path
	 * @param boolean $asArray
	 * @return string
	 */
	public function path($path = null, $asArray = false) 
	{
		$argCount = func_num_args();
		if ($argCount == 0) {
			return $this->parsedUrl['path'];
		} elseif ($argCount == 1) {
			return $this->parsedUrl['path'];
		} elseif (is_array($path)) {
			$this->parsedUrl['path'] = implode('/', $path);
		} else {
			if ($path === null) {
				if ($asArray) {
					$arr = explode('/', $this->parsedUrl['path']);
					if (empty($arr[count($arr)])) {
						$arr = array_slice($arr, 0, count($arr) - 1);
					}
					return $arr;
				} else {
					return $this->parsedUrl['path'];
				}
			} else {
				$this->parsedUrl['path'] = $path;
			}
		}
		return $this;
	}
	
	/**
	 * Builds a URL with the parsed date in {@link parsedUrl}
	 * @return builded url 
	 */
    public function render() 
	{
    	// drop if beforeRender fucks it off
    	if (!$this->beforeRender()) return null;
    	$url = '';
	    $url .= $this->parsedUrl['scheme'].'://';
	    // if username or password set
	    if (!empty($this->parsedUrl['user']) && !empty($this->parsedUrl['pass'])) {
	    	
	    }
	    $template = "%scheme%%user:pass%%host%%port%%path%%query%%fragment%";
	    $replaceArray = array(
	    	"%scheme%" 		=> null,
			"%user:pass%"	=> null,
	    	"%host%" 		=> $this->host(),
	    	"%port%"		=> null,
	    	"%path%"		=> null,
	    	"%query%"		=> null,
	    	"%fragment%"	=> null
	    );
	    if ($this->user() != null && $this->pass() != null) {
	    	$replaceArray["%user:pass%"] = $this->user().":".$this->pass()."@";
	    }
	    // use scheme?
	    if ($this->scheme()) {
		   	try {
		    	$replaceArray["%scheme%"] = $this->scheme()."://";
		    } catch (UrlPortNotFoundException $e) {
		    	$replaceArray["%port%"] = $this->port();
		    }
	    }
	    // use path ?
	    if ($this->path()) {
	    	$replaceArray["%path%"] = "/".$this->path();
	   	}
	   	// use query ?
	    if ($this->query()) {
	    	$replaceArray["%query%"] = ($this->query() != null) ? "?".$this->query() : null;
	    }
	    // use fragment - which is the hash (#)!
	    if ($this->fragment()) {
	    	$replaceArray["%fragment%"] = ($this->fragment() != null) ? "#".$this->fragment() : null;
	    }
	    $rendered = strtr($template, $replaceArray);;
	    return $this->afterRender($rendered);
    }	
}

/**
 * Basic Url Exception
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class UrlException extends ComponentException 
{}