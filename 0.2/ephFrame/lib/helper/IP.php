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
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

/**
 * IPv4 Class
 * 
 * helper class for converting ipv4 addresses to integers and back
 * see the examples in the methods for code examples
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 29.12.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 */
class IP extends Helper {
	
	/**
	 * Convert an ip to number
	 * <code>
	 * // echoes 1470267142
	 * echo $this->IP->toNumber('87.162.127.6');
	 * </code>
	 * @param string|array(integer) $ip
	 * @static
	 * @return integer
	 */
	public static function toNumber($ip) {
		if (empty($ip)) {
			return 0;
		}
		if (!is_array($ip)) {
			$ips = explode('.', $ip);
		}
        return ($ips[3] + ($ips[2] * 256) + ($ips[1] * 65536) + ($ips[0] * 16777216));
	}
	
	/**
	 * Convert number to ip address
	 * <code>
	 * // echoes '87.162.127.6'
	 * echo $this->IP->toIP(1470267142);
	 * </code>
	 * @param integer $number
	 * @static
	 * @return string
	 */
	public static function toIP($number) {
		if (empty($number)) {
			return '';
		}		
		$ip = implode('.', array(($number >> 24) & 0xff, ($number >> 16) & 0xff, ($number >> 8) & 0xff, $number & 0xff));
		pr($ip);
		exit;
	}

}