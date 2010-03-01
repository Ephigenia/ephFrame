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
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

/**
 * XML File/Node Class
 * 
 * ! THIS CLASS IS NOT REALLY COOL; MUST BE REWRITTEN! 
 * // todo remove upper comment if class is rewritten!
 * // todo rewrite this class as soon as possible
 * 
 * This Class is an XML Node or a whole XML File (cause an xml file is a node with lots subnodes)
 * I wrote this class because I didn't like the minixml integration or the DOM Model ;-)
 *
 * This class works with simplexml from php5, but could be enhanced (even for php4 support)
 * by using an own xml parser. Maybe in the future ...
 *
 * Simply create a File from an array
 * <code>
 * $fileArray = array(
 * 	"name"	=> "root",
 * 	"attributes" => array("cubes" => "many"),
 * 	"colours" => array(
 * 		"color" => "yellow",
 * 		"color" => "green"
 * 	);
 * );
 * $xml = new XML($fileArray);
 * $xml->saveTo("myBalls.xml");
 * </code>
 *
 * Read a file into an array
 * <code>
 * $xml = new XML("myBalls.xml");
 * echo var_dump($xml);
 * </code>
 *
 * Iterate through subnodes
 * <code>
 * foreach ($xml->nodes() as $childnode) {
 * 	echo $childnode->name();
 * }
 * </code>
 *
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @author Marcel Eichner // Ephigenia <marcel.eichner@elementar.net>
 * @since 24.08.2006
 * @uses File
 * @package File
 */
class XML extends File {

	public $filename;

	/**
	 * Properties for XML acting as Node
	 */
	public $depth = 0;
	public $value;
	public $nodeName;
	public $parent;
	public $nodes = array();
	public $attributes = array();
	public $encoding = "utf-8";

	/**
	 * XML File/Node Constructor
	 * Constructs an XML Object by loading a file or parsing an Array
	 *
	 * @param array|string		String: Filename of file to load, Array: Array which to parse
	 * @return XML	Instance of Object itsself
	 */
	public function __construct($filenameOrArray = null, $encoding = null) {
		$this->encoding($encoding);
		if ($filenameOrArray !== null) {
			if (is_string($filenameOrArray)) {
				$this->filename($filenameOrArray);
				$this->loadFile($filenameOrArray);
			}
			if (is_array($filenameOrArray)) {
				$this->fromArray($filenameOrArray);
			}
		}
		return $this;
	}

	public function __get($nodename) {
		foreach ($this->nodes() as $Node) {
			if ($Node->nodeName == $nodename) return $Node;
		}
		return null;
	}

	/**
	 * Flushes XML Node, deletes all connection to subnodes, attributes and resets the
	 * Object
	 * @return XML
	 */
	public function flush() {
		$this->nodes = array();
		$this->attributes = array();
		$this->depth = 0;
		return $this;
	}

	/**
	 * Saves XML Node Content (with all subnodes/attributes) to a file,
	 * if no filename is given it tries to save in the previosly loaded file
	 * @param string	$filename	File to save to
	 * @return Saving Result
	 * @throws XMLFileNameMissingException
	 */
	public function save($filename = null) {
		if ($filename === null) {
			if (empty($this->filename)) throw new XMLFileNameMissingException();
			$filename = $this->filename;
		}
		return $this->saveTo($filename);
	}

	/**
	 * Saves XML Node Content in a File
	 * @param string	$filename	Filename to save to
	 * @return Boolean
	 */
	public function saveTo($filename) {
		if (empty($filename)) throw new XMLFileNameMissingException();
		$this->filename($filename);
		$this->create();
		$fp = fopen($filename, "w");
		fwrite($fp,"<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n");
		fwrite($fp, $this->toString());
		fclose($fp);
		@chmod($fp,0777);
		return true;
	}

	/**
	 * Loads XML File from filename set previosly by loading or using
	 * {@link filename} function
	 * @return Boolean
	 * @throws XMLFileNameMissingException
	 */
	public function load() {
		if (empty($this->filename)) throw new XMLFileNameMissingException();
		return $this->fromFile($this->filename);
	}

	/**
	 * Load XML File from given Filename
	 * @param string filename
	 * @return Boolean
	 * @throws XMLFileNameMissingException
	 */
	public function loadFile($filename) {
		if (empty($filename)) throw new XMLFileNameMissingException();
		return $this->fromFile($filename);
	}

	/**
	 * Sets filename to save to or load from (doesn't change the filename
	 * on harddisk!, use rename for that)
	 * @param string	$filename
	 * @return string
	 */
	public function filename($filename = null) {
		if ($filename !== null) { $this->filename = $filename; return $this; }
		return $this->filename;
	}

	/**
	 * Loads XML Content from a Filea and parses it
	 * @param string	$filename
	 * @throws ephFrameWrongTypeException
	 * @return Boolean
	 */
	public function fromFile($filename) {
		if (empty($filename) || !is_string($filename)) { throw new ephFrameWrongTypeException($filename,"string"); }
		// read file contents
		if (!file_exists($filename) || (file_exists($filename) && !is_readable($filename))) error_log ("File not found".$filename,0);
		$this->filename = $filename;
		$xml = simplexml_load_file($filename);
		$this->addSimpleXMLNode($xml);
		return true;
	}

	private function addSimpleXMLNode($simpleXMLNode, $depth = 0) {
		foreach ($simpleXMLNode->children() as $nodeName => $child) {
			$newChildNode = new XML();
			$newChildNode->encoding($this->encoding);
			$newChildNode->nodeName((string)$nodeName);
			$newChildNode->setAttributes($child->attributes(),true);
			$newChildNode->value($this->decode(trim($child)));
			$this->addNode($newChildNode);
			// parse subnodes
			$newChildNode->addSimpleXMLNode($child, $depth+1);
		}
	}

	/**
	 * Parses an Array to this node, no nodes will be deleted before importing,
	 * unless you set flush to true
	 *
	 * <code>
	 * 	$xmlArray = array(
	 * 		"img" => array(
	 * 				"attributes" => array("src","img/hello.jpg", "border" => 0)
	 * 			),
	 * 		"p"	=> array(
	 * 			"value"	=> "this is a text"
	 * 		)
	 * 	);
	 * 	$xml = new XML();
	 * 	$xml->fromArray($xmlArray);
	 * 	echo $xml->toString();
	 * 	// will give you
	 * 	<img src="img/hello.jpg" border="0" /><p>this is a text</p>
	 * </code>
	 */
	public function fromArray($array, $depth = 0, $flush = false) {
		if ($flush === true) $this->flush();
		$this->depth = $depth;
		if ($this->depth == 0 && count($array) == 1) {
			list($this->nodeName) = array_keys($array);
			$array = reset($array);
		}
		if (!is_array($array)) return true;
		foreach ($array as $keyName => $value) {
			if (is_array($value)) {
				if ($keyName === "attributes") {
					$this->setAttributes($array["attributes"]);
				} else {
					$childnode = new XML();
					$childnode->encoding($this->encoding);
					$sum = array_sum(array_keys($value));
					if ($sum == 0) {
						$childnode->nodeName($keyName);
						$childnode->fromArray($value, $depth+1);
						$this->addNode($childnode);
					} else {
						foreach ($value as $k => $v) {
							$childnode = new XML();
							$childnode->encoding($this->encoding);
							$childnode->nodeName($k);
							$childnode->fromArray($v,$depth+1);
							$this->addNode($childnode);
						}
					}
				}
			} else {
				if ($keyName == "nodeName") {
					$this->nodeName = $value;
				} elseif ($keyName == "value") {
					$this->value = $value;
				} else {
					$childnode = new XML();
					$childnode->encoding($this->encoding);
					$childnode->nodeName = $keyName;
					$childnode->value = $value;
					$this->addNode($childnode);
				}
			}
		}
		return true;
	}

	/**
	 * Returns XML Node Content as an Array, if there are multiple tags
	 * with the same name they will be returned as a subarray. See example
	 * for further description
	 *
	 */
	public function toArray($depth = 0) {
		$return = array(
		"nodeName"		=> $this->nodeName,
		"attributes"	=> $this->attributes,
		"value"			=> $this->value
		);
		$keyNamesWithArrayNeeded = array();
		foreach ($this->nodes() as $childNode) {
			$keyName = $childNode->nodeName();
			//$return[$keyName][] = $childNode->toArray($depth + 1);
			if (!in_array($keyName, $keyNamesWithArrayNeeded)) {
				if (isset($return[$keyName])) {
					$keyNamesWithArrayNeeded[] = $keyName;
					$old = $return[$keyName];
					unset($return[$keyName]);
					$return[$keyName][] = $old;
					$return[$keyName][] = $childNode->toArray($depth+1);
				} else {
					$return[$keyName] = $childNode->toArray($depth+1);
				}
			} else {
				$return[$keyName][] = $childNode->toArray($depth+1);
			}
		}
		return $return;
	}

	/**
	 * Parses String into XML Node Objects
	 * @param string	XML String to Parse
	 */
	public function fromString($string) {
		if (!empty($string)) {
			$xml = simplexml_load_string($string);
			$this->addSimpleXMLNode($xml);
		}
		return true;
	}

	/**
	 * Returns XML Node Content as a String (i.e. XML Code)
	 * @return string
	 */
	public function toString($withXMLOpenTag = false) {
		$nodeString = str_repeat("\t",$this->depth()+1)."<".$this->nodeName();
		if ($withXMLOpenTag) {
			if (!empty($this->encoding)) {
				$nodeString = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n".$nodeString;
			} else {
				$nodeString = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\" ?>\r\n".$nodeString;
			}
		}
		if ($this->hasAttributes()) $nodeString .= " ".$this->attributesString();
		if ($this->hasValue()) {
			// check for if CDData is needed
			if (strpos($this->value(),"&") !== false || strpos($this->value(),">") || strpos($this->value(),"<")) {
				$nodeString .= "><![CDATA[".$this->encode($this->value())."]]>";
			} else {
				$nodeString .= ">".$this->encode($this->value())."";
			}
		}
		if ($this->hasNodes()) {
			if (!$this->hasValue()) {
				$nodeString .= ">\n";
			}
			foreach ($this->nodes() as $childNode) $nodeString .= $childNode->toString();
		}
		if (!$this->hasNodes() && !$this->hasValue()) {
			$nodeString .= " />\n";
		} else {
			if ($this->hasNodes()) $nodeString .= str_repeat("\t",$this->depth()+1);
			$nodeString .= "</".$this->nodeName().">\n";
		}
		return $nodeString;
	}

	/**
	 * PHP5 Magic Function and alias for {@link toString}
	 * @see toString
	 * @return string
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * In the File Manner, this functions gives you the xml sourcecode
	 * @return string
	 */
	public function toXML() {
		return $this->toString();
	}


	/**
	 * Sends common Http header to browser
	 * @param string	$download	Actives additional download headers (for download dialog)
	 * @return string
	 */
	public function header($downloadFileName = false) {
		$headers = array();
		$headers[] = "Content-Type: text/xml";
		if (!empty($this->encoding)) {
			$headers[] = "Content-Encoding: ".$this->encoding;
		}
		if ($downloadFileName) {
			$headers[] = 'Content-Disposition: attachment; filename="'.$downloadFileName.'"';
		}
		foreach ($headers as $value) header($value);
		return implode("\n",$headers);
	}

	/**
	 * Returns parent Node if there is one or sets the parent Node
	 * @param XML	$parent If Passed set to parent of this node
	 * @return XML
	 */
	public function parent($parent = null) {
		if ($parent !== null) { $this->parent = $parent; return $this; }
		return ($this->parent);
	}

	/**
	 * Tests wheter this Node has a parent or not, root nodes
	 * have no parents
	 * @return boolean
	 */
	public function hasParent() {
		return (!empty($this->parent));
	}

	/**
	 * Returns or sets Node Name
	 * @param string
	 * @return string Name of this Node
	 */
	public function nodeName($nodeName = null) {
		if ($nodeName !== null) { $this->nodeName = $nodeName; return $this; }
		if (empty($this->nodeName) && $this->depth == 0) $this->nodeName = "root";
		return $this->nodeName;
	}

	/**
	 * Returns value of Node, the stuff between the < >
	 * @param string	$value
	 * @return string
	 */
	public function value($value = null) {
		if ($value !== null) return $this->setValue($value);
		return $this->getValue();
	}

	/**
	 * part of {@link value}
	 * @param string
	 * @return XML
	 */
	public function setValue($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * part of {@link value}
	 * @param string
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Tests wheter this Node has any value set or not
	 * @return boolean
	 */
	public function hasValue() {
		return strlen($this->value);
	}

	/**
	 * Returns node depth
	 * @return integer Depth of the node
	 */
	public function depth($depth = null) {
		if ($depth !== null) { $this->depth = $depth; return $this; }
		return $this->depth;
	}

	/**
	 * Sets or returns current encoding
	 * @return string
	 * @param string	$encoding
	 */
	public function encoding($encoding = null) {
		if (func_num_args() > 0 && $encoding !== null) {
			$this->encoding = strtolower((string) $encoding);
			return $this;
		}
		return $this->encoding;
	}

	/**
	 * Encodes a string that might have to be encoded -
	 * check encoding you know
	 * @param string	$str
	 * @return string
	 */
	private function encode($str) {
		switch (strtolower($this->encoding)) {
			case "utf-8":
				$str = utf8_encode($str);
				break;
			case "iso-8859-1":
				break;
		}
		return $str;
	}

	/**
	 * Decode a string that might has to be encoded
	 * @param string	$str
	 * @return string
	 */
	private function decode($str) {
		switch (strtolower($this->encoding)) {
			case "utf-8":
				$str = utf8_decode($str);
				break;
			case "iso-8859-1":
				break;
		}
		return $str;
	}

	/**
	 * Returns Childnodes
	 * @return array(XML)
	 */
	public function nodes() {
		return $this->nodes;
	}

	/**
	 * Returns first child of this node if there is one
	 * @return node
	 */
	public function firstChild() {
		if (count($this->nodes()) == 0) return null;
		return reset($this->nodes);
	}

	/**
	 * Deletes all Childnodes
	 * @return Boolean true on Success
	 */
	public function deleteNodes() {
		$this->nodes = array();
		return true;
	}

	/**
	 * Returns a Node which is named by $nodename
	 * If node is not found, Null is returned
	 * @param string	$nodename	Nodename to search
	 * @return XML
	 */
	public function node($nodename) {
		return $this->getNode($nodename);
	}

	/**
	 * Adds a childnode
	 * @param XML
	 * @return Boolean
	 */
	public function addNode(&$node) {
		if (is_array($node)) {
			$newNode = new XML($node, $this->encoding);
			return $this->addNode($newNode);
		}
		$node->depth = $this->depth+1;
		$node->parent($this);
		$this->nodes[] = $node;
		return true;
	}

	/**
	 * Deletes a node with the given name
	 * @param string $nodename
	 * @return Boolean
	 */
	public function deleteNode($nodename) {
		if (($deleteNode = $this->getNode($nodename)) !== null) {
			$deleteNode->delete();
		}
		return false;
	}

	/**
	 * Returns a Node which is named by $nodename
	 * If node is not found, Null is returned
	 * @param string	$nodename	Nodename to search
	 * @return XML|null
	 */
	public function getNode($nodename) {
		foreach ($this->nodes() as $node) {
			if ($node->nodeName() == $nodename) return $node;
		}
		return null;
	}

	/**
	 * Returns Number of Childnodes in this Node
	 * @return integer
	 */
	public function nodeCount() {
		return count($this->nodes());
	}

	/**
	 * Tests wheter this Node has any childnodes
	 * @return Boolean
	 */
	public function hasNodes() {
		return (count($this->nodes()) > 0);
	}

	/**
	 * Tests wheter this XML Node has a childnode with
	 * the given Name
	 * @param string	$nodeName	Name of the node to be tested
	 * @return boolean
	 */
	public function hasNode($nodeName) {
		foreach ($this->nodes() as $childNode) {
			if ($childNode->nodeName == $nodeName) return true;
		}
		return false;
	}

	/**
	 * Returns all Attributes as an array
	 * $return[attributeName] = attributeValue
	 * @param string $attributeName	Optional Parameter to return value of specific attribute
	 * @return array(string|integer)
	 */
	public function attributes($attributeName = null) {
		if ($attributeName !== null) {
			return $this->attribute($attributeName);
		}
		return $this->attributes;
	}

	/**
	 * Returns all Attributes as an string of attributes, usefull for html/xml tags
	 * @return string
	 */
	public function attributesString() {
		$return = "";
		foreach ($this->attributes() as $attributeName => $attributeValue) {
			$return .= $attributeName.'="'.$this->encode($attributeValue).'" ';
		}
		return substr($return,0,-1);
	}

	/**
	 * Sets or returns and Attribute
	 * @param string	$name	Name of Attribute to Set
	 * @param mixed	$value	New Value to set, if empty the value will be returned
	 * @return XML
	 */
	public function attribute($name, $value = null) {
		if ($value !== null) return $this->setAttribute($name, $value);
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
		return false;
	}

	/**
	 * Tests wheter this node has an attribute with the given name or not
	 * @param string $name
	 * @return Boolean
	 */
	public function hasAttribute($name) {
		return array_key_exists($name, $this->attributes);
	}

	/**
	 * Sets and Attribute, alias for {@link attribute}
	 * @param string	$name	Name of Attribute to Set
	 * @param mixed	$value	New Value
	 * @return XML
	 */
	public function setAttribute($name, $value) {
		$this->attributes[$name] = $value;
		return $this;
	}

	/**
	 * Sets Multiple Attributes by an Array
	 * <code>
	 * $xmlNode->setAttributes(array("src" => "img/image.jpg", "border" => 0));
	 * </code>
	 *
	 * @param array(string)	$attributesArray
	 * @param Boolean			$decode			utf8_decode array values?
	 * @return XML	Instance of this Node
	 */
	public function setAttributes($attributesArray, $decode = false) {
		foreach ($attributesArray as $attributeName => $attributeValue) {
			if ($decode) $attributeValue = $this->decode($attributeValue);
			$this->setAttribute($attributeName,$attributeValue);
		}
		return $this;
	}

	/**
	 * Returns number of Attributes
	 * @return integer
	 */
	public function attributesCount() {
		return count($this->attributes());
	}

	/**
	 * Returns wheter this node has anny attribute
	 * @return boolean
	 */
	public function hasAttributes() {
		return (count($this->attributes) > 0);
	}

}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class XMLException extends ComponentException {

}
/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class XMLFileNameMissingException extends XMLException {
	public function __construct() {
		$this->message = "No filename set";
		parent::__construct();
	}
}