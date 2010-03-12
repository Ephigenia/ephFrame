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
 * @license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright	copyright 2007+, Ephigenia M. Eichner
 * @link		http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

// init simpletest and framework
require_once dirname(__FILE__).'/../../../autorun.php';

/**
 * Unittest for {@link SelectQuery}
 * 
 * This class should acid test the {@link SelectQuery} Class from the DAO.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 18.08.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 * @uses SelectQuery
 */
class TestSelectQuery extends UnitTestCase
{	
	public function setUp() 
	{
		ephFrame::loadClass('ephFrame.lib.model.DB.SelectQuery');
	}
	
	public function testSimple() 
	{
		$q = new SelectQuery();
		$q->autoNewLine = false;
		$q->from('testtable')->select('*');
		$this->assertEqual((string) $q, 'SELECT * FROM testtable');
	}
	
	public function testAutoAllColumns() 
	{
		$q = new SelectQuery();
		$q->autoNewLine = false;
		$q->from('testtable');
		$this->assertEqual((string) $q, 'SELECT FROM testtable');
	}	
}