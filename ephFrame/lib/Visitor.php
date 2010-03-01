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
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

/**
 * Visitable Pattern Interface
 * 
 * as described here <a href="http://en.wikipedia.org/wiki/Visitor_pattern">Visitor Pattern</a>
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 09.08.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 */
interface Visitor {
	
	public function visit();
	
}