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
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

/**
 * Interface for components
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 04.10.2008
 * @package ephFrame
 * @subpackage ephFrame.lib
 */
interface ephFrameComponent
{
	public function startup();
	
	public function init(Controller $controller);
	
	public function beforeRender();
	
	public function beforeAction();
}