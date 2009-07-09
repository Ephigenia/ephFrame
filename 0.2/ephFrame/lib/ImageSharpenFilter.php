<?php

/**
 * ephFrame: <http://code.moresleep.net/project/ephFrame/>
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

// load interface for filter class
interface_exists('ImageFilter') or require dirname(__FILE__).'/ImageFilter.php';

/**
 * Example Image Sharpen Filter
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 27.04.2009
 * @package ephFrame
 * @subpackage ephFrame.lib
 */
class ImageSharpenFilter extends Object implements ImageFilter {

	public $cache = array();
	
	public $sharpness = 16;
	
	public $divisor = 8;
	
	public $offset = 0;
	
	public function __construct($sharpness = null) {
		if ($sharpness !== null) {
			$this->sharpness = (float) $sharpness;
		}
	}
	
	/**
	 * Runs the filter on a {@link Image}
	 * @param Image $image
	 * @return Image the manipulated image
	 */
	public function apply(Image $image) {
		$s = $this->sharpness;
		$matrix = array(
			array(-1, -1, -1),
			array(-1, $s, -1),
			array(-1, -1, -1)
		);
		imageconvolution($image->handle(), $matrix, $this->divisor, $this->offset);
		return $image;
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ImageSharpenFilterException extends BasicException {}

//http://www.weberdev.com/get_example-4602.html
if(!function_exists('imageconvolution')){
function imageconvolution($src, $filter, $filter_div, $offset){
    if ($src==NULL) {
        return 0;
    }
    
    $sx = imagesx($src);
    $sy = imagesy($src);
    $srcback = ImageCreateTrueColor ($sx, $sy);
    ImageCopy($srcback, $src,0,0,0,0,$sx,$sy);
    
    if($srcback==NULL){
        return 0;
    }
    $pxl = array(1,1);
    for ($y=0; $y<$sy; ++$y){
        for($x=0; $x<$sx; ++$x){
            $new_r = $new_g = $new_b = 0;
            $alpha = imagecolorat($srcback, $pxl[0], $pxl[1]);
            $new_a = $alpha >> 24;
            
            for ($j=0; $j<3; ++$j) {
                $yv = min(max($y - 1 + $j, 0), $sy - 1);
                for ($i=0; $i<3; ++$i) {
                        $pxl = array(min(max($x - 1 + $i, 0), $sx - 1), $yv);
                    $rgb = imagecolorat($srcback, $pxl[0], $pxl[1]);
                    $new_r += (($rgb >> 16) & 0xFF) * $filter[$j][$i];
                    $new_g += (($rgb >> 8) & 0xFF) * $filter[$j][$i];
                    $new_b += ($rgb & 0xFF) * $filter[$j][$i];
                }
            }

            $new_r = ($new_r/$filter_div)+$offset;
            $new_g = ($new_g/$filter_div)+$offset;
            $new_b = ($new_b/$filter_div)+$offset;

            $new_r = ($new_r > 255)? 255 : (($new_r < 0)? 0:$new_r);
            $new_g = ($new_g > 255)? 255 : (($new_g < 0)? 0:$new_g);
            $new_b = ($new_b > 255)? 255 : (($new_b < 0)? 0:$new_b);

            $new_pxl = ImageColorAllocateAlpha($src, (int)$new_r, (int)$new_g, (int)$new_b, $new_a);
            if ($new_pxl == -1) {
                $new_pxl = ImageColorClosestAlpha($src, (int)$new_r, (int)$new_g, (int)$new_b, $new_a);
            }
            if (($y >= 0) && ($y < $sy)) {
                imagesetpixel($src, $x, $y, $new_pxl);
            }
        }
    }
    imagedestroy($srcback);
    return 1;
}
}