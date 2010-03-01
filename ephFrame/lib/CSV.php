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

class_exists('File') or require dirname(__FILE__).'/File.php';

/**
 * CSV File
 *
 * Multipurpose CSV Class, which makes it easy to import / export Data as
 * Arrays from CSV Files.
 *
 * Loading a CSV File and returning content
 * <code>
 * $csvFile = new CSV("csvexample.csv");
 * echo '<table border="0">';
 * foreach ($csvFile->getContent() as $Row) {
 * 	echo "<tr>\n";
 * 	foreach ($Row as $CellValue) {
 * 		echo "<td>".$CellValue."</td>";
 * 	}
 * 	echo "</tr>\n";
 * }
 * </code>
 *
 * Exporting to a CSV File, for example, a list of entries in a project
 * <code>
 * $csvFile = new CSV();
 * foreach ($Projects->asArray() as $entry) {
 * 	// $entry is an array here
 * 	// $entry["time"]
 * 	// $entry["text"];
 * 	$csv->addRow($entry);
 * }
 * echo $csv->toString();
 * </code>
 * 
 * @todo add multiline-column support
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 19.05.2007
 * @version 0.2
 * @package ephFrame
 * @subpackage ephFrame.lib
 */
class CSV extends File implements Renderable, Iterator, Countable {

	/**
	 * CSV File Content, Don't touch
	 * @var array(array(string))
	 */
	protected $data = array();
	
	/**
	 * Row Seperators
	 * @var array
	 */
	private $seperators = array(';', ',', '|');
	
	/**
	 * Stores the regular expression for matching lines for
	 * caching purposes
	 * @var string
	 */
	protected $lineRegExp;
	
	/**
	 * Iterator index
	 * @var integer
	 */
	protected $iterartorPosition;

	/**
	 * CSV File Constructer
	 * @param string|array $filename
	 * @param string|array	$seperator
	 * @return CSV
	 */
	public function __construct($filename = null, $seperator = null) {
		$this->recreateLineRegExp();
		if (is_array($filename)) {
			$this->data = $filename;
		} elseif (is_object($filename) && is_a($filename, 'Set')) {
			$this->data = $filename->toArray();
		} elseif (!empty($filename)) {
			parent::__construct($filename);
			if (parent::exists()) {
				$this->data = $this->toArray();
			}
		}
		$this->seperator($seperator);
		return $this;
	}

	/**
	 * Sets or returns the current row seperator for this csv file
	 * @param array|string
	 * @return array
	 */
	public function seperator($seperator = null) {
		if (func_num_args() == 0) return $this->seperators;
		if (func_num_args() > 1) {
			$args = func_get_args();
			$this->seperators = $args;
		} elseif (is_array($seperator)) {
			foreach(func_get_args() as $arg) $this->seperator($arg);
		} elseif (!in_array($seperator, $this->seperators)) {
			$this->seperators[] = $seperator;
		}
		$this->recreateLineRegExp();
		return $this;
	}

	/**
	 * re-creates the line regexp for caching
	 * @return string
	 */
	protected function recreateLineRegExp() {
		$seperatorRexExp = implode($this->seperators);
		$this->lineRegExp = str_replace('%s', $seperatorRexExp, '/['.$seperatorRexExp.']?(?:(?:"((?:[^"]|"")*)")|([^'.$seperatorRexExp.']*))/');
	}

	/**
	 * Unencoded a Column from csv
	 * @param string	$column
	 * @return string
	 */
	protected function unencodeColumn($column) {
		$value = $column;
		if (in_array(substr($value,0,1), $this->seperators)) $value = substr($value,1);
		if (in_array(substr($value,-1,1), $this->seperators)) $value = substr($value,0,-1);
		if (substr($value,0,1) == '"' || substr($value,0,1) == "'") $value = substr($value,1);
		if (substr($value,-1,1) == '"' || substr($value,-1,1) == "'") $value = substr($value,0,-1);
		$value = stripslashes($value);
		return ($value);
	}

	/**
	 * Encodes a single coloumn for CSV
	 * @param string	$column
	 * @return string
	 */
	protected function encodeColumn($column) {
		$encoded = $column;
		// has quotes?
		if (preg_match('/"|\n/', $encoded)) {
			$encoded = str_replace('"','""',$encoded);
		}
		// add quotes if seperator in string
		if (preg_match('/'.implode('|',$this->seperators).'/', $encoded)) {
			$encoded = sprintf('"%s"', $encoded);
		}
		return $encoded;
	}

	/**
	 * Parses a line from csv line and returns the parsed data array
	 * @return array(string)
	 */
	protected function parseLine($raw) {
		if (!is_string($raw)) return false;
		// split line using regular expression
		if (!preg_match_all($this->lineRegExp, $raw, $foundColumns)) {
			return false;
		}
		// trim seperators at beginning and end of values
		foreach ($foundColumns[0] as $columnNo => $value) {
			$foundColumns[0][$columnNo] = $this->unencodeColumn($value);
		}
		return $foundColumns[0];
	}

	/**
	 * Returns one line of parsed csv data as an array or false if the end
	 * is reached
	 * @return array(array(string))
	 */
	public function read() {
		return $this->parseLine(parent::read());
	}
	
	public function toArray() {
		$r = array();
		while($d = $this->read()) {
			$r[] = $d;
		}
		return $r;
	}
	
	public function toCSV() {
		return $this->render();
	}
	
	/**
	 * {@link render} callback
	 * @return boolean
	 */
	public function beforeRender() {
		return true;
	}
	
	/**
	 * {@link render} callback
	 * @param string $rendered
	 * @return string
	 */
	public function afterRender($rendered) {
		return $rendered;
	}

	/**
	 * Renders the CSV Table from the data in the {@link tableData} array
	 * and returns the rendered string. The Seperator that is used is
	 * the first from the {@link seperators} array
	 * @return string
	 */
	public function render() {
		$rendered = '';
		if (!$this->beforeRender()) return $rendered;
		$firstSeperator = reset($this->seperators);
		foreach ($this as $row) {
			$encodedRow = array();
			foreach ($row as $value) {
				$encodedRow[] = $this->encodeColumn($value);
			}
			$rendered .= implode($firstSeperator, $encodedRow).RT.LF;
		}
		$rendered = substr($rendered, 0, -2); // strip last Line breaks
		return $this->afterRender($rendered);
	}
	
	public function __toString() {
		return $this->render();
	}
		
	public function addRow($args) {
		if (func_num_args() > 1) {
			$args = func_get_args();
		}
		if (!is_array($args)) {
			$args = array($args);
		}
		$this->data[] = $args;
		return $this;
	}

	public function append($string) {
		$args = func_get_args();
		return $this->callMethod('addRow', $args);
	}

	/**
	 * Saves the contents to the filename set
	 */
	public function save() {
		return parent::write($this->render());
	}

	/**
	 * Saves conents in the given File
	 * @param string	$filename
	 */
	public function saveAs($filename) {
		$classname = get_class($this);
		$newFile = new $classname($filename);
		$newFile->write($this->render());
		return $newFile;
	}
	
	/**
	 * Returns the number of lines 
	 * @return integer
	 */
	public function count() {
		// if (empty($this->data)) $this->read();
		return count($this->data);
	}

	public function rewind() {
		$this->iteratorPosition = 0;
		// if (empty($this->data)) $this->read();
		reset($this->data);
		return true;
	}

	public function next() {
		// if (empty($this->data)) $this->read();
		$this->iteratorPosition++;
		next($this->data);
	}

	public function key() {
		// if (empty($this->data)) $this->read();
		return key($this->data);
	}

	public function current() {
		if (empty($this->data)) return false;
		return current($this->data);
	}

	public function valid() {
		return ($this->iteratorPosition < count($this));
	}

}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class CSVException extends BasicException {}