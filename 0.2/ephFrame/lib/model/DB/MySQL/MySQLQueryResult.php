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

ephFrame::loadClass('ephFrame.lib.model.DB.QueryResult');

/**
 * 	Integration for MySQL Results
 * 	
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.model.DB.MySQL
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 07.10.2007
 */
class MySQLQueryResult extends QueryResult {
	
	/**
	 *	Returns the next result as associative array or false if there
	 * 	is no result left
	 * 	@return array()|boolean
	 */
	public function fetchAssoc() {
		return mysql_fetch_assoc($this->result);
	}
	
	/**
	 *	Returns the next result as object or false if there
	 * 	is no result left
	 * 	@return array()|boolean
	 */
	public function fetchObject() {
		return mysql_fetch_assoc($this->result);
	}
	
	/**
	 *	Returns the next result as indexed array or false if there
	 * 	is no result left
	 * 	@return array()|boolean
	 */
	public function fetchIndexed() {
		return mysql_fetch_row($this->result);
	}
	
	/**
	 *	Returns the number of rows in the hole result as integer
	 * 	@return integer
	 */
	public function numRows() {
		if (!isset($this->numRows) && is_resource($this->result)) {
			$this->numRows = mysql_num_rows($this->result);
		}
		return $this->numRows;
	}
	
}

?>