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
 * // todo match this to {@link IndexedArray}
 * @package ephFrame
 * @subpackage ephFrame.lib.model.DB
 * @version 0.1
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 30.08.2007
 */
class QueryResult extends IndexedArray implements Iterator, Countable 
{
	const FETCH_ASSOC = 'assoc';
	const FETCH_OBJECT = 'object';
	const FETCH_INDEXED = 'indexed';
	
	const SEEK_END = 'end';
	const SEEK_START = 'start';
	
	/**
	 * Stores the sql result
	 * @var ressource
	 */
	public $result;
	
	/**
	 * stores the current fetch method name
	 * @var string
	 */
	public $fetchMethod = self::FETCH_ASSOC;
	
	/**
	 * Stores the number of rows in this Query result
	 * @var integer
	 */
	public $numRows;
	
	/**
	 * stores the valid fetch method names that are
	 * ok for this {@link DAO}
	 * @var array(string)
	 */
	public $validFetchMethods = array(
		self::FETCH_ASSOC, self::FETCH_INDEXED, self::FETCH_OBJECT
	);
	
	/**
	 * @var integer
	 */
	protected $iteratorPosition = 0;
	
	/**
	 * Query Result constructor excepts a mysql result ressource
	 * @param ressource $result
	 * @return DAOQueryResult
	 */
	public function __construct($result) 
	{
		parent::__construct();
		$this->result = $result;
		return $this;
	}

	/**
	 * Sets the fetch method that is used when you call {@link fetch} or
	 * {@link next}. If the fetchmethodname is not in the list of valid
	 * fetch methods a {@link DAOInvalidFetchMethod} Exception is thrown
	 * @param string $fetchMethod
	 * @return DAOQueryResult
	 */
	public function fetchMethod($fetchMethod = null) 
	{
		if (func_num_args() == 0 || $fetchMethod == null) return $this->fetchMethod();
		if (!$this->isValidFetchMethod($fetchMethod)) throw new DBInvalidFetchMethod($this, $fetchMethod);
		$this->fetchMethod = $fetchMethod;
		return $this;
	}
	
	private function isValidFetchMethod($fetchMethod) {
		return in_array($fetchMethod, $this->validFetchMethods);
	}
	
	public function fetchAll($fetchMethod = null) 
	{
		if (count($this->data) != $this->numRows()) {
			$this->data = array();
			while($d = $this->fetch($fetchMethod)) {}
		}
		return $this->data;
	}
	
	public function seek($dataIndex) 
	{
		if ($dataIndex > $this->count()) return false;
		$this->iteratorPosition = $dataIndex;
		return $this;
	}
	
	public function fetch($fetchMethod = null) 
	{
		if ($fetchMethod !== null) $this->fetchMethod($fetchMethod);
		if ($row = $this->{'fetch'.ucFirst($this->fetchMethod)}()) {
			$this->data[] = $row;
			$this->iteratorPosition++;
		}
		return $row;
	}
	
	public function fetchObject() 
	{}
	public function fetchAssoc() 
	{}
	public function fetchIndexed() 
	{}
	
	public function numRows() 
	{}
	
	/**
	 * Alias for {@link numRows}
	 * @return integer
	 */
	public function count() 
	{
		return $this->numRows();
	}

	public function rewind() 
	{
		$this->seek(self::SEEK_START);
		return parent::rewind();
	}

	public function next() 
	{
		return $this->fetch();
	}

	/**
	 * @return integer|string
	 */
	public function key() 
	{
		return key($this->set);
	}

	/**
	 * @return mixed
	 */
	public function current() 
	{
		return current($this->data);
	}

	public function valid() 
	{
		return ($this->iteratorPosition < count($this));
	}
	
	/**
	 * Returns the dumped contents of this Result
	 * @return string
	 */
	public function dump() 
	{
		return $this->__toString();
	}
	
	public function __toString() 
	{
		$return = '';
		$i = 0;
		while ($a = $this->fetchAssoc()) {
			$return .= sprintf('Result %s of %s', $i, $this->numRows());
			foreach ($a as $key => $value) {
				$return .= sprintf(TAB.'%s: %s'.LF, $key, $value);
			}
			$i++;
		}
		return $return;
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class DBQueryResultException extends DBException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class DBInvalidFetchMethod extends DBQueryResultException 
{
	public function __construct($fetchMethod) 
	{
		parent::__construct('Invalid fetch method given \''.$fetchMethod.'\'.');
	}
}