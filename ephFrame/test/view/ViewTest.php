<?php

namespace ephFrame\test\view;

use ephFrame\view\View;

class ViewTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->fixture = new View();
	}
	
	/**
	 * @expectedException \ephFrame\view\TemplateNotFoundException
	 */
	public function testTemplateNotFound()
	{
		$this->fixture->render('view', 'index');
	}
	
	public function testRender()
	{
		$this->fixture->rootPath = dirname(__FILE__).'/';
		$this->fixture->theme = false;
		$this->assertEquals(
			$this->fixture->render('view', 'fixtures/template', array('username' => 'Marcel')),
			'Hello my name is: Marcel!'
		);
	}
}