<?php

namespace ephFrame\test\view;

use ephFrame\view\Renderer;

class RendererTest extends \PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->renderer = new Renderer();
	}
	
	/**
	 * @expectedException \ephFrame\view\TemplateNotFoundException
	 */
	public function testTemplateNotFound()
	{
		$this->renderer->render('template that can not be found', array('crap' => 'data'));
	}
	
	public function testRender()
	{
		$result = $this->renderer->render(
			__DIR__.'/../fixtures/view/template.html',
			array('username' => 'Karl')
		);
		$this->assertEquals($result, 'Hello my name is: Karl!');
	}
}