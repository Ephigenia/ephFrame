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
 * 	Simple Example:
 * 	<code>
 * 	// should render you 'INSERT INTO users VALUES('Ephigenia', 'love@ephigenia')
 * 	$insert = new InsertQuery('users');
 * 	$insert->value('username', 'Ephigenia');
 * 	$insert->value('email', 'love@ephigenia.de');
 * 	echo $insert;
 * 	</code>
 * 
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.model.DB
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de
 * 	@since 09.03.2008
 * 	@version 0.2
 */
class InsertQuery extends DBQuery {
	
	public $verb = 'INSERT';
	
	public $renderTemplate = '%verb %flags INTO %tables ( %keys ) VALUES ( %values )';
		
	public function __construct($table = null, $values = array(), $conditions = array()) {
		return parent::__construct($this->verb, $table, $conditions, $values);
	}
	
	public function renderKeys() {
		$rendered = '';
		foreach($this->values->keys() as $key) {
			$rendered .= $key.', ';
		}
		return substr($rendered, 0, -2);
	}
	
	public function renderValues() {
		$rendered = '';
		foreach($this->values->values() as $value) {
			$rendered .= $value.', ';
		}
		return substr($rendered, 0, -2);
	}
	
	public function into($tablename, $alias = null) {
		return parent::table($tablename, $alias);
	}
	
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class InsertQueryException extends DBQueryException {}

?>