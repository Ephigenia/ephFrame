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

/**
 * Class for persitens storage of model structure data.
 * 
 * This class stores and reads structures saved for the model.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 25.10.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.model
 */
class ModelStructureCache extends Object
{	
	public static $cache = array();
	
	protected $structure = array();
	
	protected $model;
	
	protected $ttl = HOUR;
	
	protected $filename;
	
	protected $fileExtension = 'structure.json';
	
	public function __construct($model, $ttl = HOUR) 
	{
		$this->model = $model;
		$this->ttl = $ttl;
		$this->filename = MODELCACHE_DIR.$this->model->tablename.'.'.$this->fileExtension;
		return $this;
	}
	
	protected function expired() {
		return (filemtime($this->filename) + $this->ttl) > time();
	}
	
	public function load() 
	{
		if (isset(self::$cache[$this->model->name])) {
			return self::$cache[$this->model->name];
		}
		if ($this->ttl > 0 && file_exists($this->filename) && is_readable($this->filename) && $this->expired()) {
			foreach (json_decode(file_get_contents($this->filename)) as $fieldName => $fieldInfoArr) {
				$this->structure[$fieldName] = new ModelFieldInfo();
				$this->structure[$fieldName]->fromJson($fieldInfoArr);
			}
			Log::write(Log::VERBOSE, get_class($this).': '.$this->model->name.' structure loaded from model structure cache.');
			self::$cache[$this->model->name] = $this->structure;
			return $this->structure;
		}
		return false;
	}
	
	public function save(Array $structure = array()) 
	{
		if (!file_exists(MODELCACHE_DIR) || !is_dir(MODELCACHE_DIR)) {
			throw new ModelStructureCacheDirNotFoundException(MODELCACHE_DIR);
		} elseif (!is_writable(MODELCACHE_DIR)) {
			throw new ModelStructureCacheDirNotWritableException(MODELCACHE_DIR);
		}
		foreach($structure as $fieldInfo) {
			$data[$fieldInfo->name] = $fieldInfo->toArray();
		}
		file_put_contents($this->filename, json_encode($data));
		Log::write(Log::VERBOSE, 'ephFrame: '.get_class($this).' structure stored in model cache.');
		self::$cache[$this->model->name] = $structure;
		return true;
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ModelStructureCacheException extends ObjectException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ModelStructureCacheDirException extends ModelStructureCacheException 
{
	public $dir;
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ModelStructureCacheDirNotFoundException extends ModelStructureCacheDirException 
{
	public function __construct($dir) 
	{
		$this->dir = $dir;
		parent::__construct('Model cache directory could not be found \''.$dir.'\'.');
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class ModelStructureCacheDirNotWritableException extends ModelStructureCacheDirException 
{
	public function __construct($dir) 
	{
		$this->dir = $dir;
		parent::__construct('Model cache directory is not writable \''.$dir.'\'.');
	}
}