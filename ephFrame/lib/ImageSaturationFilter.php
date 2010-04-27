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

// load parent class
interface_exists('ImageFilter') or require dirname(__FILE__).'/ImageFilter.php';
class_exists('ImageColorFilter') or require dirname(__FILE__).'/ImageColorFilter.php';

/**
 * Saturation Image Filter
 * 
 * This class converts all colors in an image to grey values by reducing
 * the hue value to 0.<br />
 * <br />
 * This is kinda slow, I tried to improve performance in counting backwards
 * on the x and y values of pixels and storing the calculated color values
 * in a cache.<br />
 * <br />
 * <code>
 * $imageFilter = new ImageGreyFilter();
 * $img = new Image('webroot/static/img/Blue_Box_in_museum.jpg');
 * $img->header();
 * $img->applyFilter($imageFilter);
 * echo $img->render(100);
 * </code>
 * 
 * @todo add adjustment filter for saturation strength
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 13.10.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 */
class ImageSaturationFilter extends ImageColorFilter implements ImageFilter 
{
	/**
	 * Runs the filter on a {@link Image}
	 * @param Image $image
	 * @return Image the manipulated image
	 */
	public function apply(Image $image) 
	{
		$imgWidth = $image->width() - 1;
		$imgHeight = $image->height() - 1;
		$imgHandle = $image->handle();
		$cache = array();
		// backwards iteration seemes to be faster in php
		for ($x = $imgWidth; $x >= 0; $x--) {
			for ($y = $imgHeight; $y >= 0; $y--) {
				// imagecolorat returns the big int for the color we can 
				// use that for getting r,g,b values
				$rgb = imagecolorat($imgHandle, $x, $y);
				// reading and storing calculated color values in a cache
				// also increases performance
				if (isset($cache[$rgb])) {
					$grey = $cache[$rgb];
				} else {
					$r = ($rgb >> 16) & 0xFF;
	                $g = ($rgb >> 8) & 0xFF;
	                $b = $rgb & 0xFF;
					$c = round(($r + $g + $b) / 3);
					$grey = $cache[$rgb] = ($c << 16) + ($c << 8) + $c;
				}
				imagesetpixel($imgHandle, $x, $y, $grey);
			}
		}
		return $image;
	}	
}