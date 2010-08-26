<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Brunnenstr. 10
 *                      10119 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

/**
 * Static DBQuery/Result Storage
 * 
 * This class can store DB Queries, their result ids and a {@link Timer} Object
 * that stores the response time the query took.
 * 
 * @package ephFrame
 * @version 0.1
 * @subpackage ephFrame.lib.model.DB
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 23.07.2007
 */
class QueryHistory implements Countable
{
	/**
	 * Stores all the queries in an array
	 * @var array(array)
	 */
	public $data = array();
	
	/**
	 * Stores the single instance of QueryHistory
	 * @var QueryHistory
	 */
	public static $instance;
	
	public static function instance()
	{
  		if (self::$instance === null) {
  			$classname = __CLASS__;
  			self::$instance = new $classname();
  		}
  		return self::$instance;
  	}
	
	/**
	 * Adds a query to the history
	 * @param DBQuery $query
	 * @param QueryResult $result
	 * @param Timer $timer
	 */
	public static function add($query, $result, Timer $timer)
	{
		$history = self::instance();
		$history->data[] = array(
			'query' => $query,
			'result' => $result,
			'timer' => $timer
		);
		return true;
	}
	
	/**
	 * Returns the number of queries in the history
	 * @return integer
	 */
	public function count() 
	{
		$history = self::instance();
		return count($history->data);
	}
	
	/**
	 * Returns the last query from the query history
	 * @return DBQuery
	 */
	public function last() 
	{
		$history = self::instance();
		if (count($history) > 0) return $history->data[count($history)-1]['query'];
		return false;
	}
	
	/**
	 * Returns a query from the history
	 * @param integer $queryIndex
	 * @return DBQuery
	 */
	public function query($queryIndex) 
	{
		if ($this->defined($queryIndex)) throw new QueryHistoryIndexNotFoundException();
		return $this->data[$queryIndex]['query'];
	}
	
	/**
	 * Return the time in seconds the queries took all in all
	 * @return float
	 */
	public function timeTotal($precision = 6) 
	{
		$sum = 0.0;
		foreach($this->data as $index => $data) {
			$sum += $data['timer']->time();
		}
		return round($sum, $precision);
	}
	
	public function render() 
	{
		$rendered = '';
		foreach ($this->data as $index => $data) {
			//@todo make the following lines better
			if($data['result']->numRows()) {
				$rendered .= sprintf('Query #%s (%ss, %s result/s):'.LF.'%s'.LF.LF,
									$index +1 ,
									round($data['timer']->render(), 5),
									$data['result']->numRows(),
									(string) $data['query']
								);
			} else {
				$rendered .= sprintf('Query #%s (%ss):'.LF.'%s'.LF.LF,
									$index +1,
									round($data['timer']->render(), 5),
									(string) $data['query']
								);
			}
			
		}
		return $rendered;
	}
	
	public function __toString() 
	{
		return $this->render();
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class QueryHistoryException extends BasicException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class QueryHistoryIndexNotFoundException extends BasicException 
{}