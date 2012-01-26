<?php

namespace ephFrame\test\HTML\Form;

use
	ephFrame\HTML\Form\Fieldset
	;

/**
 * @group Form
 */
class FieldsetTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Fieldset();
	}
	
	public function testVisible()
	{
		$this->fixture->visible = false;
		$this->assertEquals('', (string) $this->fixture);
	}
}