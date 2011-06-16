<?php

namespace ephFrame\File;

class Image extends File
{
	protected $handle;
	
	protected $width;
	
	protected $height;
	
	public $antialiased = true;
	
	public $thickness = 1;
	
	public function __construct($filenameOrWidth, $height = null)
	{
		if (is_int($filenameOrWidth)) {
			$this->width = $filenameOrWidth;
			$this->height = $height;
		} else {
			parent::__construct($filenameOrWidth);
			$imginfo = getimagesize($filenameOrWidth);
			$this->width = $imginfo[0];
			$this->height = $imginfo[1];
		}
	}
	
	protected function handle()
	{
		if (!$this->handle) {
			if ($this->exists()) {
				switch($this->mimeType()) {
					case 'image/jpeg':
						$this->handle = imagecreatefromjpeg($this->path);
						break;
					case 'image/gif':
						$this->handle = imagecreatefromgif($this->path);
						break;
					case 'image/png':
						$this->handle = imagecreatefrompng($this->path);
						break;
					default:
						throw new ImageInvalidMimeTypeException($this->mimeType());
				}
			} else {
				$this->handle = imagecreatetruecolor($this->width, $this->height);
			}
		}
		return $this->handle;
	}
	
	public function width($width = null)
	{
		if (func_num_args() == 0) {
			return $this->width;
		}
		return $this->scale($width, $this->height);
	}
	
	public function height($height = null)
	{
		if (func_num_args() == 0) {
			return $this->height;
		}
		return $this->scale($height, $this->height);
	}
	
	public function scale($width, $height, $constrainProportions = true)
	{
		$targetWidth = $width;
		$targetHeight = $height;
		// scale proportianal
		if ($constrainProportions) {
			if ($width > $height) {
				$targetHeight = round($this->height * ($width / $this->width));
			} else {
				$targetWidth = round($this->width * ($height / $this->height));
			}
			if ($targetHeight > $height) {
				$targetWidth = round($this->width * ($height / $this->height));
				$targetHeight = $height;
			} elseif ($targetWidth > $width) {
				$targetHeight = round($this->height * ($width / $this->width));
				$targetWidth = $width;
			}
		}
		$newHandle = imagecreatetruecolor($targetWidth, $targetHeight);
		imagecopyresampled($newHandle, $this->handle(), 0, 0, 0, 0, $targetWidth, $targetHeight, $this->width, $this->height);
		$this->handle = $newHandle;
		$this->width = $targetWidth;
		$this->height = $targetHeight;
		return $this;
	}
	
	public function cropScale($width, $height)
	{
		$width = $width ?: $this->width;
		$height = $height ?: $this->height;
		$srcX = $srcY = 0;
		$srcW = $this->width;
		$srcH = $this->height;
		$scale = $this->height / $height;
		if (($width * $scale) > $srcW) {
			$scale = $this->width / $width;
		}
		$srcW = $width * $scale;
		$srcX = ($this->width / 2) - ($srcW / 2);
		$srcH = $height * $scale;
		$srcY = ($this->height / 2) - ($srcH / 2);
		$newHandle = imagecreatetruecolor($width, $height);
		imagecopyresampled($newHandle, $this->handle(), 0, 0, $srcX, $srcY, $width, $height, $srcW, $srcH);
		$this->handle = $newHandle;
		$this->width = $width;
		$this->height = $height;
		return $this;
	}
	
	public function crop($x1, $y1, $x2, $y2)
	{
		$width = $x2 - $x1;
		$height = $y2 - $y1;
		$newHandle = imagecreatetruecolor($width, $height);
		imagecopyresampled($newHandle, $this->handle(), 0, 0, $x1, $y1, $width, $height, $width, $height);
		$this->handle = $newHandle;
		$this->width = $width;
		$this->height = $height;
		return $this;
	}
	
	public function pixel($x, $y, $color = null)
	{
		if ($color) {
			imagesetpixel($this->handle(), $x, $y, $color);
		} else {
			return imagecolorat($this->handle(), $x, $y);
		}
	}
	
	public function border($color, $width = 1, $padding = 0)
	{
		for($i = 0; $i < $width; $i++) {
			$this->rect($i + $padding, $i + $padding, $this->width - $i - 1 - $padding, $this->height - $i - 1 - $padding, $color);
		}
		return $this;
	}
	
	public function rectangle($x1, $y1, $x1, $y2, $borderColor, $backgroundColor = null, $thickness = 1, $antialiased = null)
	{
		$thickness = $thickness ?: $this->thickness;
		$antialias = $antialias ?: $this->antialias;
		$this->thickness($thickness);
		$this->antialias($antialiased);
		if ($backgroundColor) {
			imagefilledrectangle($this->handle(), $x1, $y1, $x2, $y2, $backgroundColor);
		} else {
			imagerectangle($this->handle(), $x1, $y1, $x2, $y2, $borderColor);
		}
		$this->thickness($this->thickness);
		$this->antialias($this->antialias);
		return $this;
	}
	
	public function line($x1, $y1, $x2, $y2, $color, $thickness = null, $antialiased = null)
	{
		$thickness = $thickness ?: $this->thickness;
		$antialias = $antialias ?: $this->antialias;
		$this->thickness($thickness);
		$this->antialias($antialiased);
		imageline($this->handle(), $x1, $y1, $x2, $y2, $color);
		$this->thickness($this->thickness);
		$this->antialias($this->antialias);
		return $this;
	}
	
	public function antialias($antialias)
	{
		if ($antialias && function_exists('imageantialias')) {
			imageantialias($this->handle(), $antialias);
		}
		return $this;
	}
	
	public function thickness($thickness)
	{
		imagesetthickness($this->handle(), $thickness);
		return $this;
	}
	
	public function saveAs($path, $quality = null, $createDirs = true)
	{
		if ($createDirs && !is_dir(dirname($path))) {
			mkdir(dirname($path), 0755, true);
		}
		switch($this->mimeType) {
			case 'image/jpeg':
				if ($quality) {
					imagejpeg($this->handle(), $path, $quality);
				} else {
					imagejpeg($this->handle(), $path);
				}
				break;
			case 'image/png':
				imagepng($this->handle(), $path);
				break;
			case 'image/gif':
				imagegif($this->handle(), $path);
				break;
		}
		$this->path = $path;
		return $this;
	}
}

class ImageInvalidMimeTypeException extends Exception
{
	public function __construct($mimeType)
	{
		parent::__construct(sprintf('Image class was unable to create a handle for the "%s" mimetype', $mimetype));
	}
}