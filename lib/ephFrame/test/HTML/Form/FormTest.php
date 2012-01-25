<?php

namespace ephFrame\test\HTML\Form;

use ephFrame\HTML\Form\Form;

use ephFrame\HTML\Form\Element\Text;

/**
 * @group Form
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new Form(array('action' => 'myaction'));
	}
	
	public function test__toString()
	{
		$this->assertEquals(
			(string) $this->fixture,
			'<form method="post" accept-charset="utf-8" action="myaction"><fieldset /></form>'
		);
	}
	
	public function testOffsetSet()
	{
		$element = new Text('field1', 'value');
	}
}