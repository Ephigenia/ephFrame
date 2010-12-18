<?php

namespace ephFrame\test\core;

use \ephFrame\core\Library;

class LibraryTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \ephFrame\core\LibraryPathNotFoundException
	 */
	public function testAddFail()
	{
		Library::add('\test_namespace\path', '/directory/that/does_not_exist');
	}
	
	public function testAdd()
	{
		$this->assertTrue(Library::add('\ephFrame\core', dirname(__FILE__).'/../../core/'));
	}
}