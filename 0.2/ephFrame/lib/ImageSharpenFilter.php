<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright 2007+, Ephigenia M. Eichner, Kopernikusstr. 8, 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 	@license http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright Copyright 2007+, Ephigenia M. Eichner
 * 	@link http://code.ephigenia.de/projects/ephFrame/
 * 	@filesource
 */

// load parent class
interface_exists('ImageFilter') or require dirname(__FILE__).'/ImageFilter.php';

/**
 *	Example Image Sharpen Filter
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 27.04.2009
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class ImageSharpenFilter extends Object implements ImageFilter {

	public $cache = array();
	
	public $sharpness = 0;
	
	public function __construct($sharpness = null) {
		if ($sharpness !== null) {
			$this->sharpness = (float) $sharpness;
		}
	}
	
	/**
	 *	Runs the filter on a {@link Image}
	 * 	@param Image $image
	 * 	@return Image the manipulated image
	 */
	public function apply(Image $image) {
		$matrix = array(
			array(-1, -2, -1),
			array(-2, $this->sharpness + 12, -2),
			array(-1, -2, -1)
		);
		$divisor	 = $this->sharpness;
		$offset	= 0;
		imageconvolution($image->handle(), $matrix, $divisor, $offset);
		return $image;
	}
	
}

?>