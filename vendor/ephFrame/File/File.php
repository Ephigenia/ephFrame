<?php

namespace ephFrame\File;

class File
{
	protected $path;
	
	protected $mimeType;
	
	public function __construct($path)
	{
		$this->path = $path;
	}
	
	public function basename()
	{
		return basename($this->path);
	}
	
	public function filename()
	{
		return pathinfo(basename($this->path), \PATHINFO_FILENAME);
	}
	
	public function exists()
	{
		return file_exists($this->path) && is_file($this->path);
	}
	
	public function readable()
	{
		return is_readable($this->path);
	}
	
	public function writable()
	{
		return is_writable($this->path);
	}
	
	public function mimeType()
	{
		if (empty($this->mimeType)) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$this->mimeType = finfo_file($finfo, $this->path);
			finfo_close($finfo);
		}
		return $this->mimeType;
	}
	
	public function extension()
	{
		return pathinfo($this->basename(), \PATHINFO_EXTENSION);
	}
	
	public function directory()
	{
		return dirname($this->path);
	}
	
	public function size()
	{
		return filesize($this->path);
	}
	
	public function saveAs($path, $createDirs = true)
	{
		return $this->copy($oath, $createDirs);
	}
	
	public function move($path, $createDirs = true)
	{
		if ($createDirs && !is_dir(dirname($path))) {
			mkdir(dirname($path), 0755, true);
		}
		if (!rename($this->path, $path)) {
			throw new Exception(sprintf('Could not move file %s to %s', $this->path, $path));
		}
		$this->path = $path;
		return $this;
	}
	
	public function copy($path, $createDirs = true)
	{
		if ($createDirs && !is_dir(dirname($path))) {
			mkdir(dirname($path), 0755, true);
		}
		if (copy($this->path, $path)) {
			throw new Exception(sprintf('Could not copy file %s to %s', $this->path, $path));
		}
		$class = get_class($this);
		return new $class($path);
	}
	
	public function delete()
	{
		return unlink($this->path);
	}
	
	public function __toString()
	{
		return $this->path;
	}
}

class Exception extends \Exception {}