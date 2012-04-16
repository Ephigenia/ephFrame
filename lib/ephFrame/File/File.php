<?php

namespace ephFrame\File;

class File extends \SPLFileInfo
{
	protected $mimeType;
	
	public function basename($suffix = true)
	{
		if ($suffix == false) {
			return pathinfo($this->pathName(), PATHINFO_FILENAME);
		}
		return parent::getBasename($suffix);
	}
	
	public function filename($suffix = true)
	{
		return $this->basename($suffix);
	}
	
	public function exists()
	{
		return is_file((string) $this);
	}
	
	public function readable()
	{
		return parent::isReadable();
	}
	
	public function writable()
	{
		return parent::isWritable();
	}
	
	public function mimeType()
	{
		if (empty($this->mimeType)) {
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$this->mimeType = finfo_file($finfo, (string) $this);
			finfo_close($finfo);
		}
		return $this->mimeType;
	}
	
	public function extension()
	{
		return pathinfo((string) $this, PATHINFO_EXTENSION);
	}
	
	public function pathname()
	{
		return parent::getPathname();
	}
	
	public function path()
	{
		return parent::getPath();
	}
	
	public function realPath()
	{
		return parent::getRealPath();
	}
	
	public function aTime()
	{
		return parent::getATime();
	}
	
	public function mTime()
	{
		return parent::getMTime();
	}
	
	public function cTime()
	{
		return parent::getCTime();
	}
	
	public function owner()
	{
		return parent::getOwner();
	}
	
	public function group()
	{
		return parent::getGroup();
	}
	
	public function size()
	{
		return parent::getSize();
	}
	
	public function saveAs($path, $createDirs = true)
	{
		return $this->copy($path, $createDirs);
	}
	
	public function move($path, $createDirs = true)
	{
		if (!$this->exists()) {
			throw new FileNotFoundException();
		}
		if ($createDirs && !is_dir(dirname($path))) {
			mkdir(dirname($path), 0755, true);
		}
		if (!rename((string) $this, $path)) {
			throw new Exception(sprintf('Could not move file %s to %s', (string) $this, $path));
		}
		$this->path = $path;
		return $this;
	}
	
	public function copy($path, $createDirs = true)
	{
		if (!$this->exists()) {
			throw new FileNotFoundException();
		}
		if ($createDirs && !is_dir(dirname($path))) {
			mkdir(dirname($path), 0755, true);
		}
		if (copy((string) $this, $path)) {
			throw new Exception(sprintf('Could not copy file %s to %s', (string) $this, $path));
		}
		$class = get_class($this);
		return new $class($path);
	}
	
	public function delete()
	{
		if (!$this->exists()) {
			throw new FileNotFoundException();
		}
		return unlink((string) $this);
	}
}

class Exception extends \Exception {}

class FileNotFoundException extends Exception {}

class FileNotReadableException extends Exception {}