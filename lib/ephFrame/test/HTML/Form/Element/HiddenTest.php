<?php

namespace ephFrame\test\HTML\Form\Element;

use ephFrame\HTML\Form\Element\Hidden;

/**
 * @group Element
 * @group Hidden
 */
class HiddenTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Hidden('isEnabled', 'yes', array());
	}
	
	public function testToString()
	{
		$rendered = (string) $this->fixture;
		$this->assertEquals('<input type="hidden" name="isEnabled" value="yes" />', $rendered);
	}
}