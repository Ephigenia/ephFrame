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
ephFrame::loadClass('ephFrame.lib.Image');

/**
 * Abstract Barcode Class
 * <a href="http://de.wikipedia.org/wiki/Barcode">Barcode</a>
 *
 * Easy to use Barcode Abstract Class.
 * Integrations have been made for 
 * - I25 aka ISO16390
 * Integrations wich are not completed yet
 * - EAN
 * - Code128
 * 
 * @author Marcel Eichner // Ephigenia <love at ephigenia dot de>
 * @since 15.05.2006
 * @package ephFrame
 * @subpackage ephFrame.lib
 */
abstract class Barcode extends Image {
{
	public $code;
	
	public $barColor;
	public $borderColor;
	public $backgroundColor;
	public $textColor;
	
	private $drawPos = 10;
	private $drawText = true;
	private $drawBorder = true;
	private $stretchText = true;

	/**
	 * Abstract BarCode Constructor
	 */
    public function __construct($code, $width = 300, $height = 100) 
	{
    	$this->type("gif");
    	$this->font(2);
    	$this->width($width);
    	$this->height($height);
    	$this->code($code);
    	$this->backgroundColor("#ffffff");
    	$this->textColor("#000000");
    	$this->barColor("#000000");
    	$this->borderColor("#000000");
    }
    
    /**
     * Sets the String to Barcode
     * @param string $code
     * @throws BarcodeEmptyCodeException
     */
    public function code($code = -1) 
	{
    	if ($code != -1) {
    		if (empty($code)) throw new BarcodeEmptyCodeException();
    		$this->code = $code;	
    	}
    	return $this->code;
    }
    
    /**
     * Draws the text under the barcode
     * @param boolean $bool
     */
    final public function drawText($bool = -1) 
	{
    	if ($bool != -1) {
    		$this->drawText = (bool) $bool;
    	} else {
    		$textYPos = $this->height() - $this->fontHeight() - 8;
    		$textWidth = $this->fontWidth() * strlen($this->code);
    		if ($this->stretchText()) {
    			$barcodeWidth = $this->barcodeWidth();
    			$deltaX = $barcodeWidth / $textWidth;
    			for ($i = 0; $i < strlen($this->code); $i++) {
    				// textpos + center 
    				$textXPos = round(($deltaX * $i * $this->fontWidth()) + ($this->width() / 2) - ($barcodeWidth / 2) + 6);
    				imagestring($this->handle(), $this->font(), $textXPos, $textYPos, $this->code[$i], $this->textColor->handle());
    			}
    		} else {
	    		$textXPos = round(($this->width() / 2) - ($textWidth / 2));
	    		imagestring($this->handle(), $this->font(), $textXPos, $textYPos, $this->code, $this->textColor->handle());	
    		}
    	}
    }
    
    /**
     * Define if the text should be stretched along the hole barcode
     * @param boolean $bool
     */
    final public function stretchText($bool = -1) 
	{
    	if ($bool != -1) $this->stretchText = (bool) $bool;
    	return $this->stretchText;
    }
    
    /**
     * Define if the border should be drawn or draw the border if no parameter is passed
     * @param boolean $bool
     */
    final public function drawBorder ($bool = -1) 
	{
    	if ($bool != -1) {
    		$this->drawBorder = (bool) $bool;	
    	} else {
	    	imagerectangle($this->getImageHandle(), 0, 0, $this->width()-1, $this->height()-1, $this->borderColor()->handle());
    	}
	}
	
	final private function _newColor($colorname, $color = -1) {
		if ($color != -1) {
			$this->{$colorname} = (get_class($color) == "Color") ? $color : new Color($this, $color);
		}
		return $this->{$colorname};
	}
	
	/**
	 * Define the Backgroundcolor
	 * @param string|array(integer)|Color	$backgroundColor
	 */
	final public function backgroundColor($backgroundColor = -1) 
	{
		return $this->_newColor("backgroundColor",$backgroundColor);
	}
	
	/**
	 * Define the Color of the Border, useless if drawBorder is set to false
	 * @param string|array(integer)|Color	$borderColor
	 */
    final public function borderColor($borderColor = -1) 
	{
    	return $this->_newColor("borderColor", $borderColor);
    }
    
    /**
	 * Define the Color for the barcode Bars
	 * @param string|array(integer)|Color	$barColor
	 */
    final public function barColor($barColor = -1) 
	{
    	return $this->_newColor("barColor", $barColor);
    }
    
    /**
	 * Define the Color of the Text, useless if drawText is set to false
	 * @param string|array(integer)|Color	$textColor
	 */
    final public function textColor($textColor = -1) 
	{
    	return $this->_newColor("textColor", $textColor);
    }
    
    public function increaseDrawPos($int) 
	{
    	$this->drawPos += $int;
    }
    
    public function drawPos($pos = -1) 
	{
    	if ($pos != -1) $this->drawPos = $pos;
    	return $this->drawPos;
    }
    
    public function drawBar($xPos, $width) 
	{
    	$YEnd = $this->height()-10;
    	if ($this->drawText) {
    		$YEnd -= $this->fontHeight();
    	}
    	imagefilledrectangle($this->handle(), $xPos, 10, $xPos+($width-1), $YEnd, $this->barColor()->handle());
    	$this->increaseDrawPos($width);
    }
    
    /**
     * Returns the content of the image
     * @return string
     */
    public function getImage($quality = 60) 
	{
    	if ($this->drawText) $this->drawText();
    	if ($this->drawBorder) $this->drawBorder();
    	// calculate Starting Position for barcode
    	$this->drawPos(round(($this->width() / 2) - ($this->barcodeWidth() / 2)));
    	$this->drawStart();
    	$this->drawCode();
    	$this->drawEnd();
    	return parent::getImage($quality);
    }
    
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class BarcodeException extends BasicException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class BarcodeEmptyCodeException extends BarcodeException 
{
	public function __construct() 
	{
		$this->message = 'You must set a code for the barcode. Empty Codes are invalid!';
		parent::__construct();
	}
}