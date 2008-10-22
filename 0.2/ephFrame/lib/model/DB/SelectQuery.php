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
	
	public function beforeRender() {
		if (count($this->select) == 0) {
			$this->select->add('*');
		}
		return parent::beforeRender();
	}
	
	public function from($tablename, $alias = null) {
		return parent::table($tablename, $alias);
	}
	
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class SelectQueryException extends DBQueryException {}


?>