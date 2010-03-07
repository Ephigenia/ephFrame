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
 * @license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright	copyright 2007+, Ephigenia M. Eichner
 * @link		http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

/**
 * This is a ephFrame TestCase using the SimpleTest-PHP-Testing Suite.
 * If you want to test ephFrame on your machine you need to have SimpleTest
 * installed in /ephFrame/vendor/simpletest/.
 * 
 * This file should setup a hole framework wide test, testing components and
 * Helpers.
 * @package ephFrame
 * @subpackage ephFrame.test
 */

require_once dirname(__FILE__).'/autorun.php';

$helperTests = new GroupTest('ephFrame Framework Unit Test');

// ephFrame Helper Tests
$helperTests->addTestFile('lib/helper/TestString.php');
$helperTests->addTestFile('lib/helper/TestTime.php');
$helperTests->addTestFile('lib/helper/TestCharset.php');
$helperTests->addTestFile('lib/helper/TestHTML.php');
$helperTests->addTestFile('lib/helper/TestValidator.php');
$helperTests->addTestFile('lib/helper/TestSanitizer.php');

// ephFrame basic classes
$helperTests->addTestFile('lib/TestFile.php');
$helperTests->addTestFile('lib/TestIndexedArray.php');
$helperTests->addTestFile('lib/TestCSV.php');
$helperTests->addTestFile('lib/TestObjectSet.php');
$helperTests->addTestFile('lib/TestCollection.php');
$helperTests->addTestFile('lib/TestHash.php');
$helperTests->addTestFile('lib/TestHTMLTag.php');
$helperTests->addTestFile('lib/TestSGMLBeautifier.php');
$helperTests->addTestFile('lib/TestDir.php');
$helperTests->addTestFile('lib/TestSGMLAttributes.php');
$helperTests->addTestFile('lib/TestSocket.php');
$helperTests->addTestFile('lib/TestInflector.php');
// test image class only on http server enviorenment
if (isset($_SERVER['HTTP_HOST'])) {
	$helperTests->addTestFile('lib/TestImage.php');
}

// test components
$helperTests->addTestFile('lib/component/TestSearchQueryParser.php');
$helperTests->addTestFile('lib/component/TestWikiText.php');
$helperTests->addTestFile('lib/component/TestLog.php');

// DAO Tests
$helperTests->addTestFile('lib/model/DB/TestSelectQuery.php');
$helperTests->run($reporter);