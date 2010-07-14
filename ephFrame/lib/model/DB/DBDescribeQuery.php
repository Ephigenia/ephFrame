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

ephFrame::loadClass('ephFrame.lib.model.DB.DBQuery');

/**
 * Query that describes something from a database
 * @package ephFrame
 * @subpackage ephFrame.lib.model.DB
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 26.09.2008
 */
class DBDescribeQuery extends DBQuery 
{
	public $renderTemplate = '%verb %tables';
	
	public function __construct($tablename) 
	{
		parent::__construct('DESCRIBE');
		$this->table($tablename);
		return $this;
	}	
}