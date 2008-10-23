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

/**
 *	Image Color Class
 *
 *	Calculate RGB Values from Hex and back:
 *	<code>
 *		$color = new Color(array(255,255,255));
 *		// is same as
 *		$color = new Color(255,255,255);
 *		// get hex value for that color
 *		$hex = $color->hex();
 *		// convert without object context
 *		$rgb = Color::HexToRGB("#FF00EB");
 *	</code>
 *	<br />
 *	Lighten or darken a color:
 *	<code>
 *		$color = new Color(array(255,0,0));
 *		$darkened = $color->shift(-20);
 *	</code>
 *	
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 03.05.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@version 0.2
 */
class Color extends Object {
	
	public $r;
	public $g;
	public $b;
	public $handle;
	public $image;

	/**
	 *	Color Constructor
	 *	@param array(integer), integer, string
	 */
    public function __construct() {
    	parent::__construct();
    	// create color
    	$args = func_get_args();
    	// parameters passed: image, hexcolorstring
    	if (count($args) == 2 && is_string($args[1])) {
    		$this->image = $args[0];
    		list($this->r, $this->g, $this->b) = Color::HexToRGB($args[1]);
    	// parameters passed: image, r, g, b
    	} elseif (count($args) == 4) {
    		$this->image = $args[0];
    		$this->r = (int) $args[0];
    		$this->g = (int) $args[1];
    		$this->b = (int) $args[2];
    	// parameters passed: image, array(r,g,b)
    	} elseif (count($args) == 2 && is_array($args[1])) {
    		$this->image = $args[0];
    		$this->r = (int) $args[1][0];
    		$this->g = (int) $args[1][1];
    		$this->b = (int) $args[1][2];
    	// parameters passed: hexcolorstring
    	} elseif (count($args) == 1 && is_string($args[0])) {
    		list($this->r, $this->g, $this->b) = Color::HexToRGB($args[0]);
    	// parameters passed: array(r,g,b)
    	} elseif (count($args) == 1 && is_array($args[0])) {
    		$this->r = (int) $args[0][0];
    		$this->g = (int) $args[0][1];
    		$this->b = (int) $args[0][2];
    	// parameters passed r,g,b
    	} elseif (count($args) == 3) {
    		$this->r = (int) $args[0];
    		$this->g = (int) $args[1];
    		$this->b = (int) $args[2];
    	}
    	if (is_resource(func_get_arg(0)) || is_object(func_get_arg(0))) {
    		$this->handleCreate();
    	}
    	return $this;
    }
    
    /**
     *	Returns color handle used for painting in images
     *	@return integer
     */
    public function handle() {
    	if (empty($this->handle)) {
    		$this->handleCreate();	
    	}
    	return $this->handle;
    }
    
    /**
     *	Creates a Handle for this color and returns it
     *	@return integer
     */
    public function handleCreate() {
    	$this->handle = imagecolorallocate($this->image->handle(), $this->r, $this->g, $this->b);
    	return $this->handle;
    }
    
    /**
     *	Shifts r,g,b values for a given factor, usefull for lighten or darken a color
     *	pass positive factors for lighten and negative for darken the color
     *
     *	@param	integer $factor
     */
    public function shift($factor) {
    	$this->r += $factor;
    	$this->g += $factor;
    	$this->b += $factor;
    	if ($this->r < 0) $this->r = 0;
    	if ($this->r > 255) $this->r = 255;
    	if ($this->g < 0) $this->g = 0;
    	if ($this->g > 255) $this->g = 255;
    	if ($this->b < 0) $this->b = 0;
    	if ($this->b > 255) $this->b = 255;
    }
    
    /**
     *	Returns Hex Value for this Color as String
     *	@return string
     */
    public function hex() {
    	$hex = self::RGBtoHex($this->r, $this->g, $this->b);
    	return $hex;
    }
    
    /**
     *	Returns r,g,b array for this color
     *	@return array(integer)
     */
    public function rgb() {
    	return array($this->r, $this->g, $this->b);
    }
    
    /**
     *	Returns r,g,b array as float values,
     * 	0 = 0, 1 = 255, 0.5 = 128
     * 	@return array(float)
     */
   	public function rgbFloat() {
   		return array($this->r / 255, $this->g / 255, $this->b / 255);
   	}
   	
   	/**
   	 * 	Returns the color value as float values
   	 *	@return array(float)
   	 */
   	public function toFloat() {
   		return Color::rgbFloat($this->r, $this->g, $this->b);
   	}
    
    /**
     *	Converts a Hex Value to an array(R,G,B)
     *	@param	string $hex
     *	@return array(integer)
     */
    public static function HexToRGB($hex) {
    	// cut #
    	if (substr($hex,0,1) == '#') $hex = substr($hex, 1);
    	$rgb = hexdec($hex);
    	return array(($rgb >> 16) & 0xFF, ($rgb >> 8) & 0xFF, $rgb & 0xFF);
    }
    
    /**
     *	Converts RGB to HEX
     *	@param array(integer)|integer	RGB Array or Red Value
     *	@param integer	Green
     *	@param integer	Blue
     *	@return string
     */
    public static function RGBtoHex($red, $green = null, $blue = null) {
    	if (is_array($red)) {
    		return self::RGBtoHex($red[0],$red[1],$red[2]);
    	}
    	return str_pad(dechex($red),2,'0',STR_PAD_LEFT).str_pad(dechex($green),2,'0',STR_PAD_LEFT).str_pad(dechex($blue),2,'0',STR_PAD_LEFT);
    }
    
    /**
     * 	Returns the hex value for this color
     * 	@return string
     */
    public function toHex() {
    	return Color::RGBtoHex($this->r, $this->g, $this->b);
    }
    
    /**
     * 	Converts rgb values to YUV
     * 	
     * 	YUV colors are described here <a href="http://de.wikipedia.org/wiki/YUV">YUV</a>
     *
     * 	@param integer|array(integer) $r
     * 	@param integer $g
     * 	@param integer $g
     * 	@return array array containing Y, U and V value
     */
    public static function RGBtoYUV($r, $g = null, $b = null) {
    	if (is_array($r)) {
    		list($r, $g, $b) = $r;
    	}
    	$y = (0.299 * $r) + (0.587 * $g) + (0.114 * $b);
    	$u = ($b - $y) * 0.493;
    	$v = ($r - $y) * 0.877;
    	return array($y, $u, $v);
    }
    
    /**
     *	Returns this color as YUV values
     * 	@return array(integer)
     */
    public function toYUV() {
    	return Color::RGBtoYUV($this->r, $this->g, $this->b);
    }
    
    /**
     * 	Convert YUV color values back to RGB values
     * 	@param array(integer) $y
     * 	@param integer $u
     * 	@param integer $v
     * 	@return array
     */
    public static function YUVtoRGB($y, $u = null, $v = null) {
    	if (is_array($y)) {
    		list($y, $u, $v) = $y;	
    	}
    	$b = $y + $u / 0.493;
    	$r = $y + $v / 0.877;
    	$g = (1.7 * $y) - (0.509 * $r) - (0.194 * $b);
    	return array(round($r), round($g), round($b));
    }
    
    /**
     * 	Converts rgb values to HSV
     * 	
     * 	HSV Colors consist of hue, saturation and value values that represent
     * 	diffent informations about a color.
     * 	The HSV color model is described here: <a href="http://de.wikipedia.org/wiki/HSV_%28Farbmodell%29">HSV</a>
     *
     * 	@param integer|array(integer) $r
     * 	@param integer $g
     * 	@param integer $b
     * 	@return array
     */
	public static function RGBtoHSV ($r, $g, $b) {
		if (is_array($r)) {
			list($r, $g, $b) = $r; 
		} 
		$var_R = ($r / 255); 
		$var_G = ($g / 255); 
		$var_B = ($b / 255); 
		$var_Min = min($var_R, $var_G, $var_B); 
		$var_Max = max($var_R, $var_G, $var_B); 
		$del_Max = $var_Max - $var_Min;
		$V = $var_Max;
		$max = $var_Max;
		if ($del_Max == 0) { 
			$H = 0; 
			$S = 0; 
		} else {
			$S = $del_Max / $var_Max; 
			$del_R = ((($max - $var_R) / 6) + ($del_Max / 2)) / $del_Max; 
			$del_G = ((($max - $var_G) / 6) + ($del_Max / 2)) / $del_Max; 
			$del_B = ((($max - $var_B) / 6 ) + ( $del_Max / 2)) / $del_Max;
      		if ($var_R == $var_Max) $H = $del_B - $del_G;
			else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B; 
			else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R;
			if ($H<0) $H++;
			if ($H>1) $H--;
		}
		return array($H, $S, $V); 
	}
	
	/**
	 *	Returns this color as HSV values
	 * 	@return array(integer)
	 */
	public function toHSV() {
		return Color::RGBtoHSV($this->r, $this->g, $this->b);
	}
    
    /**
     *	Converts a RGB Color to HUV Color
     * 	@param array(integer)|integer RGB Array or red value
     * 	@param integer Green value
     * 	@param integer Blue value
     */
    public static function RGBtoHUV($r, $g = null, $b = null) {
    	if (is_array($r)) {
    		list($r, $g, $b) = $r;
    	}
    	$h = pow(cos( ( (1/2)*( ($r-$g)+($r-$b) ) / ( sqrt( ($r-$g)*($r-$g) + ($r-$b)*($g-$b) ) ) )), -1);
		$s = 1 - (3/($r+$g+$b)) * min($r,$g,$b);
		$v = ($r + $g + $b) / 3;
		return array($h, $s, $v);
    }
    
    /**
     *	Returns this color as HUV values
     * 	@return array(integer)
     */
    public function toHUV() {
    	return Color::RGBtoHUV($this->r, $this->g, $this->b);
    }
    
}

?>