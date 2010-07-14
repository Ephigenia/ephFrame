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
require_once dirname(__FILE__).'/../autorun.php';

/**
 * [SOME DOCU HERE WOULD BE NICE HEE!?]
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 18.08.2008
 * @package ephFrame
 * @subpackage ephFrame.test
 */
class TestFile extends UnitTestCase
{	
	/**
	 * @var File
	 */
	protected $testFile;
	
	/**
	 * @var File
	 */
	protected $falseFile;
	
	public function setUp() 
	{
		ephFrame::loadClass('ephFrame.lib.File');
		$this->testFile = new File(__FILE__);
		$this->falseFile = new File('nixda');
	}
	
	public function testReadLine() 
	{
		$file = new File(dirname(__FILE__).'/../tmp/textfile.txt');
		while($line = $file->read(true)) {
			$this->assertTrue(is_string($line));
		}
		$this->assertTrue(count($file->toArray()), 4);
	}
	
	public function testExt() 
	{
		$this->assertEqual(File::ext('Simplefilename.ext'), 'ext');
		$this->assertEqual(File::ext('Simplefilename.ext  '), 'ext');
		$this->assertEqual(File::ext('Simplefilename.double.ext  '), 'ext');
		$this->assertEqual(File::ext('.ext  '), 'ext');
		$this->assertEqual(File::ext('Mültibäite.ext  '), 'ext');
		$this->assertEqual(File::ext('Mültibäite.hä?  '), 'hä?');
		$this->assertEqual(File::ext('Simplefilename...ext'), 'ext');
		// uppercase extensions
		$this->assertEqual(File::ext('Simplefilename...eXt'), 'ext');
		$this->assertEqual(File::ext('Simplefilename...eXt', false), 'eXt');
		// test empty extensions
		$this->assertEqual(File::ext('S'), '');
	}
	
	public function testSizeHumanized() 
	{
		$this->assertEqual(File::sizeHumanized(1), '1 B');
		$this->assertEqual(File::sizeHumanized(KILOBYTE - 1), '1023 B');
		// KB
		$this->assertEqual(File::sizeHumanized(KILOBYTE), '1 KB');
		$this->assertEqual(File::sizeHumanized(KILOBYTE + 1), '1 KB');
		$this->assertEqual(File::sizeHumanized(1024 * 1023 - 1), '1023 KB');
		$this->assertEqual(File::sizeHumanized(1024 * 1023 - 1, 3), '1022.999 KB');
		$this->assertEqual(File::sizeHumanized(MEGABYTE - 1), '1 MB');
		// MB
		$this->assertEqual(File::sizeHumanized(MEGABYTE), '1 MB');
		$this->assertEqual(File::sizeHumanized(MEGABYTE + 1), '1 MB');
		$this->assertEqual(File::sizeHumanized(GIGABYTE - 1), '1 GB');
		// GB
		$this->assertEqual(File::sizeHumanized(GIGABYTE), '1 GB');
		$this->assertEqual(File::sizeHumanized(GIGABYTE + 1), '1 GB');
	}
	
	public function testIsFile() 
	{
		$this->assertTrue($this->testFile->isFile());
	}
	
	public function testIsDir() 
	{
		$this->assertFalse($this->testFile->isDir());
	}
	
	public function testMIME() 
	{
		$this->assertEqual($this->testFile->mimeType(), 'application/x-httpd-php');
	}
	
	public function testExtension() 
	{
		$this->assertEqual($this->testFile->extension(), 'php');
		$this->assertFalse($this->falseFile->extension());
	}
	
	public function testSize() 
	{
		$this->assertTrue(is_int($this->testFile->size()));
		$this->assertTrue($this->testFile->size() > 100);
	}
	
	public function testExists() 
	{
		$this->assertTrue($this->testFile->exists());
		$this->assertFalse($this->falseFile->exists());
	}
	
	public function testLastModified() 
	{
		$this->assertTrue(is_int($this->testFile->lastModified()));
		$this->assertTrue($this->testFile->lastModified() > 0);
	}
	
	public function testCreated() 
	{
		$this->assertTrue(is_int($this->testFile->created()));
		$this->assertTrue($this->testFile->created() > 0);
	}
		
}