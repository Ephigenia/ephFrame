<?php

namespace ephFrame\test\core;

use ephFrame\core\CallbackHandler;

/**
 * @group core
 */
class CallbackHandlerTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new CallbackHandler();
		$this->fixture->add('afterTest', array($this, 'callbackAfterTest'));
		$this->fixture->add('afterTest2', array($this, 'callbackAfterTestArguments'));
		$this->fixture->add('afterTest3', array($this, 'callbackWithReturnValue'));
	}
	
	public function testCall()
	{
		$this->fixture->call('afterTest');
		$this->assertTrue($this->called);
	}
	
	public function callbackAfterTest()
	{
		$this->called = true;
	}
	
	public function testCallWithArguments()
	{
		$this->fixture->call('afterTest2', array('arg1', 'arg2'));
		$this->assertEquals($this->arg1, 'arg1');
		$this->assertEquals($this->arg2, 'arg2');
	}
	
	public function callbackAfterTestArguments($arg1, $arg2)
	{
		$this->arg1 = $arg1;
		$this->arg2 = $arg2;
	}
	
	public function testCallWithReturnValue()
	{
		$return = $this->fixture->call('afterTest3', array('return value'));
		$this->assertEquals($return, 'return value');
	}
	
	public function callbackWithReturnValue($arg1)
	{
		return $arg1;
	}
	
	public function testNonExistingCallback()
	{
		$this->assertTrue($this->fixture->call('non_existing_callback_name'));
	}
}