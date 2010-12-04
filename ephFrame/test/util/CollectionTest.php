<?php

namespace ephFrame\test\util;

use ephFrame\util\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$r = new Collection(array(1, 2, 3, 4, 5));
		$this->assertEquals((array) $r, array(1, 2, 3, 4, 5));
		$r = new Collection(array(1, 2, 3, 4, 5, 5));
		$this->assertEquals((array) $r, array(1, 2, 3, 4, 5));
	}
	
	public function testOffsetSet()
	{
		$r = new Collection(array(1, 2, 3, 4, 5, 5));
		$r[] = 5;
		$this->assertEquals((array) $r, array(1, 2, 3, 4, 5));
	}
}