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

ephFrame::loadClass('ephFrame.lib.model.DB.DBQuery');

/**
 * 	Database Select Query
 * 
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 *	@since 19.05.2007
 *	@package ephFrame
 *	@subpackage ephFrame.lib.model.DB
 * 	@version 0.2
 */
class SelectQuery extends DBQuery {
	
	public $verb = 'SELECT';
	
	public function __construct() {
		return parent::__construct();
	}
	
	public function from($tablename, $alias = null) {
		return parent::table($tablename, $alias);
	}

	/**
	 *	Add a new field (with alias) to select query
	 * 	<code>
	 * 	// select id, created
	 * 	$query->select(array('id', 'created'));
	 * 	// select id but with the alias User.id
	 * 	$query->select('id', 'User.id');
	 * 	</code>
	 * 	@param string|array(string) Single Select name or multiple
	 * 	@return DBQuery
	 */
	public function select($fieldname, $alias = null) {
		if (func_num_args() == 0) return $this->select;
		if (is_array($fieldname)) {
			foreach($fieldname as $v) {
				$this->select($v);
			}
		} else {
			$this->select->add(trim($fieldname), trim($alias));
		}
		return $this;
	}
	
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class SelectQueryException extends DBQueryException {}


?>