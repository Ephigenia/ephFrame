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
 * 	@package ephFrame
 * 	@subpackage ephFrame
 */

/**
 * 	This File stores some basic functions that are accessible without addressing
 * 	a class. Some of the functions don't even use a class.
 * 
 * 	Some like {@link loadClass} or {@link loadInterface} are just shortcuts
 * 	to the methods of {@link ephFrame}
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 16.09.2007
 * 	@package ephFrame
 */

// test for loaded ephFrame
if (!class_exists('ephFrame')) {
	die ('ephFrame is not initiated. Core functions are only available'.
		'when you instanciate ephFrame');
}

/**
 * 	Function alias for {@link ephFrame}s loadInterface Method
 * 	@param string $interfacePath
 * 	@return boolean
 */
function loadClass($classPath) {
	return ephFrame::loadClass($classPath);
}

/**
 *	Load a component
 * 	@param string $componentName
 * 	@return boolean
 */
function loadComponent($componentName) {
	return ephFrame::loadComponent($componentName);
}

/**
 *	Load a helper
 * 	@param string $componentName
 * 	@return boolean
 */
function loadHelper($helperName) {
	return ephFrame::loadHelper($helperName);
}

/**
 * 	Function alias for {@link ephFrame}s loadInterface Method
 * 	@param string $interfacePath
 * 	@return boolean
 */
function loadInterface($interfacePath) {
	return ephFrame::loadInterface($interfacePath);
}

/**
 * 	Coalesce works like the method from SQL, returning the first 
 * 	argument that is not null
 * 	@return mixed
 */
if (!function_exists('coalesce')) {
	function coalesce() {
		foreach(func_get_args() as $arg) {
			if ($arg !== null) return $arg;
		}
		return null;
	}
}

/**
 *	Like {@link coalesce} but checking for empty arguments
 * 	@return mixed
 */
if (!function_exists('coalesceEmpty')) {
	function coalesceEmpty() {
		foreach(func_get_args() as $arg) {
			if (!empty($arg)) return $arg;
		}
		return null;
	}
}

/**
 *	This is a shortcut for the {@link Log} class to log messages into files
 * 	@param integer $level
 * 	@param string $message
 */
function logg($level, $message) {
	if (!class_exists('Log')) {
		loadClass('ephFrame.lib.component.Log');
	}
	Log::write($level, $message);
}

/**
 *	Returns the first match matched by the passed regular expression if 
 * 	there was a match (shortcut for preg_match and extracting)
 * 	@param string $subject
 * 	@param string $pattern
 * 	@return mixed
 */
function preg_match_first($subject, $pattern) {
	preg_match($pattern, $subject, $found);
	if (isset($found[1])) {
		return $found[1];
	}
	return false;
}

/**
 *	Swaps the values of two variables if the first one is larger than
 * 	the second.
 * 	@param integer|float $var1
 * 	@param integer|float $var2
 * 	@return boolean
 */
function swapIfGt(&$var1, &$var2) {
	if ($var2 > $var1) {
		$tmp = $var2;
		$var2 = $var1;
		$var1 = $tmp;
		unset($tmp);
		return true;
	}
	return false;
}

/**
 *	Swaps the values of two variables if the first one is smaller than the
 * 	second
 * 	@param integer|float $var1
 * 	@param integer|float $var2
 * 	@return boolean
 */
function swapIfLt(&$var1, &$var2) {
	return swapIfGt($var2, $var1);
}

/**
 *	Assert test for untrue values and creates a php user error notice
 * 	if the assertion failes. Read the wiki entry for more information
 * 	about <a href="http://de.wikipedia.org/wiki/Assertion">Assertion</a>
 * 	Assertion also helps you to be more DBC-Like (Design by Contract)
 * 	read about this in The pragmatic programmer.
 * 	<code>
 * 	assert(1 == 2); // will trigger an user notice error
 * 	</code>
 */
if (!function_exists('assert')) {
	function assert($condition) {
		if ($condition) return true;
		trigger_error('Assertion failed.'.var_export(debug_backtrace(), true), E_USER_NOTICE);
	}
}

/**
 * 	This should help to debug your stuff, this function only reacts
 * 	on a debugging level higher or equal to DEBUG_DEBUG. If you use
 * 	this function for every var_dumping or print_r action that you
 * 	might have used for debugging before you can use the search function
 * 	from your editor by searching for 'dump' and you get debugging echoes.
 * 
 * 	You can also get prevent the debug message from output (echo) by
 * 	passing true as second parameter so you get the output that would have
 * 	been printed as return 
 *
 * 	@param mixed $var
 * 	@param boolean $output
 */
function dump($var, $output = true) {
	if (!$output) {
		return var_export($var, true);
	} elseif (Registry::get('DEBUG') >= DEBUG_DEBUG) {
		echo var_dump($var);
	}
}

/**
 * 	This is just like {@link dump} but with <pre> output
 * 	@param mixed $var
 * 	@link {@link dump}
 */
function predump($var) {
	echo '<pre>'.LF.print_r($var, true).LF.'</pre>'.LF;
}

/**
 *	Lowercases the first letter in a string if it's a letter, just like
 * 	ucfirst does. This is not mulitbytesave!
 * 	@param string $string
 */
if (!function_exists('lcfirst')) {
	function lcfirst($string) {
		return strtolower(substr($string, 0, 1)).substr($string, 1);
	}
}

if (!function_exists('len')) {
	/**
	 * 	Alias for count or strlen on scalar vartypes, otherwise false
	 * 	
	 * <code>
	 * 	// returns 4 (not 1)
	 * 	$int = 1234;
	 * 	var_dump(len($int));
	 * 
	 * 	// returns the length of the string (multibyte safe), so it's 17'
	 * 	$string = "Hölle Hölle Hölle";
	 * 	var_dump(len($string));
	 * 
	 * 	// objects that implement an iterator or Countable pattern will return
	 *  // the value that is implemented in count()
	 * 	class t implements Countable {
	 * 		function count() {
	 * 			return 123;
	 * 		}
	 * 	}
	 * 	// in our example 123
	 * 	var_dump(len($t));	
	 * 	</code>
	 * 
	 * 	@param mixed $var
	 * 	@return int
	 * 	@todo TESTS, write a test for this
	 */
	function len($var) {
		if (is_array($var)) {
			return count($var);
		} elseif (is_string($var)) {
			return String::length($string);
		} elseif (is_numeric($var)) {
			return count((string)$var);
		} elseif (is_object($var)) {
			if (method_exists($var, 'count')) {
				return (int) $var->count();
			}
		}
		return false;
	}
}


if ( !function_exists( 'json_encode' ) ) {
/**
 *	Encodes php stuff to json code {@link http://www.json.org}.
 * 	This method provides json encoding if current php version is lower than
 * 	5.2 where json_encode was implemented. This does not handle character
 * 	encodings.
 * 	@param mixed $data
 * 	@return string
 */
function json_encode( $data ) {
	$data = trim($data);
	if (is_null($data)) return 'null';
	if (is_numeric($data)) return $data;
	if (is_array($data)) {
		$only_numeric = true;
		$last_key = -1;
		foreach (array_keys($data) as $key) {
			if (!is_numeric($key) || $last_key + 1 != $key) {
				$only_numeric = false;
				break;
			}
			$last_key++;
		}
		$render = '';
		$data_render = array();
		if ($only_numeric) {
			foreach ($data as $item) $data_render[]= json_encode( $item );
			$render = '['.join(',', $data_render).']';
		} else {
			foreach ($data as $key => $value) $data_render[] = json_encode($key).':'.json_encode($value);
			$render .= '{'.join( ',', $data_render ).'}';
		}
		return $render;
	}
    return '"'.quotemeta($data).'"';
    }
}

?>