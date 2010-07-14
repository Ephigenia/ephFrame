<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Brunnenstr. 10
 *                      10119 Berlin
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
 * Model Field (DB Table column) Information Storage
 * 
 * Stores information about database table column information used in {@link 
 * Model}
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 07.10.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.model
 */
class ModelFieldInfo extends Object 
{
	const QUOTE_STRING = 'string';
	const QUOTE_INTEGER = 'integer';
	const QUOTE_FLOAT = 'float';
	const QUOTE_BOOLEAN	= 'bool';
	
	public $name;
	public $null = false;
	public $primary = false;
	public $type;
	public $length;
	
	/**
	 * Stores the default value for this field if ther’s any
	 * @var mixed
	 */
	public $default;
	
	/**
	 * Stores true if the field is any signed integer or float
	 * @var boolean
	 */
	public $signed = true;
	
	/**
	 * Stores a constant starting with QUOTE_ that indicates the quoting
	 * type for the values of this field
	 * @var string
	 */
	public $quoting = self::QUOTE_STRING;
	
	public $extra;
	
	/**
	 * Stores all options in an array for the ENUM type
	 * @var array(string)
	 */
	public $enumOptions = array();
	
	/**
	 * Stores a map for different field types and their quoting
	 * @var array(string)
	 */
	protected $quoteMap = array(
		self::QUOTE_STRING 	=> array('char', 'varchar', 'blob', 'tinyblob', 'mediumblob', 'text', 'mediumtext', 'tinytext', 'date', 'date', 'time', 'datetime'),
		self::QUOTE_INTEGER	=> array('int', 'bigint', 'timestamp', 'year', 'smallint', 'mediumint', 'bigint'),
		self::QUOTE_FLOAT 	=> array('float', 'double'),
		self::QUOTE_BOOLEAN	=> array('bool') 
	);
	
	public function __construct(Array $columnInfo = array()) 
	{
		if (is_array($columnInfo) && count($columnInfo) > 0) {
			$this->fromColumnInfo($columnInfo);
		}
		return $this;
	}
	
	public function fromColumnInfo(Array $columnInfo) 
	{
		$this->name = $columnInfo['Field'];
		$this->null = (@$columnInfo['Null'] == 'NO') ? false : true;
		$this->primary = (@$columnInfo['Key'] == 'PRI') ? true : false;
		$this->type = $columnInfo['Type'];
		if (!empty($columnInfo['Extra'])) {
			$this->extra = $columnInfo['Extra'];
		}
		// parse type, length and signed
		if (preg_match('@([a-z]+)\s*\((\d+)(?:,(\d+))?\)\s*(unsigned)?@i', $this->type, $found)) {
			// type
			if (!empty($found[1])) {
				foreach($this->quoteMap as $phptype => $matches) {
					if (!in_array(strtolower($found[1]), $matches)) continue;
					$this->quoting = $phptype;
				}
				$this->type = $found[1];
			}
			// length
			if (!empty($found[2])) {
				$this->length = (int) $found[2];
				if (!empty($found[3])) {
					$this->length .= ','.$found[3];
				}
			}
			// signed
			if (!empty($found[4])) {
				$this->signed = false;
			}
		// check for enum
		}
		if (preg_match('@enum\(([^\)]+)\)@i', $this->type, $found)) {
			$enumOptionsRaw = $found[1];
			// split raw enum options and save 'em
			if ($splitted = preg_split('@\',\'@', $enumOptionsRaw)) {
				foreach($splitted as $option) {
					if (substr($option, 0, 1) == '\'') $option = substr($option, 1);
					if (substr($option, -1, 1) == '\'') $option = substr($option, 0, -1);
					$this->enumOptions[] = $option;
				}
			}
			$this->type = 'enum';
			$this->quoting = self::QUOTE_STRING;
		}
		
		// default value
		if (!empty($columnInfo['Default'])) {
			if ($columnInfo['Default'] == 'NULL') {
				$this->default = null;
			} elseif (Validator::float($columnInfo['Default'])) {
				$this->default = (float) $columnInfo['Default'];
			} elseif (Validator::integer($columnInfo['Default'])) {
				$this->default = (int) $columnInfo['Default'];
			}
		}
		return $this;
	}
	
	public function fromJson($data) 
	{
		$this->fromArray(get_object_vars($data));
		unset($data);
		return true;
	}
	
	public function fromArray(Array $data = array()) 
	{
		foreach($data as $key => $value) {
			if (property_exists($this, $key)) {
				$this->{$key} = $value;
			}
		}
		return true;
	}
	
	public function toArray() 
	{
		$r = get_object_vars($this);
		$ignore = array('quoteMap');
		foreach($ignore as $key) {
			unset($r[$key]);
		}
		return $r;
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.exception
 */
class ModelFieldInfoException extends ObjectException 
{}