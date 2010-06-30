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

class_exists('DBQuery') or require dirname(__FILE__).'/DBQuery.php';

/**
 * A SQL Query that inserts values into one table
 * 
 * Simple Example:
 * <code>
 * // should render you 'INSERT INTO users VALUES('Ephigenia', 'love@ephigenia')
 * $insert = new InsertQuery('users');
 * $insert->value('username', 'Ephigenia');
 * $insert->value('email', 'love@ephigenia.de');
 * echo $insert;
 * </code>
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.model.DB
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 09.03.2008
 * @version 0.2
 */
class InsertQuery extends DBQuery
{	
	public $verb = 'INSERT';
	
	public $renderTemplate = '%verb %flags INTO %tables ( %keys ) VALUES ( %values )';
		
	public function __construct($table = null, $values = array(), $conditions = array())
	{
		return parent::__construct($this->verb, $table, $conditions, $values);
	}
	
	public function renderKeys()
	{
		$pairs = new IndexedArray();
		foreach($this->values->keys() as $key) {
			$pairs[] = '`'.str_replace('.', '`.`', $key).'`';
		}
		return $pairs->implode(', ');
	}
	
	public function renderValues()
	{
		return $this->values->values()->implode(', ');
	}
	
	public function into($tablename, $alias = null)
	{
		return parent::table($tablename, $alias);
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class InsertQueryException extends DBQueryException 
{}