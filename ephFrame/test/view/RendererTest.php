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
			dirname(__FILE__).'/fixtures/template.html.php',
			array('username' => 'Karl', 'template' => 'test')
		);
		$this->assertEquals($result, 'Hello my name is: Karl!');
	}
}