<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

// init simpletest and framework
require_once dirname(__FILE__).'/../autorun.php';

/**
 * [SOME DOCU HERE WOULD BE NICE HEE!?]
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 18.08.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestSocket extends UnitTestCase 
{
	/**
	 * @var Socket
	 */
	public $socket;
	
	public function setUp() 
	{
		loadClass('ephFrame.lib.Socket');
		$this->socket = new Socket('localhost', 80);
		$this->assertTrue($this->socket->connect());
	}
	
	public function testConnect() 
	{
		$this->assertTrue($this->socket->connected());
	}
	
	public function testSend() 
	{
		$this->socket->write('GET /'.LF.LF);
		$this->assertTrue(strlen($this->socket->read()) > 0);
	}	
}