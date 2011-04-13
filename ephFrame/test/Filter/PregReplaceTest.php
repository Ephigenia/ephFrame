<?php

namespace ephFrame\test\Filter;

use \ephFrame\Filter\PregReplace;

class PregReplaceTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new PregReplace();
	}
	
	/**
	 * @expectedException \PHPUnit_Framework_Error_Warning
	 */
	public function testPregCompileFail()
	{
		$this->fixture->regexp = 'invalid';
		$this->fixture->apply('testtext');
	}
}