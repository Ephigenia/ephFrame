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
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

/**
 * Optparse
 * 
 * Optparser for PHP
 * 
 * This class should or maybe is the optparse conversion of the well known
 * Optparser from the other famous programming languages such as python.
 * 
 * [here comes more docu soon, as for now, check the test.php file for an
 * exmple]
 * 
 * // todo split the parser from the options
 * // todo implement simple error checking on arguments
 * // todo implement custom error checking on arguments (or in ConsoleApp?)
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 19.07.2008
 */
class OptParse extends Object
{	
	const TYPE_CALLBACK = 'callback';
	const TYPE_ARRAY 	= 'array';
	const TYPE_BOOL 	= 'boolean';
	const TYPE_STRING 	= 'string';
	const TYPE_INTEGER	= 'integer';
	const TYPE_FLOAT	= 'float';
	
	public $validTypes = array(
		self::TYPE_ARRAY,
		self::TYPE_BOOL,
		self::TYPE_CALLBACK,
		self::TYPE_FLOAT,
		self::TYPE_INTEGER,
		self::TYPE_STRING
	);
	
	/**
	 * @var string
	 */
	protected $longArgRegExp = '/^-{1,2}([a-zA-z0-9]+)=?(.+)?$/';
	
	/**
	 * @var string
	 */
	protected $shortArgRegExp = '/^[-\/]([a-zA-z0-9]+)$/';
	
	/**
	 * Stores the raw arguments
	 * @var array(string)
	 */
	protected $rawArgs;
	
	/**
	 * Configuration for possible arguments and parameters this OptParse knows
	 * and can parse.
	 * @var array(string)
	 */
	public $config = array(
		// the help option should always be there
		array(
			'short' => 'h',
			'long' 	=> 'help',
			'help' 	=> 'display usage message and exit',
			'dest' 	=> 'help',
			'type' 	=> self::TYPE_BOOL,
			'store'	=> true
		)
	);
	
	/**
	 * Stores the string that is printed after 'Usage: '... so this might be
     * something like: '[options] [path] [url]
	 * @var string
	 */
	public $usage = '[options]';
	
	/**
	 * Stores the parsed options
	 * @var array(string)
	 */
	public $options = array();
	
	/**
	 * Stores the parsed arguments (everything that did not match in the options)
	 * @var array(string)
	 */
	public $arguments = array();
	
	/**
	 * OptParse constructor
	 * @param array(string)
	 * @return OptParse
	 */
	public function __construct(Array $args = array()) {
		if (func_num_args() == 1) {
			$this->rawArgs = $args;
		} else {
			global $argv;
			$this->rawArgs = array_slice($argv, 1);
		}
		// merge config with parent class configs
		$this->parse();
		return $this;
	}
	
	/**
	 * Adds an other option info config array to the {@link config}.
	 * The type of the option is validated against {@link validTypes} array.
	 * @var array(string)
	 * @return OptParse
	 */
	public function addOption(Array $optionInfo) {
		if (isset($optionInfo) && !in_array($optionInfo['type'], $this->validTypes)) {
			throw new OptParseInvalidTypeException($this, $optionInfo['type']);
		}
		$this->config[] = $optionInfo;
		return $this;
	}
	
	private function renderOptUsage(Array $optionInfo, $width = 80) {
		$optionValue = '';
		if (isset($optionInfo['dest'])
			&& isset($optionInfo['type'])
			&& $optionInfo['type'] != self::TYPE_BOOL)
			{
			$optionValue .= ' '.strtoupper($optionInfo['dest']);
		}
		// collect short and long options
		$argList = array();
		if (!empty($optionInfo['short'])) {
			$argList[] = '-'.$optionInfo['short'].$optionValue;
		}
		if (!empty($optionInfo['long'])) {
			$argList[] = '--'.$optionInfo['long'].$optionValue;
		}
		$argListRendered = '  '.implode(', ',$argList);
		$usage = $argListRendered;
		// add description to argument hint if set
		if (isset($optionInfo['help'])) {
			// if the usage parameters is to long, put help to the next line
			if (strlen($usage) > 22) {
				$usage .= LF;
				$helpMessage = wordwrap($optionInfo['help'], $width - 24 - 2, LF, true);
				$usage .= String::indent($helpMessage, 24, ' ');
			// or add the parameter help message in the next line, indented
			} else {
				$usage .= str_repeat(' ', 24 - strlen($usage));
				$m = wordwrap($optionInfo['help'], $width - 24, LF, true);
				$a = explode(LF, $m);
				$usage .= array_shift($a);
				if (count($a) > 0) {
					$usage .= LF;
				}
				$usage .= String::indent(implode(LF, $a), 24, ' ');
			}
		}
		return $usage;
	}
	
	/**
	 * Returns a usage Message for this ArgParser Options
	 * @param integer $width
	 */
	public function usage($width = 80) {
		$r = 'Usage: '.basename($_SERVER['PHP_SELF']);
		if ($this->usage) {
			$r .= ' '.$this->usage;
		}
		$r .= LF;
		if (count($this->config) > 0) {
			$r .= LF.'Options:'.LF;
			foreach($this->config as $index => $optionInfo) {
				$r .= $this->renderOptUsage($optionInfo, $width).LF;
			}
		}
		return $r;
	}
	
	/**
	 * Parses the arguments and returns an array of options and arguments
	 * @return array(mixed)
	 */
	public function parse() {
		// parse all arguments from the rawArguments array
		for($i = 0; $i < count($this->rawArgs); $i++) {
			// --[argumentname]=[value]
			if (preg_match($this->longArgRegExp, $this->rawArgs[$i], $found)) {
				$this->setOption($found[1], @$found[2]);
			// [-|/][argumentname] [value]
			} elseif (preg_match($this->shortArgRegExp, $this->rawArgs[$i], $found)) {
				$this->setOption($found[1], $found[2]);
			// -/ [optioname] value <-- value is the match
			// at least if [optionname] is no boolean
			} elseif (
				isset($this->rawArgs[$i-1])
				&& isset($this->rawArgs[$i])
				&& preg_match($this->shortArgRegExp, $this->rawArgs[$i-1])
				&& !preg_match($this->longArgRegExp, $this->rawArgs[$i])
				&& !preg_match($this->shortArgRegExp, $this->rawArgs[$i]))
				{
				// get name of last short arg
				preg_match($this->shortArgRegExp, $this->rawArgs[$i-1], $found);
				$lastShortArg = $found[1];
				$optionInfo = $this->getOptionDefinition($lastShortArg);
				if (isset($optionInfo['type']) && $optionInfo['type'] == self::TYPE_BOOL) {
					$this->arguments[] = $this->rawArgs[$i];
				} else {
					$this->setOption($lastShortArg, $this->rawArgs[$i]);
				}
			// test.php test <-- test would be the match
			} else {
				$this->arguments[] = $this->rawArgs[$i];
			}
		}
		// fill up arguments array with options that are not passed, but have
		// a default value or values that have short and long names and need
		// to get syncronized (this is kind of crap shit implementation i know)
		foreach($this->config as $index => $data) {
			if (!isset($data['default'])) {
				continue;
			}
			if (isset($data['short']) && !isset($this->options[$data['short']])) {
				if (isset($data['long']) && isset($this->options[$data['long']])) {
					$this->setOption($data['short'], $this->options[$data[$long]]);
				} else {
					$this->setOption($data['short'], $data['default']);
				}
			}
			if (isset($data['long']) && !isset($this->options[$data['long']])) {
				if (isset($data['short']) && isset($this->options[$data['short']])) {
					$this->setOption($data['long'], $this->options[$data['short']]);
				} else {
					$this->setOption($data['long'], $data['default']);
				}
			}
		}
		return array($this->options, $this->arguments);
	}
	
	private function getOptionDefinition($name) {
		foreach($this->config as $i => $optionInfo) {
			if ((is_array($optionInfo['short']) && in_array($name, $optionInfo['short']))
				|| (is_array($optionInfo['long']) && in_array($name, $optionInfo['long']))
				|| (in_array($name, array($optionInfo['short'], $optionInfo['long'])))
				) {
				return $optionInfo;
			}
		}
		return false;
	}
	
	public function setOption($name, $value = null) {
		// try to find the arguments name in the options array
		if ($optionInfo = $this->getOptionDefinition($name)) {
			// special type of args, defined in the {@link options}
			switch(isset($optionInfo['type']) ? $optionInfo['type'] : false) {
				// callbacks
				case self::TYPE_CALLBACK:
					if (!empty($optionInfo['callback'])) {
						$methodName = $optionInfo['callback'];
					} elseif (!empty($optionInfo['dest'])) {
						$methodName = $optionInfo['dest'];
					} elseif (!empty($optionInfo['long'])) {
						$methodName = $optionInfo['long'];
					} else {
						user_error('Undefined callback name for option.', E_USER_ERROR);
					}
					return $this->$methodName($name, $value);
				// arrays
				case self::TYPE_ARRAY:
					if (!empty($value)) {
						if (!isset($this->arguments[$name])) {
							$this->options[$name] = array($value);
						} else {
							$this->options[$name][] = $value;
						}
					}
					return $this;
				// boolean and or flags
				case self::TYPE_BOOL:
					// options with default value and no storage directive are set their default
					/*if ($value == null && !isset($optionInfo['store']) && isset($optionInfo['default'])) {
						$value = $optionInfo['default'];
					} else*/
					if (isset($optionInfo['store']) && $value == null) {
						$value = $optionInfo['store'];
					}
					if (in_array($value, array('true', 'yes', 'ja', 'y', 'j', 't', true, null), true)) {
						$value = true;
					} elseif (in_array($value, array('false', 'no', 'nein', 'n', 'f', false), true)) {
						$value = false;
					}
					break;
				// strings
				case self::TYPE_STRING:
					$value = strval($value);
					break;
				// integers
				case self::TYPE_INTEGER:
					$value = intval($value);
					break;
				// floats and doubles
				case self::TYPE_FLOAT:
					$value = floatval($value);
					break;
			}
			if (isset($optionInfo['dest'])) {
				$name = $optionInfo['dest'];
			}
		// auto convert value to matching type (int/float)
		} else {
			// possibly hit a flag
			if (strlen($value) == 0) {
				$value = true;
			// integer values and also boolean values will be the same
			} else if (preg_match('/^[-+]?(\s+)?\d+/', $value)) {
				$value = (int) $value;
			// float values (. as devider)
			} elseif (preg_match('/^[-+]?(\s+)?\d+(.\d+)?$/', $value)) {
				$value = (float) $value;
			}
		}
		$this->options[$name] = $value;
		return $this;
	}
	
	/**
	 * Flushes the info stored in the ArgParser
	 * @return ArgParser
	 */
	public function flush() {
		$this->arguments = array();
		$this->rawArgs = array();
		return $this;
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class OptParseException extends Exception {}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class OptParseInvalidTypeException extends OptParseException {
	public function __construct(OptParse $optParse, $optionType) {
		$message = sprintf('Invalid type of option passed in option info array. \'%s\' is not a valid OptParse option type', $optionType);
		parent::__construct($message);
	}
}