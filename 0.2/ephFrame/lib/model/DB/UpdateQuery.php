<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright (c) 2007+, Ephigenia M. Eichner
 * 						 Kopernikusstr. 8
 * 						 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 
 * 	@license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright		copyright 2007+, Ephigenia M. Eichner
 * 	@link			http://code.ephigenia.de/projects/ephFrame/
 * 	@version		$Revision$
 * 	@modifiedby		$LastChangedBy$
 * 	@lastmodified	$Date$
 * 	@filesource		$HeadURL$
 */

class_exists('DBQuery') or require dirname(__FILE__).'/DBQuery.php';

/**
 *  A Database UPDATE query
 * 
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.model.DB
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de
 * 	@since 09.03.2008
 * 	@version 0.2
 */
class UpdateQuery extends DBQuery {
	
	public $verb = 'UPDATE';
	
	public $renderTemplate = '%verb %flags %tables SET %values %where %orderBy %limit';
	
	public function __construct($table = null, $values = array(), $conditions = array()) {
		return parent::__construct($this->verb, $table, $conditions, $values);
	}
	
	public function update($tablename, $alias = null) {
		return parent::table($tablename, $alias);
	}
	
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class UpdateQueryException extends DBQueryException {}

?>