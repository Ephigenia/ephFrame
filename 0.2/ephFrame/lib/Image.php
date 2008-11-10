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

require_once dirname(__FILE__).'/File.php';
require_once dirname(__FILE__).'/Color.php';

/**
 *	Image Class
 * 
 * 	This class is an extension of the {@link File} class and can be handled
 * 	for every image file and can also be saved like the {@link File} classes
 * 	can.<br />
 * 	<br />
 * 	I tried to implement base features from the GD Lib in this class, that's
 * 	why this class will throw an exception if the GD Lib was not found in the
 * 	loaded extensions.<br />
 * 	<br />
 * 	If some GD lib functions are not implemented in this class you can use
 * 	the {@link handle} method for manipulating the image with gd lib image
 * 	functions.<br />
 * 	<br />
 * 	The most cool function is the {@link stretchResizeTo} method which
 * 	resizes the image to the given format by resizing the image and cutting
 * 	all overlapping image stuff. Just like flickr does with the small
 * 	preview squares. Put in a very large image - get a 60x60 thumbnail and
 * 	you'll have the hole image on that thumbnail.<br />
 *	<br />
 *	Sample use to create a thumbnail:
 *	<code>
 *		$image = new Image("bigimage.jpg");
 *		// resize to size, using constrain proportions
 *		$image->resizeTo(100,100);
 *		$image->saveAs("small.jpg");
 *	</code>
 *	<br />
 * 
 * 	Making flickr-like thumbnails (resize but so exact size)
 * 	<code>
 * 		$image = new Image("test.jpg");
 * 		$thumb = $image->stretchResizeTo(200,200);
 * 		$thumb->saveAs("thumbnail.jpg");
 * 	</code>
 *
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 03.05.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@version 0.2
 *	@uses File
 * 	@uses Color
 */
class Image extends File implements Renderable {

	/**
	 *	@var ressource
	 */
	private $handle;
	
	/**
	 *	@var integer
	 */
	private $height = 0;
	
	/**
	 * 	@var integer
	 */
	private $width = 0;
	
	/**
	 *	@var integer
	 */
	private $type = self::TYPE_GIF;
	
	/**
	 *	Stores colors used in this image
	 * 	@param array()
	 */
	private $colors = array ();
	
	const TYPE_GIF = 1;
	const TYPE_JPG = 2;
	const TYPE_PNG = 3;
	const TYPE_SWF = 4;
	
	private $imageTypes = array(
		'none',
		self::TYPE_GIF => 'gif',
		self::TYPE_JPG => 'jpg',
		self::TYPE_PNG => 'png',
		self::TYPE_SWF => 'swf'
	);
	
	/**
	 *	Size uses by the text drawing options
	 * 	@param integer
	 */
	private $textSize = 2;
	
	/**
	 * 	Caches the ouput of getImageInfo for more speed
	 *	@var array()
	 */
	private $imgInfo = array ();

	/**
	 *	Image Constructor
	 * 
	 * 	Creates a new Image Instance for an existing file or creates a new Image
	 * 	with the given resolution. You also can pass a allready created image
	 * 	ressource id.<br />
	 * 	<br />
	 * 	Three ways of creating an image instance:
	 * 	<code>
	 * 		$img = new Image("img.jpg");
	 * 		$img = new Image(100,200);
	 * 		$imgRessource = imagecreatefromgif("user.gif");
	 * 		$img = new Image($imgRessource);
	 * 	</code>
	 *	<br />
	 * 	Throws a {@link ImageGDLibNotFoundException} if no GD Lib Functions where
	 * 	found on the server. Imagemagick support is planned but not yet implemented
	 * 
	 *	@param string|integer|ressource	$filenameWithOrHandle Filename of the Image
	 * 	@param integer	$height
	 * 	@return Image
	 */
	public function __construct($filenameWidthOrHandle = null, $height = null) {
		// first check for gd lib installed
		if (!$this->phpHasGDLib()) throw new ImageGDLibNotFoundException();
		// create from ressource
		if (is_resource($filenameWidthOrHandle) && get_resource_type($filenameWidthOrHandle) == 'image') {
			$this->handle = $filename;
		// create from filename // if file not found throw an exception
		} elseif (is_string($filenameWidthOrHandle)) {
			parent::__construct($filenameWidthOrHandle);
			$this->checkExistence();
			//if (!$this->exists()) throw new FileNotFoundException($this);
		} elseif (is_int($filenameWidthOrHandle) && is_int($height)) {
			assert($filenameWidthOrHandle > 0 && $height > 0);
			$this->width = $filenameWidthOrHandle;
			$this->height = $height;
			$this->createHandle();
		}
		return $this;
	}
	
	/**
	 *	Applies a image filter to the image, see {@link ImageFilter}.
	 * 	@param $filter ImageFilter
	 * 	@return Image
	 */
	public function applyFilter(ImageFilter $filter) {
		$filter->apply(&$this);
		return $this;
	}
	
	/**
	 *	Sets a background Color
	 * 	@param Color|array $color
	 * 	@return Image
	 */
	public function backgroundColor($color) {
		return $this->rectangle(0, 0, $this->width, $this->height, null, $color);
	}
	
	/**
	 *	Draws a rectangle into the image
	 * 
	 * 	<code>
	 * 	$img->rectangle(100,100,200,250,null,'ff0000');
	 * 	</code>
	 * 
	 * 	@param integer $x offset start drawing rectangle
	 * 	@param integer $y offset start drawing rectangle
	 * 	@param integer $x2
	 * 	@param integer $y2
	 *  @param string $borderColor
	 *  @param string $backgroundColor
	 * 	@return Image
	 */
	public function rectangle($x1, $y1, $x2, $y2, $borderColor = null, $backgroundColor = null) {
		if ($backgroundColor !== null) {
			$backgroundColor = $this->createColor($backgroundColor);
			imagefilledrectangle($this->handle(), $x1, $y1, $x2, $y2, $backgroundColor->handle());
		}
		if ($borderColor !== null) {
			$borderColor = $this->createColor($borderColor);
			imagerectangle($this->handle(), $x1, $y1, $x2, $y2, $borderColor->handle());
		}
		return $this;
	}
	
	/**
	 * 	Alias for {@link rectangle}
	 * 	@return Image
	 */
	public function rect($x1, $y1, $x2, $y2, $borderColor = null, $backgroundColor = null) {
		return $this->rectangle($x1, $y1, $x2, $y2, $borderColor = null, $backgroundColor = null);
	}
	
	/**
	 *	Draws a line
	 * 	@param integer $x1
	 * 	@param integer $y1
	 * 	@param integer $x2
	 * 	@param integer $y2
	 * 	@param array|Color $color
	 */
	public function line($x1, $y1, $x2, $y2, $color, $thickness = 1) {
		if (func_num_args() == 6) $this->setThickness($thickness);
		if (!is_integer($color)) {
			$color = $this->createColor($color);
			$color = $color->handle();
		}
		imageline($this->handle(), $x1, $y1, $x2, $y2, $color);
		$this->resetThickness();
		return $this;
	}
	
	/**
	 * 	@param integer $thickness
	 *	@return Image
	 */
	private function setThickness($thickness = 1) {
		assert(is_numeric($thickness));
		if ($thickness > 1) {
			imagesetthickness($this->handle(), $thickness);
		}
		return $this;
	}
	
	/**
	 *	@return Image
	 */
	private function resetThickness() {
		imagesetthickness($this->handle(), 1);
		return $this;
	}
	
	/**
	 *	Sets a pixel of the image to the given color
	 * 	@param integer $x
	 * 	@param integer $y
	 * 	@param array|integer $color
	 * 	@return Image 
	 */
	public function setPixel($x, $y, $color) {
		$color = $this->createColor($color);
		imagesetpixel($this->handle(), $x, $y, $color->handle());
		return $this;
	}
	
	/**
	 *	Returns the color at a specific position in the image as rgb array
	 * 	@param integer $x
	 * 	@param integer $y
	 * 	@return Color
	 */
	public function getPixel($x, $y) {
		$col = imagecolorat($this->handle(), $x, $y);
		if (!$col) return 0;
		return array(($col >> 16) & 0xFF, ($col >> 8) & 0xFF, $col & 0xFF);
	}
	
	/**
	 *	Draws a sprite into the image starting on the upper left corner
	 * 	of the sprite. This works with 1/0 sprites and multicolor sprites.<br />
	 * 	<br />
	 * 	This method maybe should be transformed into a filter class, because
	 * 	the parameters and input properties are very vary. But for first we
	 * 	make it easy this way.<br />
	 * 	<br />
	 * 	1/0 sprite:
	 * 	<code>
	 * 	$hackerEmblem = json_decode('[
	 *		[0,1,0],
	 *		[0,0,1],
	 *		[1,1,1]
	 *		]');
	 *  // draw the sprite in yellow, with invertion and scale it 500%
	 *  $img->drawSprite(130, 50, $hackerEmblem, 'FFFF00', true, 5);
	 * 	</code>
	 * 	
	 * 	@param integer $posx horizontal position to start rendering the sprite
	 * 	@param integer $posy vertical position
	 * 	@param array(integer) $sprite sprite array
	 * 	@param array|Color $color A color, see {@link Image} or {@link Color}
	 * 	@param integer $scale Scale the sprite by given value 2 doubles the size
	 * 	@return Image
	 */
	public function drawSprite($posx, $posy, Array $sprite, $color = null, $invert = false, $scale = 1) {
		// get the color
		if (!is_integer($color)) {
			$color = $this->createColor($color);
			$color = $color->handle();
		}
		// draw sprite (without scaling)
		if ($scale == 1) {
			foreach ($sprite as $y => $line) {
				foreach($line as $x => $value) {
					if ((!$invert && $value) || ($invert && !$value)) {
						imagesetpixel($this->handle(), $posx+$x, $posy+$y, $color);
					}
				}
			}
		// draw sprit with scaling, using imagefilledrectangle for massivly
		// increasing performance.
		} else {
			foreach ($sprite as $y => $line) {
				foreach($line as $x => $value) {
					if ((!$invert && $value) || ($invert && !$value)) {
						$xstart = ($x * $scale) + $posx;
						$ystart = ($y * $scale) + $posy;
						$xend = $xstart + $scale;
						$yend = $ystart + $scale; 
						imagefilledrectangle($this->handle(), $xstart, $ystart, $xend, $yend, $color);
					}	
				}
			}
		}
		return $this;
	}
	
	/**
	 * 	Alias for {@link drawSprite}
	 * 	@return Image
	 */
	public function sprite($posx, $posy, Array $sprite, $color = null, $invert = false, $scale = 1) {
		return $this->drawSprite($posx, $posy, $sprite, $color, $invert, $scale);
	}
	
	/**
	 *	Test wheter php has gd lib installed or not
	 *	@throws ImageGDLibNotFoundException if gd lib was not found
	 * 	@return boolean
	 */
	public static function phpHasGDLib() {
		return (in_array('gd', get_loaded_extensions()));
	}
	
	/**
	 *	Returns image informations, like getimagesize does with caching
	 * 	@return array()
	 *	@throws FileNotFoundException if the file was not found
	 *	@throws FileEmptyException if the file is empty, length zero
	 */
	private function getImageInfo() {
		if (empty($this->imgInfo)) {
			$this->checkExistence();
			$this->imgInfo = getimagesize($this->nodeName);
		}
		return $this->imgInfo;
	}

	/**
	 *	Returns the width of the image
	 *	@return integer|Image
	 */	
	public function width($width = null) {
		if (func_num_args() == 0) {
			if (!empty($this->width)) return $this->width;
			if ($this->exists()) {
				$imgInfo = $this->getImageInfo();
				$this->width = $imgInfo[0];
			}
			return $this->width;
		}
		if (!is_int($width)) throw new IntegerExpectedException();
		if ($width <= 0) throw new ImageInvalidWidthException($width);
		$this->width = $width;
		return $this;
	}

	/**
	 *	Returns the height of the image
	 *	@return integer|Image
	 */
	public function height($height = null) {
		if (func_num_args() == 0) {
			if (!empty ($this->height)) return $this->height;
			if ($this->exists()) {
				$imgInfo = $this->getImageInfo();
				$this->height = $imgInfo[1];
			}
			return $this->height;
		}
		if (!is_int($height)) throw new IntegerExpectedException();
		if ($height <= 0) throw new ImageInvalidHeightException($height);
		$this->height = $height;
		return $this;
	}
	
	/**
	 *	Writes a text on the image
	 * 	<code>
	 * 	$img = new Image(200,200);
	 * 	$img->text(20, 20, 'Hello Image!', 1);
	 * 	$img->header();
	 * 	$img->render();
	 * 	</code>
	 * 	@param integer $x
	 * 	@param integer $y
	 * 	@param string|object $text
	 * 	@param Color|integer|array $color
	 * 	@param integer $size 0-6
	 * 	@param Color|integer|array $backgroundColor optional Background color
	 * 	@return Image
	 */
	public function text($x, $y, $text, $color, $size = null, $backgroundColor = null) {
		if (is_object($text)) {
			if (method_exists($text, '__toString')) {
				$text = $text->__toString();
			} elseif (method_exists($text, 'render')) {
				$text = $text->render();
			}
		}
		if (!is_integer($color)) {
			$color = $this->createColor($color);
			$color = $color->handle();
		}
		if ($backgroundColor !== null) {
			$this->rectangle($x-2, $y, $x+$this->textWidth($size), $y + $this->textHeight($size), $backgroundColor);
		}
		if ($size === null) {
			$size = $this->fontSize;
		}
		imagestring($this->handle(), $size, $x, $y, $text, $color);
		return $this;
	}
	
	/**
	 *	Returns the height of a font
	 *	@param  integer $font
	 *	@return integer
	 */
	public function fontHeight($font = null) {
		return ImageFontHeight(($font == null) ? $this->fontSize : $font);
	}							   
		
	/**
	 *	Returns the width of a font
	 *	@param  integer $font
	 *	@return integer
	 */						   
	public function fontWidth($font = null)  {
		return ImageFontWidth(($font == null) ? $this->fontSize : $font);
	}
	
	/**
	 *	Returns width of a text that is rendered in a specific font
	 *	@param string 	$text
	 *	@param integer	$font
	 *	@return integer
	 */
	public function textWidth($text, $font = null) {
		if (is_object($text)) {
			if (method_exists($text, '__toString')) {
				$text = $text->__toString();
			} elseif (method_exists($text, 'render')) {
				$text = $text->render();
			}
		}
		return strlen($text) * $this->fontWidth($font);
	}
	
	/**
	 * 	Image has Panel Format?
	 * 	@return boolean
	 */
	public function isPanelFormat() {
		return ($this->width() < $this->height());
	}
	
	/**
	 *	Image has Landscape Format?
	 *	@return boolean
	 */
	public function isLandScapeFormat() {
		return !$this->isPanelFormat();
	}

	/**
	 *	Resizes The Image to the given size using optional constrain proportions<br />
	 *	<br />
	 *	Resize uploaded image and put into folder
	 *	<code>
	 *		$image = new Image($_FILES["image"]["tmp_name"]);
	 *		$image->resizeTo(200,300,true);
	 *		$image->saveAs("images/products/product_1_thumb.jpg",100);
	 *	</code>
	 *	@param	integer	$width
	 *	@param 	integer	$height
	 *	@param	boolean $constrainProps
	 */
	public function resizeTo($width, $height, $constrainProps = true) {
		if ($constrainProps) {
			if ($this->isPanelFormat()) {
				$width = round($this->width() * ($height / $this->height()));
			} else {
				$height = round($this->height() * ($width / $this->width()));
			}
		}
		$oldHandle = $this->handle();
		$newHandle = $this->createHandle($this->type(), $width, $height);
		imagecopyresampled($newHandle, $oldHandle, 0, 0, 0, 0, $width, $height, $this->width(), $this->height());
		$this->handle = $newHandle;
		$this->width = $width;
		$this->height = $height;
		return $this;
	}
	
	/**
	 *	Resizes the image to the full thumbnail width, like flickr does
	 * 	also portrait sizes are scaled to full thumbnail sizes. Overlapping
	 * 	areas of the images are cropped.<br />
	 * 	<br />
	 * 	This is easier to understand if you think about the thumbnails in flickr
	 * 	or other webservices where the thumbnails always have the same size and 
	 * 	no panel or landscape format change.
	 * 
	 * 	@param	integer	$width
	 * 	@param	integer	$height
	 * 	@return Image
	 */
	public function stretchResizeTo($width, $height) {
		$srcX = 0;
		$srcY = 0;
		$srcW = $this->width();
		$srcH = $this->height();
		$scale = $this->height() / $height;
		if (($width * $scale) > $srcW) {
			$scale = $this->width() / $width;
		}
		$srcW = $width * $scale;
		$srcX = ($this->width() / 2) - ($srcW / 2);
		$srcH = $height * $scale;
		$srcY = ($this->height() / 2) - ($srcH / 2);
		$oldHandle = $this->handle();
		$newHandle = $this->createHandle($this->type(), $width, $height);
		imagecopyresampled($newHandle, $oldHandle, 0, 0, $srcX, $srcY, $width, $height, $srcW, $srcH);
		$this->handle = $newHandle;
		return $this;
	}
	
	/**
	 *	Alias for {@link stretchResizeTo}
	 * 	@param integer $width
	 * 	@param integer $height
	 * 	@return Image
	 */
	public function cropResizeTo($width, $height) {
		return $this->stretchResizeTo($width, $height);
	}
	
	/**
	 *	Scales the image in percent or float values
	 *
	 *	Scale image and save
	 *	<code>
	 *		$img = new Image("scaleme.jpg");
	 *		$img->scale(20);
	 * 		// same like
	 * 		$img->scale(0.2);
	 *	</code>
	 * 
	 *	@param	integer	$percent
	 *	@throws ImageScaleException
	 *	@return Image
	 */
	public function scale($percent) {
		//assert(is_integer($percent) || is_float($percent));
		if ($percent <= 0) throw new ImageScaleException($this);
		if ($percent <= 1) {
			$newWidth = $percent * $this->width();
			$newHeight = $percent * $this->height();
		} else {
			$newWidth = ($percent/100) * $this->width();
			$newHeight = ($percent/100) * $this->height();
		}
		$this->resizeTo(round($newWidth), round($newHeight));
		return $this;
	}

	/**
	 *	Returns the header Content Type string for this type of image
	 *
	 *	create image and show it in browser window with correct headers send
	 *	<code>
	 *		$image = new Image();
	 *		$image->height(100);
	 *		$image->width(200);
	 *		header($image->header());
	 *		echo $image;
	 *	</code>
	 * 	@param	string	$filename
	 * 	@param 	boolean	$download	if set to true a download header is created
	 *	@return string
	 */
	public function header($filename = null, $download = false) {
		$header[] = "Content-Type: image/" . $this->imageTypes[$this->type()];
		if ($download) {
			if ($filename !== null) {
				$header[] = 'Content-Disposition: attachment; filename="'.$filename.'"';	
			} else {
				$header[] = 'Content-Disposition: attachment; filename="image.'.$this->imageTypes[$this->type()].'"';	
			}
		} else {
			if ($filename !== null) {
				$header[] = 'Content-Disposition: inline; filename="'.$filename.'"';
			} else {
				$header[] = 'Content-Disposition: inline; filename="image/jpeg"';//.'.$this->imageTypes[$this->type()].'"';	
			}
		}
		foreach ($header as $line) header($line);
		return $header;
	}

	/**
	 *	Sets or Returns the image type
	 *	if type is set Image Class is returned, if no parameter passed type of image is returned
	 *	@param 	string $type
	 *	@return string|Image
	 *	@throws ImageIndeterminateFormat
	 *	@throws ImageInvalidTypeException
	 */
	public function type($type = null) {
		if (func_num_args() > 0) {
			if (is_integer($type)) {
				if (array_key_exists($type, $this->imageTypes)) {
					$type = $this->imageTypes[$type];
				} else {
					throw new ImageInvalidTypeException($type);
				}
			} elseif (!in_array($type, $this->availableTypes())) {
				throw new ImageInvalidTypeException($type);
			}
			$this->type = $type;
			return $this;
		}
		$imgInfo = $this->getImageInfo();
		if (!isset($imgInfo[2])) throw new ImageIndeterminateFormat($this);
		$this->type = $imgInfo[2];
		return $this->type;
	}
	
	/**
	 * 	Returns available Image Types this class can handle
	 * 	@return array(string)
	 */
	public function availableTypes() {
		return $this->imageTypes;
	}

	/**
	 *	Tests wheter the image has the given format
	 *	<code>
	 *		// test for on format
	 *		$r = $img->hasType(Image::TYPE_GIF);
	 *		// test for multiple types, using an array as parameter
	 *		$r = $img->hasType(array(Image::TYPE_GIF, Image::TYPE_JPG));
	 *	</code>
	 *	@param	string|array(string)	$type
	 */
	public function hasType($type) {
		if (is_array($type)) {
			return (in_array($this->type(), $type));
		} else {
			return ($this->type() == $type);
		}
	}
	
	/**
	 *	Alias for {@link hasType}
	 *	@see hasType
	 * 	@return boolean
	 */
	public function isType($type) {
		return $this->hasType($type);
	}

	/**
	 *	Returns the file extension for a image file, based on getImageType
	 *	@return string
	 *	@see getImageType
	 */
	public function extension() {
		return $this->imageTypes[$this->type()];
	}

	/**
	 *	Returns the number of color channels in the image
	 *	@return integer	Number of color channels
	 *	@throws ImageIndeterminateColorChannels
	 */
	public function channels() {
		$imgInfo = $this->getImageInfo();
		if (!array_key_exists('channels', $imgInfo)) {
			throw new ImageIndeterminateColorChannels($this);
		}
		return $imgInfo['channels'];
	}

	/**
	 *	Tests wheter the image has the given number of color channels
	 *	@param	integer	$channels
	 */
	public function hasChannels($channels) {
		return ($this->channels() == $channels);
	}
	
	/**
	 *	Returns the numbers of bits for the image
	 * 	@return integer
	 */
	public function bits() {
		$imgInfo = $this->getImageInfo();
		if (!array_key_exists('bits', $imgInfo)) {
			throw new ImageIndeterminateBits($this);
		}
		return (int) $imgInfo['bits'];
	}

	/**
	 *	Creates an Image Ressource Handle for you for an existing
	 *	image file or creates an empty image ressource handle of the 
	 *	given type, width and height. If no with and height is given
	 * 	the width and height property of the image is used.
	 *
	 *	@param	string	$type
	 *					Type of the image, jpg, gif, png
	 *	@param	integer	$width
	 *	@param 	integer $height
	 *	@return ressource
	 */
	public function createHandle($type = null, $width = null, $height = null) {
		// create handle from existing file
		if ($this->exists() && func_num_args() == 0) {
			switch ($this->type()) {
				case self::TYPE_GIF :
					$this->handle = imagecreatefromgif($this->nodeName);
					break;
				case self::TYPE_JPG :
					$this->handle = imagecreatefromjpeg($this->nodeName);
					break;
				case self::TYPE_PNG :
					$this->handle = imagecreatefrompng($this->nodeName);
					break;
				case self::TYPE_SWF :
					throw new ImageCreateUnableToCreateSWFException();
					break;
				default:
					throw new ImageUndetectableImageType($this);
					break;
			}
		// otherwise create empty image
		} else {
			if (is_null($type)) {
				$type = $this->type();
			}
			if (is_null($width)) $width = $this->width();
			if (is_null($height)) $height = $this->height();
			assert($width > 0 && $height > 0);
			// create JPG images in true color
			if ($type == self::TYPE_JPG || $type == 'jpg' || $type == 'jpeg') {
				$this->handle = imagecreatetruecolor($width, $height);
			} else {
				$this->handle = imagecreate($width, $height);
			}
		}
		return $this->handle;
	}
	
	/**
	 *	Return an image handle for this image.
	 * 	If no image handle has been created for this image it will be
	 * 	created using {@link createHandle}
	 *	@return ressource for image
	 */
	public function handle() {
		if (empty($this->handle)) {
			$this->createHandle();
		}
		return $this->handle;
	}

	/**
	 * 	Returns the image contents
	 * 	@param	integer	$quality optional JPG-Image quality (only applied if image is an jpeg)
	 * 	@return boolean|string image string source or boolean failure
	 */
	public function getImage($quality = 60) {
		switch ($this->type()) {
			default :
			case self::TYPE_GIF:
			case 'gif' :
				return imagegif($this->handle());
				break;
			case self::TYPE_JPG:
			case 'jpeg':
			case 'jpg' :
				return imagejpeg($this->handle(), '', $quality);
				break;
			case self::TYPE_PNG:
			case 'png' :
				return imagepng($this->handle());
				break;
			case self::TYPE_SWF:
			case 'swf' :
				throw new ImageCreateUnableToCreateSWFException();
				break;
		}
		return false;
	}

	/**
	 *	Saves the image on the harddrive
	 *
	 *	<code>
	 *		$image->saveImage("../img/products/product_1.jpg", 100);
	 *	<code>
	 *	@param	string $filename Optional Parameter for saving the image in an other filename
	 *	@param	integer	$quality optional jpeg quality
	 */
	public function saveImage($filename = null, $quality = 60) {
		if (is_null($filename)) {
			$filename = $this->nodeName;
		} else {
			//$this->nodeName = $filename;
		}
		switch ($this->type()) {
			default :
			case self::TYPE_GIF:
			case 'gif' :
				imagegif($this->handle(), $filename);
				break;
			case self::TYPE_JPG:
			case 'jpg' :
				imagejpeg($this->handle(), $filename, $quality);
				break;
			case self::TYPE_PNG:
			case 'png' :
				imagepng($this->handle(), $filename);
				break;
			case self::TYPE_SWF:
			case 'swf' :
				throw new ImageCreateUnableToCreateSWFException();
				break;
		}
		return true;
	}
	
	/**
	 *	Alias for {@link saveImage}
	 *	@see saveImage
	 */
	public function saveAs($filename, $quality = 60) {
		return $this->saveImage($filename, $quality);
	}
	
	/**
	 *	Saves image ressource on harddrive
	 * 
	 * 	Saving compressed Jpg Image:
 	 *	<code>
  	 *		$image = new Image("uncompressed.jpg");
 	 *		$image->save(50);
 	 *	</code>
	 *	@param integer	$quality Quality for jpg images
	 *	@return true on success
	 */
	public function save($quality = 60) {
		return $this->saveImage(null, $quality);
	}

	/**
	 *	Returns the calculated aspect Ratio (width to height aspect) of the image
	 * 	<code>
	 * 	$image = new Image(640, 480);
	 * 	// echoes 1.33
	 * 	echo $image->aspectRatio();
	 * 	</code>
	 *	@param	integer	$precision
	 *	@return float
	 */
	public function aspectRatio($precision = 2) {
		// prevent division by zero
		if ($this->height() == 0 || $this->width() == 0) {
			return 0.0;
		}
		// calculate ratio
		if ($this->isPanelFormat()) {
			return round($this->height() / $this->width(), $precision);
		} else {
			return round($this->width() / $this->height(), $precision);
		}
	}
	
	/**
	 * 	Draws a N-3-Point Bezier Curve
	 * 	
	 *	According to Wickipedia a <a href="http://de.wikipedia.org/wiki/B%C3%A9zierkurve">Bezier</a>
	 * 	Curve is a parametic curve important in computer graphics. 
	 * 
	 * 	Simple example:
	 * 	<code>
	 * 	$img->drawBezier(array(
	 *		array(200, 200),
	 *		array(400, 100),
	 *		array(50, 20),
	 *		array(400, 200)
	 *	), 'FFFF00');
	 * 	</code>
	 * 	
	 * 	@param array(integer)|array(array(integer))	$points 4 Points to use to draw the bezier
	 *	@param handle|Color $col Color to draw with
	 *	@param float $precision	standard 0.0125 has large precision, 0.1 some rougher style, 0-1 are valid inputs
	 * 	@param integer $thickness
	 * 	@return Image
	 */
	function drawBezier(Array $points, $color, $precision = 0.0125, $thickness = 1) {
		// check Point parameter
		if (count($points) == 8) {
			list($ax, $ay, $bx, $by, $cx, $cy, $dx, $dy) = $points;
		} elseif (count($points) == 4 && is_array($points[0])) {
			list($ax, $ay) = $points[0];
			list($bx, $by) = $points[1];
			list($cx, $cy) = $points[2];
			list($dx, $dy) = $points[3];
		} else {
			throw new ImageDrawBezierInvalidPointCount($this);
		}
		// get the color
		if (!is_integer($color)) {
			$color = $this->createColor($color);
			$color = $color->handle();
		}
		// start Drawing
		$ox = $ax;
		$oy = $ay;
		// set line thickness
		if ($thickness > 1) $this->setThickness($thickness);
		// t goes from 0 to 1 in $precision (curve precision)
		$t = 0;
		while($t <= 1) {
			$a = $t;
			$b = 1 - $t;
			$x = $ax * $b * $b * $b + 3 * $bx * $b * $b * $a + 3 * $cx * $b * $a * $a + $dx * $a * $a * $a;
			$y = $ay * $b * $b * $b + 3 * $by * $b * $b * $a + 3 * $cy * $b * $a * $a + $dy * $a * $a * $a;
			//DrawRect x-1,y-1,3,3
			imageline($this->handle(), $ox, $oy, $x, $y, $color);
			$ox = $x;
			$oy = $y;
			$t += $precision;
		}
		// reset thickness to 1
		$this->resetThickness();
		return $this;
	}

	/**
	 *	Returns the most dominat color a new Color
	 * 	@return Color
	 */
	public function dominantColor() {
		$colormatrix = $this->getColorMatrix();
		$width = $this->width();
		$height = $this->height();
		$colorOccurence = array ();
		for ($x = 0; $x < $width; $x++) {
			for ($y = 0; $y < $height; $y++) {
				$color = $colormatrix[$x][$y];
				if (!isset ($colorOccurence[$color["index"]])) {
					$colorOccurence[$color["index"]] = 1;
				} else {
					$colorOccurence[$color["index"]]++;
				}
			}
		}
		arsort($colorOccurence);
		$keys = array_keys($colorOccurence);
		return $this->getColorForIndex($keys[0]);
	}
	
	public function createColor($color) {
		$args = func_get_args();
		if (get_class($args[0]) == 'Color') {
			$color = $args[0];
			$color->image = $this;
		} else {
			if (is_string($args[0]) || is_array($args[0])) {
				$color = new Color($this, $args[0]);
			} elseif (count($args) == 3) {
				$color = new Color($this, $args[0], $args[1], $args[2]);
			}
		}
		return $color;
	}

	/**
	 *	Return a {@link Color} Object for a index given. If the
	 * 	Color was not found false is returned
	 * 
	 * 	@param 	integer	$colorIndex Index of Color you want to get
	 *	@return Color|boolean
	 */
	public function getColorForIndex($colorIndex) {
		if (isset ($this->colors[$colorIndex])) {
			return $this->colors[$colorIndex];
		} elseif (!empty($this->handle)) {
			$color = imagecolorsforindex($this->handle, $colorIndex);
			$this->colors[$colorIndex] = new Color($color);
			return $color;
		}
		return false;
	}

	/**
	 * 	Converts the image to a color matrix.
	 * 	@return	array(integer(array(string)))
	 */
	public function getColorMatrix() {
		$imageHandle = $this->handle();
		$width = $this->width();
		$height = $this->height();
		$matrix = array ();
		for ($x = 0; $x < $width; $x++) {
			for ($y = 0; $y < $height; $y++) {
				$matrix[$x][$y] = $this->getColorForIndex(imagecolorat($imageHandle, $x, $y));
			}
		}
		return $matrix;
	}
	
	public function beforeRender() {
		return true;
	}
	
	public function afterRender($rendered) {
		return $rendered;
	}
	
	/**
	 * 	Returns the rendered image source that can be saved or send to output
	 * 	@param integer $quality
	 * 	@return string
	 */
	public function render($quality = 80) {
		return $this->getImage($quality);
	}
	
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ImageException extends BasicException {}

/**
 * 	Thrown if the gd-lib (that is used by the {@link Image} class can
 * 	not be found. (checked on {@link Image} constructor.
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ImageGDLibNotFoundException extends ImageException {}

/**
 *	Thrown if a bezier curve has to less points to draw
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 * 
 */
class ImageDrawBezierInvalidPointCount extends ImageException {}

/**
 *	Thrown if this class should create or manipulate swf files
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ImageCreateUnableToCreateSWFException extends ImageException {}

/**
 *	Thrown if the image type could not be determined
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ImageUndetectableImageType extends ImageException {
	public function __construct(Image $image) {
		if ($image->exists()) {
			$message = 'Unable to determine type of image (\''.$image->nodeName.'\').';
		} else {
			$message = 'Unable to determine type of image.';
		}
		parent::__construct($message);
	}
}

/**
 *	Thrown if the image type could not be determined
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ImageIndeterminateBits extends ImageException {}

/**
 *	Thrown if the number of channels in the image could not be found
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ImageIndeterminateColorChannels extends ImageException {}

?>