<?php

namespace ephFrame\test\HTML\Form\Decorator;

use
	ephFrame\HTML\Form\Decorator\Label,
	ephFrame\HTML\Form\Element\Text,
	ephFrame\HTML\Form\Form
	;

/**
 * @group Form
 * @group FormDecorator
 */
class LegendTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Form();
		$this->fixture->fieldsets[0][] = new Text('field', 'value', array('decorators' => false));
		$this->fixture->fieldsets[0]->legend = 'My Legend';
	}
	
	public function testDefaultRender()
	{
		$this->assertEquals(
			'<form method="post" accept-charset="utf-8">'.
				'<fieldset>'.
					'<legend>My Legend</legend>'.
					'<input type="text" maxlength="255" name="field" />'.
				'</fieldset>'.
			'</form>',
			(string) $this->fixture);
	}
}