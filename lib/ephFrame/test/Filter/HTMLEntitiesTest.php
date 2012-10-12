<?php

namespace ephFrame\test\Filter;

use \ephFrame\Filter\HTMLEntities;

/**
 * @group Filter
 */
class HTMLEntitiesTest extends \PHPUnit_Framework_TestCase 
{
	public function setUp()
	{
		$this->fixture = new HTMLEntities();
	}
	
	public function testSimpleValues()
	{
		return array(
			array('<b>Test</b>', '&lt;b&gt;Test&lt;/b&gt;'),
			array('<b class="test>">Test & Deploy</b>', '&lt;b class=&quot;test&gt;&quot;&gt;Test &amp; Deploy&lt;/b&gt;'),
			array('line'.PHP_EOL.'break', 'line'.PHP_EOL.'break'),
		);
	}
	
	/**
	 * @dataProvider testSimpleValues()
	 */
	public function testSimple($left, $right)
	{
		$this->assertEquals($right, $this->fixture->apply($left));
	}
	
	public function testUnicodeCharacters()
	{
		$this->assertEquals('@ö€', $this->fixture->apply('@ö€'));
	}
}