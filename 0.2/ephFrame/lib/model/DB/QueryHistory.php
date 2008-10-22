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
 * 	Static DBQuery/Result Storage
 * 
 * 	This class can store DB Queries, their result ids and a {@link Timer} Object
 * 	that stores the response time the query took.
 * 
 * 	@package ephFrame
 * 	@version 0.1
 * 	@subpackage ephFrame.lib.model.DB
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 23.07.2007
 */
class QueryHistory extends Object implements Countable, Renderable {
	
	/**
	 *	Stores all the queries in an array
	 * 	@var array(array)
	 */
	public $data = array();
	
	/**
	 *  Stores the single instance of QueryHistory
	 *
	 * 	@var QueryHistory
	 */
	public static $instance;
	
	public static function getInstance() {
  		if (self::$instance === null) {
  			$classname = __CLASS__;
  			self::$instance = new $classname();
  		}
  		return self::$instance;
  	}
	
	/**
	 *	Adds a query to the history
	 * 	@param DBQuery $query
	 * 	@param QueryResult $result
	 * 	@param Timer $timer
	 */
	public static function add($query, $result, Timer $timer) {
		$history = self::getInstance();
		$history->data[] = array(
			'query' => $query,
			'result' => $result,
			'timer' => $timer
		);
		return true;
	}
	
	/**
	 *	Returns the number of queries in the history
	 * 	@return integer
	 */
	public function count() {
		return count($this->data);
	}
	
	/**
	 *	Returns the last query from the query history
	 * 	@return DBQuery
	 */
	public function last() {
		if (count($this) > 0) return $this->data[count($this)-1]['query'];
		return false;
	}
	
	/**
	 *	Returns a query from the history
	 * 	@param integer $queryIndex
	 * 	@return DBQuery
	 */
	public function query($queryIndex) {
		if ($this->defined($queryIndex)) throw new QueryHistoryIndexNotFoundException();
		return $this->data[$queryIndex]['query'];
	}
	
	public function render() {
		if (!$this->beforeRender()) return '';
		$rendered = '';
		if (!$this->beforeRender()) return $rendered;
		foreach ($this->data as $index => $data) {
			$rendered .= sprintf('Query #%s (%ss, %s result/s):'.LF.'%s'.LF.LF,
									$index,
									round($data['timer']->render(), 5),
									$data['result']->numRows(),
									(string) $data['query']
								);
		}
		return $this->afterRender($rendered);
	}
	
	public function beforeRender() {
		return true;
	}
	
	public function afterRender($rendered) {
		return $rendered;
	}
	
	public function __toString() {
		echo 'QueryHistory Render reached';
		return $this->render();
	}
	
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class QueryHistoryException extends BasicException {}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class QueryHistoryIndexNotFoundException extends BasicException {}

?>