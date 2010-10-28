<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Brunnenstr. 10
 *                      10119 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright	copyright 2007+, Ephigenia M. Eichner
 * @link		http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

// init simpletest and framework
require_once dirname(__FILE__).'/../../autorun.php';

/**
 * [SOME DOCU HERE WOULD BE NICE HEE!?]
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 18.08.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class WikiTextTest extends UnitTestCase 
{
	/**
	 * @var WikiText
	 */
	protected $wikiText;
	
	public function setUp() 
	{
		Library::load('ephFrame.lib.component.WikiText');
		$this->wikiText = new WikiText();
	}
	
	public function testBold() 
	{
		$this->assertEqual($this->wikiText->format("'''test'''"), '<strong>test</strong>');	
	}
	
	public function testHeadline() 
	{
		$this->assertEqual($this->wikiText->format('= test ='), '<h1>test</h1>');
		$this->assertEqual($this->wikiText->format('== test =='), '<h2>test</h2>');
		$this->assertEqual($this->wikiText->format('=== test ==='), '<h3>test</h3>');
		$this->assertEqual($this->wikiText->format('==== test ===='), '<h4>test</h4>');
		$this->assertEqual($this->wikiText->format('===== test ====='), '<h5>test</h5>');
		$this->assertEqual($this->wikiText->format('====== test ======'), '<h6>test</h6>');
		$this->assertEqual($this->wikiText->format('======= test ======='), '<h6>test</h6>');
	}
	
	public function testItalic() 
	{
		$this->assertEqual($this->wikiText->format("''italic''"), '<i>italic</i>');
	}
	
	public function testLink() 
	{
		$this->assertEqual($this->wikiText->format('[www.ephigenia.de]'), '<a href="www.ephigenia.de">www.ephigenia.de</a>');
		$this->assertEqual($this->wikiText->format('[www.ephigenia.de ephigenia]'), '<a href="www.ephigenia.de" title="ephigenia">ephigenia</a>');
	}
	
	public function testHr() 
	{
		$this->assertEqual($this->wikiText->format('----'), '<hr />');
	}
}