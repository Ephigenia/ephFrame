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
 *	Array Helper for Array Manipulation
 * 	
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 02.05.2007
 * 	@version 0.2
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.helper
 */
class ArrayHelper extends Helper {
	
	/**
	 * 	Appends values from $append to $array
	 * 	@param array(mixed) $array
	 * 	@param array(mixed) $append
	 * 	@return array(mixed)
	 */
	public function appendFromArray($array, $append) {
		foreach($append as $value) {
			$array[] = $value;
		}
		return $array;
	}
	
	/**
	 *	Returns the number of dimensions in $array
	 * 	@param array(mixed)
	 * 	@return integer
	 */
	public static function dimensions($array) {
		if (!is_array($array)) {
			return false;
		}
		$result = 1;
		$maxDimensions = 0;
		foreach($array as $value) {
			if (is_array($value)) {
				$tmp = self::dimensions($value);
				if ($tmp > $maxDimensions) {
					$maxDimensions = $tmp;
				}
			}
		}
		return $result + $maxDimensions;
	}
	
	/**
	 *	Implode Array or Objects that implement IteratorAggregate into a string
	 * 	with optional $format string and callbacks for key and value manipulation.
	 * 
	 * 	The following examples show you how powerfull this method is:
	 * 	<code>
	 * 	$getVars = array('id' => 23, 'search' => 'Where are me keys?');
	 * 	// echoes id=23&search=Where+are+me+keys%3F
	 * 	echo implodef($getVars, '&', '%s=%s', null, 'urlencode');
	 * 	// echoes id=23&SEARCH=Where+are+me+keys%3F
	 * 	echo implodef($getVars, '&', '%s=%s', 'strtoupper', 'urlencode');
	 * 	// switch places on the result
	 * 	// echoes 23=id&Where+are+me+keys%3F=SEARCH
	 * 	echo implodef($getVars, '&', '%2$s=%1$s', 'strtoupper', 'urlencode');
	 * 	// use classes or objects for callbacks
	 * 	// static
	 * 	echo implodef($getVars, '&', '%2$s=%1$s', array('staticclassname', 'methodname'));
	 * 	// object
	 * 	echo implodef($getVars, '&', '%2$s=%1$s', array($instanceOfObject, 'methodname'));
	 * 	// prepend something to the keys
	 * 	// echose myprepend_id=23&myprepend_search=Where are me keys?
	 * 	echo implodef($getVars, '&', 'myprepend_%s=%s').'<br />';
	 * 	// you don'n want the keys? Just the values, allright:
	 * 	// 23&Where are me keys?
	 * 	echo implodef($getVars, '&', '%2$s').'<br />';
	 * 	</code>
	 * 	
	 * 	@param array(string)|object $arr	Array or Object that should be imploded
	 * 	@param string $glue optional Glue for imploding, default is an empty string
	 * 	@param string $format optional format for imploded key/values, see doc
	 * 	@param string|array $keyCallback callback for keys imploded
	 * 	@param string|array $valueCallback call back for values imploded
	 * 	@return string 
	 */
	public static function implodef($arr, $glue = '', $format = '', $keyCallback = null, $valueCallback = null) {
		if (is_object($arr) && $arr instanceof IteratorAggregate) {
			$arr = iterator_to_array($arr);
		}
		if (!is_array($arr)) return false;
		if (!empty($format)) {
			foreach($arr as $k => $v) {
				$km = $k;
				$vm = $v;
				if ($keyCallback !== null) {
					$km = call_user_func_array($keyCallback, array($km));
				}
				if ($valueCallback !== null) {
					$vm = call_user_func_array($valueCallback, array($vm));
				}
				$arr[$k] = sprintf($format, $km, $vm);
			}
		}
		return implode($glue, $arr);
	}
	
	/**
	 *	Test if an array is really empty, checking all dimensions for 
	 * 	emptiness. PHP's function not support multip dimensional arrays
	 * 	with empty(). This is what this method should provide
	 * 	
	 * 	This can possibly be used with Objects implement the Iterator
	 * 	Pattern, but then the test of emptiness is tested on the object
	 * 	itsself.
	 * 	
	 * 	<code>
	 * 	// using phps function
	 * 	$arr = array(array());
	 *	var_dump(empty($arr));
	 * 	// turns out 'false'
	 * 	// after
	 * 	var_dump(ArrayHelper::isEmpty($arr));
	 * 	// turns out 'true'
	 * 	</code>
	 * 	@param array(mixed) $in
	 * 	@return boolean
	 */
	public static function isEmpty(Array $arr) {
		foreach($arr as $value) {
			if (is_array($value)) {
				if (self::isEmpty($value)) return true;
			} elseif (!empty($value)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 *	Extracts a value from an array defined by the given path.
	 * 	Use this for arrays with more than one dimension. If the path
	 * 	was not found null is returned
	 * 	<code>
	 * 	$arr = array('table1' => array('row' => 'I\'m a test'));
	 * 	echo ArrayHelper::extract($arr, 'table1/row/');
	 * 	</code>
	 * 	Some malformed paths are corrected such as:
	 * 	//table1/row///
	 * 	/table1///
	 * 	
	 * 	You can also extract values with the normal array notation with
	 * 	cornered brakets like this:
	 * 	<code>
	 * 	echo ArrayHelper::extract($arr, 'table1[row]');
	 * 	</code>
	 * 	@param Array $arr
	 * 	@param String $path
	 * 	@return mixed
	 */
	public static function extract($arr, $path) {
		if (!is_array($arr)) return false;
		$path = preg_replace(
			array('/\[+/', '/\]+/', '/\/{2,}/', '/^\/|\/$/'),
			array('/', '', '/', ''),
			$path);
		$first = substr($path, 0, strpos($path, '/'));
		if (empty($first) && isset($arr[$path])) {
			return $arr[$path];
		} elseif (isset($arr[$first])) {
			$nextPath = substr($path, strpos($path, '/') + 1);
			return self::extract($arr[$first], $nextPath);
		}
		return null;
	}
	
	/**
	 *	Implodes an array just like php does but trying to render
	 * 	objects in the array
	 * 	@param string $glue
	 * 	@param array $array
	 * 	@return string
	 */
	public static function implode($glue, Array $array) {
		$rendered = array();
		foreach ($array as $value) {
			if (is_object($value)) {
				if (method_exists($value, 'render')) {
					$rendered = $value->render();
				} else {
					$rendered = $value->__toString();
				}
			} else {
				$rendered = $value;
			}
		}
		return implode($glue, $rendered);
	}
	
	/**
	 *	Strips slashes from every array value and returns the array
	 * 	@param array() $array
	 * 	@return array()
	 */
	public static function stripslashes($array) {
		if (!is_array($array)) {
			return stripslashes($array);
		} else {
			return array_map('stripslashes', $array);
		}
	}
	
	/**
	 *	Returns all Objects from an array of objects that have the
	 * 	given type
	 * 	@param array $input
	 * 	@param string|array $className
	 * 	@return Array
	 */
	public static function extractByClassName($input, $className) {
		if (!is_array($className)) {
			$matchAgainst = array($className); 
		} else {
			$matchAgainst = $className;
		}
		$return = array();
		foreach ($input as $possible) {
			if (is_object($possible) && in_array(get_class($possible), $matchAgainst)) {
				$return[] = $possible;
			}
		}
		return $return;
	}
	
	/**
	 *	Reduce Dimensions of an array to 1
	 * 
	 * 	Associative array (with key like $entry['list']['hey']) are
	 * 	preserved but overwritten if any higher dimension has the same
	 * 	key.
	 * 	
	 * 	@param array(mixed) $array
	 * 	@param boolean $associative optional
	 * 	@throws ArrayExpectedException
	 * 	@return array(mixed)
	 */
	public static function flatten($array, $associative = true) {
		if (!is_array($array)) {
			throw new ArrayExpectedException();
		}
		$new = array();
	    if (!is_array($array)) return $new;
	    foreach ($array as $key => $value) {
	        if (is_scalar($value) || is_resource($value)) {
	        	if ($associative) {
					$new[$key] = $value;
	        	} else {
	        		$new[] = $value;
	        	}
	        } elseif (is_array($value)) {
	            $new = array_merge($new, self::flatten($value, $associative));
	        }
	    }
    	return $new;
	}
	
	/**
	 * 	Reduce Dimensions of an array to 1 associative array, values with 
	 * 	double keys are overwritten
	 * 
	 * 	This is just an alias for {@link flatten}
	 * 
	 * 	@param array(mixed) $array
	 * 	@throws ArrayExpectedException
	 * 	@return array(mixed)
	 */
	public static function flattenAssociative($array) {
		return self::flatten($array, true);
	}
	
	/**
	 *	Returns an flattened array indexed.
	 * 	This is just an alias for {@link flatten}
	 * 	@param array(mixed) $array
	 * 	@throws ArrayExpectedException
	 * 	@return array(mixed)
	 */
	public static function flattenIndexed($array) {
		return self::flatten($array, false);
	}
	
	/**
	 *	Drops every index from the input array that matches one of the names
	 * 	as key given in the the second parameter:
	 * 	<code>
	 * 	$input = array('action' => 'index', 'id' => 23, 'method' => 'list');
	 * 	// will drop 'action' and 'method' from the array
	 * 	$result = ArrayHelper::dropIndex($input, array('action', 'method');
	 * 	</code>
	 * 
	 * 	Example with second parameter beeing type of string
	 * 	<code>
	 * 	$input = array('action' => 'index', 'id' => 23, 'method' => 'list');
	 * 	// will drop 'action'
	 * 	$result = ArrayHelper::dropIndex($input, 'action');
	 * 	</code>
	 * 
	 * 	The method will check all input vars, if the first param is no array
	 * 	it will fail. The second parameter is converted in an array if it's not
	 * 	an array and then used.
	 * 
	 * 	@param array() array that should be be-dropped
	 * 	@param array(string)|string|integer $input name of indexes that should be dropped of the array
	 * 	@param boolean be-drop rekursive on the input array, or just 1-dimension
	 * 	@return array(string) cleaned array
	 */
	public static function dropIndex($input, $indexNames = array(), $recursive = false) {
		assert(is_array($input));
		if (count($indexNames) == 0) return $input;
		if (!is_array($indexNames)) {
			$indexNames = array($indexNames);
		}
		$return = array();
		foreach ($input as $key => $value) {
			if (in_array($key, $indexNames)) {
				$return[$key] = $value;
			} else if (is_array($value)) {
				$return[$key] = self::dropIndex($value);
			}
		}
		return $return;
	}

}

/**
 * 	Alias class for fast array acces
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.helper
 *  @since Marcel Eichner // Ephigenia <love@ephigenia.de
 * 	@since 29.11.2007
 */
class A extends ArrayHelper {} 

?>