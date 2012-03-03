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
	
	public function testConstructorConfigMerge()
	{
		$decorators = array('MyDecorator' => false);
		$fieldset = new Fieldset(array(), array(
			'visible' => false,
			'decorators' => $decorators,
		));
		$this->assertFalse($fieldset->visible);
		$this->assertEquals($decorators, $fieldset->decorators);
	}
	
	public function testVisible()
	{
		$this->fixture->visible = false;
		$this->assertEquals('', (string) $this->fixture);
	}
}