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

ephFrame::loadClass('ephFrame.lib.File');

/**
 *	CSV File
 *
 * 	Multipurpose CSV Class, which makes it easy to import / export Data as
 * 	Arrays from CSV Files.
 *
 * 	Loading a CSV File and returning content
 * 	<code>
 * 	$csvFile = new CSV("csvexample.csv");
 * 	echo '<table border="0">';
 * 	foreach ($csvFile->getContent() as $Row) {
 * 		echo "<tr>\n";
 * 		foreach ($Row as $CellValue) {
 * 			echo "<td>".$CellValue."</td>";
 * 		}
 * 		echo "</tr>\n";
 * 	}
 * 	</code>
 *
 * 	Exporting to a CSV File, for example, a list of entries in a project
 * 	<code>
 * 	$csvFile = new CSV();
 * 	foreach ($Projects->asArray() as $entry) {
 * 		// $entry is an array here
 * 		// $entry["time"]
 * 		// $entry["text"];
 * 		$csv->addRow($entry);
 * 	}
 * 	echo $csv->toCSV();
 * 	</code>
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 19.05.2007
 * 	@version 0.2
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class CSV extends File implements Renderable, Iterator, Countable {

	/**
	 *	CSV File Content, Don't touch
	 *	@var array(array(string))
	 */
	protected $tableData = array();
	
	/**
	 *	Row Seperators
	 *	@var array
	 */
	private $seperators = array(';', ',', '|');
	
	/**
	 * 	Stores the regular expression for matching lines for
	 * 	caching purposes
	 * 	@var string
	 */
	protected $lineRegExp;
	
	/**
	 *	Iterator index
	 * 	@var integer
	 */
	protected $iterartorPosition;

	/**
	 *	CSV File Constructer
	 *	@param string		$filename
	 *	@param string|array	$seperator
	 */
	public function __construct($filename = null, $seperator = null) {
		parent::__construct($filename);
		$this->seperator($seperator);
		$this->recreateLineRegExp();
	}

	/**
	 *	Sets or returns the current row seperator for this csv file
	 *	@param array|string
	 * 	@return array
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
	 * 	re-creates the line regexp for caching
	 * 	@return string
	 */
	protected function recreateLineRegExp() {
		$seperatorRexExp = implode($this->seperators);
		$this->lineRegExp = str_replace('%s', $seperatorRexExp, '/['.$seperatorRexExp.']?(?:(?:"((?:[^"]|"")*)")|([^'.$seperatorRexExp.']*))/');
	}

	/**
	 *	Unencoded a Column from csv
	 *	@param string	$column
	 *	@return string
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
	 *	Encodes a Coloumn for CSV
	 *	@param string	$column
	 *	@return string
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
	 *	Parses a CSV Line and splits and decoded the coloumns
	 *	@return array(string)
	 */
	protected function parseLine($inLine) {
		// split line using regular expression
		preg_match_all($this->lineRegExp, $inLine, $foundColumns);
		if (is_array($foundColumns)) {
			// trim seperators at beginning and end of values
			foreach ($foundColumns[0] as $columnNo => $value) {
				$foundColumns[0][$columnNo] = $this->unencodeColumn($value);
			}
			return $foundColumns[0];
		} else {
			return array();
		}
	}

	/**
	 * 	Returns an array of all lines and rows in this csv-file.
	 * 	@return array(array(string))
	 */
	public function read() {
		if (empty($this->tableData)) {
			$content = parent::read();
			foreach ($content as $line) {
				$lineArr = $this->parseLine($line);
				if (!empty($lineArr)) $this->addRow($lineArr);
			}
		}
		return $this->tableData;
	}
	
	/**
	 * 	{@link render} callback
	 * 	@return boolean
	 */
	private function beforeRender() {
		return true;
	}
	
	/**
	 *	{@link render} callback
	 * 	@param string $rendered
	 * 	@return string
	 */
	private function afterRender($rendered) {
		return $rendered;
	}

	/**
	 *	Renders the CSV Table from the data in the {@link tableData} array
	 * 	and returns the rendered string. The Seperator that is used is
	 * 	the first from the {@link seperators} array
	 *	@return string
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

	/**
	 *	Sends and returns a header for a csv file with
	 * 	optional download headers.
	 * 	Specify a filename for the downloaded file if you want
	 * 	@param string	$downloadFileName
	 * 	@return string
	 */
	public function header($downloadFileName = false) {
		$header = array(MimeTypes::mimeType('csv'));
		if ($downloadFileName !== false) {
			$header[] = 'Content-Disposition: attachment; filename="'.(($downloadFileName === true) ? $this->filename : $downloadFileName).'"';
		}
		foreach ($header as $line) {
			header($line);
		}
		return implode("\n",$header);
	}

	/**
	 *	Adds a nother Line to CSV
	 *	@param array|string 	$arr
	 */
	public function addRow($arr) {
		if (is_string($arr)) $arr = array($arr);
		array_push($this->tableData, $arr);
		return $this;
	}

	/**
	 * 	Overwrites File Class Defined append Function
	 * 	@see addRow
	 */
	public function append($arr) {
		$this->addRow($arr);
	}

	/**
	 *	Saves the contents to the filename set
	 */
	public function save() {
		return parent::write($this->render());
	}

	/**
	 *	Saves conents in the given File
	 *	@param string	$filename
	 */
	public function saveAs($filename) {
		$newFile = new File($filename);
		$newFile->write($this->render());
		$this->filename = $filename;
	}
	
	public function count() {
		if (empty($this->tableData)) $this->read();
		return count($this->tableData);
	}

	public function rewind() {
		$this->iteratorPosition = 0;
		if (empty($this->tableData)) $this->read();
		reset($this->tableData);
		return true;
	}

	public function next() {
		if (empty($this->tableData)) $this->read();
		$this->iteratorPosition++;
		next($this->tableData);
	}

	public function key() {
		if (empty($this->tableData)) $this->read();
		return key($this->tableData);
	}

	public function current() {
		if (empty($this->tableData)) $this->read();
		return current($this->tableData);
	}

	public function valid() {
		return ($this->iteratorPosition < count($this));
	}

}

/**
 *	@package ephFrame
 *	@subpackage ephFrame.lib.exception
 */
class CSVException extends BasicException {}

?>