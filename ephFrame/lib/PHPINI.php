<?php 

/**
 * Static class to read constants from php ini
 * 
 * @author Ephigenia // Marcel Eichner <love@ephigenia.de>
 * @since 26.05.2009
 */
class PHPINI
{
	/**
	 * Tries to read a $varname from the php ini and returns it’s value
	 * 
	 * @param string $varname
	 * @return string|boolean|integer
	 */
	public static function get($varname)
	{
		$value = ini_get($varname);
		if (!$value) {
			return false;
		}
		// try to see file size in var
		if (preg_match('@(\d+(.\d+)?)\s*(m|kb|g)@i', $value, $found)) {
			switch(strtolower($found[3])) {
				case 'm':
					$value = (float) $found[1] * MEGABYTE;
					break;
				case 'kb':
					$value = (float) $found[1] * KILOBYTE;
					break;
				case 'g':
					$value = (float) $found[1] * GIGABYTE;
					break;
			}
		// parse true/false values
		} elseif (preg_match('@(true|yes|1)@i', $value)) {
			$value = true;
		} elseif (preg_match('@(false|no|0)@i', $value)) {
			$value = false;
		}
		return $value;
	}
	
	/**
	 * Tries to write a config variable. If ini_set is disabled false is
	 * returned
	 * @param string $name
	 * @param mixed $value
	 * @return boolean
	 */
	public static function set($name, $value)
	{
		if (!function_exists('ini_set')) {
			return false;
		}
		return ini_set($name, $value);
	}	
}