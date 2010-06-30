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
 * Image Posterize Filter
 * 
 * Will convert all colors in an image to two colors depending on the luma value.
 * <code>
 * $imageFilter = new ImagePosterizeFilter();
 * $img = new Image('webroot/static/img/Blue_Box_in_museum.jpg');
 * $img->header();
 * $img->applyFilter($imageFilter);
 * echo $img->render(100);
 * </code>
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 28.12.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 */
class ImagePosterize extends ImageColorFilter implements ImageFilter 
{
	/**
	 * desired dark color
	 * @var integer
	 */
	public $black = 0;
	
	/**
	 * desired light color
	 * @var integer
	 */
	public $white = 16777215;
	
	/**
	 * treshold value for filtering
	 * @var float
	 */
	public $treshold = 0.5;
	
	private $cache = array();
	
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
		// backwards iteration seemes to be faster in php
		for ($x = $imgWidth; $x >= 0; $x--) {
			for ($y = $imgHeight; $y >= 0; $y--) {
				// imagecolorat returns the big int for the color we can 
				// use that for getting r,g,b values
				$rgb = imagecolorat($imgHandle, $x, $y);
				// reading and storing calculated color values in a cache
				// also increases performance
				if (isset($this->cache[$rgb])) {
					$newcol = $this->cache[$rgb];
				} else {
					$r = ($rgb >> 16) & 0xFF;
	                $g = ($rgb >> 8) & 0xFF;
	                $b = $rgb & 0xFF;
	                $hsv = Color::RGBtoHSV($r, $g, $b);
	                if ($hsv[2] > $this->treshold) {
	                	$newcol = $this->white;	
	                } else {
	                	$newcol = $this->black;
	                }
					$this->cache[$rgb] = $newcol;
				}
				imagesetpixel($imgHandle, $x, $y, $newcol);
			}
		}
		return $image;
	}	
}