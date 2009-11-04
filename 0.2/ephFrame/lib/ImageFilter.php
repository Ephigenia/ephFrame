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
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

/**
 * Interface for ImageFilter
 * 
 * This interface is used by some for now testing image filter classes that
 * can be applied to an {@link Image} class using the {@link apply} method
 * to the image filter object.
 * Some examples are the {@link ImageBWFilter}, {@link ImageGreyFilter}, {@link ImageNegativeFilter}
 * that are also part of the current ephFrame release. You can advance the filters
 * by implementing this interface. All ImageFilter Classes should interface THIS! \m/ year!
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 13.10.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 */
interface ImageFilter {
	
	function apply(Image $image);
	
}