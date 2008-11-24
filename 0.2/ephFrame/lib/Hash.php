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

ephFrame::loadClass('ephFrame.lib.Set');

/**
 *	Hash-Table Class
 * 
 * 	A Hash-Table is an associative array with possible double values but no
 * 	double keys.
 * 
 * 	# {@link http://en.wikipedia.org/wiki/Lookup_table Lookup Table}
 * 
 * 	This class is based on the indexed Array {@link Set} and so can do a lot
 * 	of stuff a {@ink Set} can do, also chaining.
 * 
 * 	This is partially tested in {@link TestHash}
 *
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 18.07.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class Hash extends Set {
	
	/**
	 * 	Fills the Set with the passed $data
	 * 	@param array(mixed) $data
	 * 	@return Set
	 */
	public function fromArray(Array $data) {
		$this->data = $data;
		return $this;
	}

	/**
	 * 	Add an other $key => $value pair to the hash or just an other value
	 * 	without a key
	 *	
	 * 	@param string|integer $key
	 * 	@param mixed $value
	 * 	@return Hash
	 */
	public function add($key, $value = null) {
		if (func_num_args() == 1) {
			$this->data[] = $key;
		} else {
			$this->data[$key] = $value;
		}
		return $this;
	}
	
/**
	 * 	Alias for {@link push}
	 *
	 * 	@param mixed $val
	 * 	@return Hash
	 */
	public function append($val) {
		$args = func_get_args();
		return $this->callmethod('push', $args);
	}
	
	/**
	 * 	Appends $val to the hash using next higher index key as index.
	 *
	 * 	@param mixed $val
	 * 	@return Hash
	 */
	public function push($val) {
		if (func_num_args() > 1) {
			foreach(func_get_args() as $value) {
				$this->push($value);
			}
		} else {
			$this->data[] = $val;
		}
		return $this;
	}
	
	/**
	 * 	Sets an other $key => $value pair to the hash, existing values will be
	 * 	overwritten.
	 *
	 * @param string|integer $key
	 * @param mixed $value
	 * @return Hash
	 */
	public function setValue($key, $value) {
		$this->data[$key] = $value;
		return $this;
	}
	
	/**
	 * 	Alias for {@link setValue}
	 *
	 * 	@param string|integer $key
	 * 	@param mixed $value
	 * 	@return Hash
	 */
	public function set($key, $value = null) {
		return $this->setValue($key, $value);
	}
	
}

/**
 * 	@package ephFrame
 *	@subpackage ephFrame.lib.exception 
 */
class HashException extends BasicException {
}

?>