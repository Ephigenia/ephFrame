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

require_once dirname(__FILE__).'/DBQuery.php';

/**
 * 	A SQL Query that inserts values into one table
 * 
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.model.DB
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de
 * 	@since 09.03.2008
 * 	@version 0.2
 */
class DeleteQuery extends DBQuery {
	
	public $verb = 'DELETE';
	
	public $renderTemplate = '%verb %flags FROM %tables %where %orderBy %limit';
	
	public function __construct($table, $conditions = array()) {
		parent::__construct($this->verb, $table, $conditions);
	}
	
}

?>