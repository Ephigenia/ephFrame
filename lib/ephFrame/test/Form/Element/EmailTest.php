<?php

namespace ephFrame\test\Form\Element;

use ephFrame\HTML\Form\Element\Email;

/**
 * @group Element
 */
class EmailTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new Email('email', 'default', array());
	}
	
	public function testValid()
	{
		$this->assertTrue($this->fixture->submit('love@ephigenia.de')->ok());
	}
	
	public function testInvalid()
	{
		$this->assertFalse($this->fixture->submit(' hoho')->ok());
	}
	
	public function testNotRequired()
	{
		$this->fixture->required = false;
		$this->assertTrue($this->fixture->submit('')->ok());
	}
}