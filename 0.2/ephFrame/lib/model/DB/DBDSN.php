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

// load parent class URL
ephFrame::loadClass('ephFrame.lib.URL');

/**
 *	Class for Database Source Names
 *
 *	Database Source Names, more commonly seen as the abbreviation, DSN, are
 *	data structures used to describe a connection to a database. This DSN will
 *	take the form of protocol: subprotocol: host: port: database so as to
 *	completely specify all parameters of the connection. The exact format of
 *	the DSN will vary depending on your programming language.
 *
 *	syntax:
 *		[protocol]://[user]:[pass]@[host]:[port]/[dbname](#)[optional charset]
 *
 *	example:
 *		mysql://root:root@localhost:3306/db#utf-8
 * 
 * 	An example for establishing a database connection can be found
 * 	in the documentation of the {@link DBFactory}.
 *
 *	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 *	@since 19.05.2007
 *	@package ephFrame
 *  @version 0.1
 *	@subpackage ephFrame.lib.model.DB
 */
class DBDSN extends URL {
	
	/**
	 *	DBDSN Constructer,
	 * 	pass a string and the DBDSN is imediently parsed
	 * 	@param string
	 * 	@return DBDSN
	 */
	public function __construct($url = null) {
		parent::__construct($url);
		$this->parsedUrl['db'] = null;
		$this->parsedUrl['type'] = null;
		return $this;
	}
	
	/**
	 * 	Sets or Returns Databse Name for DSN
	 * 	@param string
	 * 	@var string
	 */
	function db($db = null) {
		if ($db === null) {
			$path = $this->path();
			return str_replace('/', '', $path);
		}
		$this->path($db);
	}
	
	/**
	 *	Sets or Returns Database Type for DSN
	 *	@var string
	 *	@return string
	 */
	public function type($type = null) {
		if ($type !== null) $this->scheme($type);
		return $this->scheme();
	}
	
	/**
	 * 	Sets or returns the charset property
	 * 	@param string $charset
	 * 	@return string
	 */
	public function charset($charset = null) {
		if ($charset !== null) $this->fragment($charset);
		return $this->fragment();
	}
	
}

?>