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

/**
 * Interface for all classes that can be rendered (send to /out)
 * 
 * All classes that have content that can be rendered as text should implement
 * this class. Some for having a better debugging output or for the client
 * display.
 * 
 * The interface supports two callbacks {@link beforeRender} and {@link afterRender}
 * than can implement some cool stuff that makes every class that implements
 * this interface more agile.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 05.06.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 */
interface Renderable 
{
	public function render();
	
	public function beforeRender();
	
	public function afterRender($rendered);

}