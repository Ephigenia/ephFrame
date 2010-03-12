<?php

class_exists('ConsoleWindow') or require dirname(__FILE__).'/ConsoleWindow.php';

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

/**
 * Status bar in the console
 * @author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * @since 25.04.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.console
 */
class ConsoleStatusBar extends ConsoleWindow
{	
	public $drawBorder = false;
	
	public function __construct($x, $y, $width, $background) 
	{
		return parent::__construct($x, $y, $width, 1, null, null, $background);
	}
}