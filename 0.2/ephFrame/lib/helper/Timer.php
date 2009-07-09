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
 * A Class for stopping time or benchmarking. Helps you to determine
 * time intervals.
 * 
 * Usage as object:
 * <code>
 * $timer = new Timer();
 * for ($i = 0; $i < 10000; $i++) { }
 * echo 'for took '.$timer.'ms';
 * </code>
 * 
 * Usage as static:
 * <code>
 * Timer::set('firstloop');
 * for ($i = 0; $i < 10000; $i++) { }
 * Timer::stop('firstloop');
 * Timer::set('secondloop');
 * for ($i = 0; $i < 10000; $i++) { }
 * Timer::stop('secondloop');
 * echo Timer::dump();
 * </code>
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 22.07.2007
 * @package ephFrame
 * @version 0.1
 * @subpackage ephFrame.lib.helper
 */
class Timer extends Helper {
	
	public static $timers = array();
	
	private $start;
	private $stop;
	
	public function __construct() {
		$this->start();
		return $this;
	}
	
	public function time() {
		if ($this->stop > 0) {
			return ($this->stop - $this->start);
		}
		return (microtime(true) - $this->start);
	}
	
	public function __toString() {
		return $this->render();
	}
	
	public function render() {
		return $this->time();
	}
	
	public function start() {
		$this->start = microtime(true);
		return $this;
	}
	
	public function end() {
		$this->stop = microtime(true);
		return $this;
	}
	
	public static function set($timerName) {
		if (!is_string($timerName)) throw new StringExpectedException();
  		self::$timers[$timerName] = new Timer();
	}
	
	public function stopTimer() {
		$this->end();
		return $this;
	}
	
	public static function stop($timerName) {
		if (!is_string($timerName)) throw new StringExpectedException();
		if (!self::hasTimer($timerName)) throw new TimerNotFoundException($timerName);
		self::$timers[$timerName]->stopTimer();
	}
	
	public static function read($timerName) {
		if (!is_string($timerName)) throw new StringExpectedException();
		if (!self::hasTimer($timerName)) throw new TimerNotFoundException($timerName);
		return self::$timers[$timerName]->__toString();
	}
	
	public static function hasTimer($timerName) {
		return (isset(self::$timers[$timerName]));
	}
	
	public static function dump() {
		$dump = '';
		foreach(self::$timers as $timerName => $timer) {
			$dump .= $timerName.': '.$timer->__toString().LF;
		}
		return $dump;	
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class TimerException extends BasicException {}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class TimerNotFoundException extends TimerException {
	public function __construct($timerName) {
		$this->message = 'No timer found with \''.$timerName.'\' as name.';
		parent::__construct();
	}
}