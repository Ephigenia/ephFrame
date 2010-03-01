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
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

ephFrame::loadClass('ephFrame.lib.model.DB.QueryResult');

/**
 * Integration for MySQL Results
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.model.DB.MySQL
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 07.10.2007
 */
class MySQLQueryResult extends QueryResult {
	
	/**
	 * Moves the internal pointer of the mysql query result index to the 
	 * passed index.
	 * @param integer $dataIndex
	 */
	public function seek($dataIndex) {
		if ($this->count() > 0) {
			if ($dataIndex == QueryResult::SEEK_END) {
				$dataIndex = $this->count()-1;
			} elseif ($dataIndex == QueryResult::SEEK_START) {
				$dataIndex = 0;
			}
			mysql_data_seek($this->result, $dataIndex);
		}
		return parent::seek($dataIndex);
	}
	
	/**
	 * Returns the next result as associative array or false if there
	 * is no result left
	 * @return array()|boolean
	 */
	public function fetchAssoc() {
		return mysql_fetch_assoc($this->result);
	}
	
	/**
	 * Returns the next result as object or false if there
	 * is no result left
	 * @return array()|boolean
	 */
	public function fetchObject() {
		return mysql_fetch_assoc($this->result);
	}
	
	/**
	 * Returns the next result as indexed array or false if there
	 * is no result left
	 * @return array()|boolean
	 */
	public function fetchIndexed() {
		return mysql_fetch_row($this->result);
	}
	
	/**
	 * Returns the number of rows in the hole result as integer
	 * @return integer
	 */
	public function numRows() {
		if (!isset($this->numRows) && is_resource($this->result)) {
			$this->numRows = mysql_num_rows($this->result);
		}
		return $this->numRows;
	}
	
}