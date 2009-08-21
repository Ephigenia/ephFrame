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
 * Time, methods and tools dealing with time.
 * 
 * This is partly tested in {@link TestTime}
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 02.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 * @version 0.2
 */
class Time extends Helper {
	
	/**
	 * Shifts a timestamp by days, month, years in the future or
	 * past. If you want to shift to the future you use positve values,
	 * dates in the past by using negative values.
	 * 
	 * @param integer	$timestamp	Time that should be shifted
	 * @param integer	$days		Number of Days to shift
	 * @param integer	$months		Number of Months to shift
	 * @param integer	$years		Number of Years to shift
	 * @return integer
	 */
	public static function shift($timestamp, $days = null, $months = null, $years = null) {
		return mktime(0, 0, 0, date('m', $timestamp) + $months, date('d', $timestamp) + $days, date('Y', $timestamp) + $years);
	}
	
	/**
	 * Checks if the given timestamp is from today
	 * @param integer	$timestamp
	 * @return boolean
	 */
	public static function isToday($timestamp) {
		return (date('d.m.Y', $timestamp) == date('d.m.Y', time()));
	}
	
	/**
	 * Checks if the given timestamp is from yesterday
	 * @param integer	$timestamp
	 * @return boolean
	 */
	public static function isYesterday($timestamp) {
		$yesterday = self::shift(time(),-1);
		$today = self::shift(time(),0);
		return ($timestamp >= $yesterday && $timestamp < $today);
	}
	
	/**
	 * Checks if the given timestamp is from tomorrow
	 * @param integer	$timestamp
	 * @return boolean
	 */
	public static function isTomorrow($timestamp) {
		$tomorrow = self::shift(time(), 1);
		$tomorrow2 = self::shift(time(), 2);
		return ($timestamp >= $tomorrow && $timestamp < $tomorrow2);
	}
	
	/**
	 * Checks if the given timestamp is from this week
	 * @param integer	$timestamp
	 * @return boolean
	 */
	public static function isThisWeek($timestamp) {
		return (date('W', $timestamp) == date("W", time()));
	}
	
	/**
	 * Checks if the given timestamp is from this month
	 * @param integer	$timestamp
	 * @return boolean
	 */
	public static function isThisMonth($timestamp) {
		return (date('m', $timestamp) == date("m", time()));
	}
	
	/**
	 * Checks if the given timestamp is from this year
	 * @param integer	$timestamp
	 * @return boolean
	 */
	public static function isThisYear($timestamp) {
		return (date('Y', $timestamp) == date('Y', time()));
	}
	
	/**
	 * Returns Time that has spend since $timestamp in human
	 * readable format. (German)
	 * // todo put this to i18n class
	 * @param  integer	$timstamp	Timestamp from the past
	 * @return string	Created human readable time string
	 */
	public static function timeAgoInWords($timestamp) {
		if (self::isToday($timestamp)) {
			$diff = time() - $timestamp;
			$hours = $diff / 3600;
			$minutes = $hours / 60;
			// more than 60 minutes
			if ($diff > 3600) {
				if (round($hours) > 1) {
					return sprintf('vor %s Stunden', round($hours));
				} else {
					return sprintf('vor 1 Stunde');
				}
			// more than a minute
			} elseif ($diff > 60) {
				return 'vor '.(ceil($hours * 60)).' Minuten';
			} else {
				return 'Jetzt gerade';
			}
		} elseif (self::isYesterday($timestamp)) {
			return 'gestern';
		} else {
			$daysDifference = floor((abs(time() - $timestamp)) / DAY);
			if ($daysDifference == 1) {
				return 'gestern';
			} elseif($daysDifference <= 2) {
				return 'vorgestern';
			} elseif ($daysDifference <= 7) {
				return 'vor '.$daysDifference.' Tagen';
			} else if ($daysDifference == 7) {
				return 'vor einer Woche';
			} else if ($daysDifference <= 14) {
				return 'vor zwei wochen';
			} else {
				return gmstrftime("am %d.%m.%Y", $timestamp);
			}
		}
		return false;
	}
	
	/**
	 * Calculates the difference of two dates and returns an associative
	 * array with the keys "years","months","days"
	 *
	 * <code>
	 * // calculate the age of a person
	 * echo implode(",",Time::diff(time(),"1953/04/15"));
	 * </code>
	 *
	 * @param	integer|string	$time1
	 * 					Timestamp or Date as string
	 * @param	integer|string	$time2
	 * 					Timestamp or Date as string
	 * @param	boolean			$signed
	 * 					true returns difference signed
	 * 					false return difference unsigned
	 * @return array(integer) Calculated Difference Array
	 */
	public static function diff($time1, $time2 = null, $signed = true) {
		if ($time2 === null) $time2 = time();
		// split arguments into year, month and day
		if (is_int($time1)) {
			list ($year1, $month1, $day1) = array (date('Y',$time1), date('n', $time1), date('j', $time1));
		} else {
			list ($day1, $month1, $year1) = preg_split('/-|\/|\\./', $time1);
		}
		if (is_int($time2)) {
			list ($year2, $month2, $day2) = array (date('Y',$time2), date('n', $time2), date('j', $time2));
		} else {
			list ($day2, $month2, $year2) = preg_split('/-|\/|\\./', $time2);
		}
		// calculate difference
		$years = intval($year1 - $year2);
		// less than a year
		if ($years < 0) {
			$years = 0;
		}
		$months = intval($month1 - $month2);
		if ($months < 0) {
			$years--;
			$months = 12 + $months;
		}
		$days = intval($day1 - $day2);
		if ($days < 0) {
			$months--;
			$days = self::lastDayOfMonth($months) + $days;
			if ($months < 0) {
				$years--;
				$months = 12 + $months;
				}			}
		// leap years
		// count the days to add by cycling through the years try to find a leap year
		if ($year1 < $year2) {
			$leapYearCycleStart = $year1;
			$leapYearCycleEnd = $year2;
		} else {
			$leapYearCycleStart = $year2;
			$leapYearCycleEnd = $year1;
		}
		for ($i = $leapYearCycleStart; $i <= $leapYearCycleEnd; $i++) {
			if (date("L",mktime(0,0,0,1,1,$i))) {
				$days++;
			}
		}
		$return = array("years" => $years, "months" => $months, "days" => $days);
		// signed?
		if (!$signed) {
			foreach ($return as $key => $value) {
				$return [$key] = abs($value);
			}
		}
		// return result
		return $return;
	}
	
	/**
	 * Return a timestamp for the monday of a week a day is in
	 */
	public static function weekMonday($year, $month, $day) {
		return strtotime($year.'-W'.date('W', mktime(12, 0, 0,$month, $day, $year)).'-1');
	}
		
	/**
	 * Gets the last day of a month
	 * @param	integer	Last day of a month
	 * @return integer Last day of a month
	 */
	public static function lastDayOfMonth($timestamp = null) {
		return date('d', self::lastDayOfMonthTimestamp($timestamp));
	}
	
	/**
	 * Returns the timestamp fo the last day of a month
	 * @param integer $timestamp
	 * @return integer Timestamp of the last day of a month
	 */
	public static function lastDayOfMonthTimestamp($timestamp = null) {
		if ($timestamp === null) {
			$timestamp = time();
		}
		return mktime(0, 0, 0, date('m',$timestamp), 0, date('Y',$timestamp));
	}
		
	/**
	 * Calculates the age of something in years
	 * <code>
	 * // calculate the age by using a string
	 * echo "I'm ".Time::calculateAgeInYears('1983/04/15')." years old.";
	 * // calculate the age by using a timestamp
	 * echo "My small brother is ".Time::calculateAgeInYears($litteBrotherBdate)." years old.";
	 * </code>
	 * @param integer|string	Timestamp or Date as a string
	 * 					If you pass the date of birth as a string, be sure to use a english formatted date
	 * @return integer Calculated Age in Years
	 * @see diff
	 */
	public static function ageInYears($time) {
		$difference = self::diff(date('j-n-Y'), $time);
		// reduce age to one if the day and month is not reached
		if (date("n") < $difference["months"] && date("j") < $difference["days"]) {
			$difference["years"]--;
		}
		// return the calculated age
		return $difference["years"];
	}
	
}