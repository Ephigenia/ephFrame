<?php

namespace ephFrame\test\Form\Element;

use ephFrame\HTML\Form\Element\Element;

/**
 * @group Element
 */
class ElementTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new Element('name', 'default');
	}
	
	public function testRequiredWithEmptyData()
	{
		$this->assertFalse($this->fixture->submit(Null)->ok());
	}
	
	public function testRequiredWithValue()
	{
		$this->fixture->required = false;
		$this->assertTrue($this->fixture->submit('value')->ok());
	}
	
	public function testTrim()
	{
		$this->assertEquals($this->fixture->submit('  trim me ')->data, 'trim me');
	}
	
	public function testStripTags()
	{
		$this->assertEquals(
			$this->fixture->submit('<em><strong>strong</strong> value</em>')->data,
			'strong value'
		);
	}
}