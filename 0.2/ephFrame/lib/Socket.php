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

/**
 * 	Basic Socket Connection
 * 
 * 	This Class acts as a simple socket. Connecting like telnet. It does know
 * 	nothing about anything about protocols or commands - that's on you to
 * 	implement.
 * 
 * 	<code>
 * 	$socket = new Socket('www.spiegel.de', 80);
 * 	$socket->connect();
 * 	$socket->write('GET /'."\n\n");
 * 	echo $socket->write();
 * 	</code>
 * 
 * 	@author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * 	@since 15.06.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class Socket extends Object {
	
	/**
	 *	Host to connect to
	 * 	@var string
	 */
	protected $host;
	
	/**
	 *	port to use when connecting
	 * 	@var integer
	 */
	protected $port = 80;
	
	/**
	 *	Timeout in seconds when connecting
	 * 	@var integer
	 */
	protected $timeout = 5;
	
	/**
	 *	Buffersize in bytes when reading from open socket with {@link read}
	 * 	reduce this if you want to save up memory on reading action
	 * 	@var integer
	 */
	protected $bufferSize = 4096;
	
	/**
	 * 	Stores the increasing number of bytes send to the open socket
	 * 	@var integer
	 */
	public $bytesSend = 0;
	
	/**
	 * 	Stores the increase number of bytes read from an open socket
	 *	@var integer
	 */
	public $bytesRead = 0;
	
	/**
	 * 	Stores a timestamp of the time the connection was established
	 * 	@var integer
	 */
	public $opened = 0;
	
	/**
	 *	Stores the last command successfully send to the sockets stream
	 * 	@var string
	 */
	public $lastCommand;
	
	/**
	 *	Current Socket Stream when connection is established
	 * 	@var ressource
	 */
	protected $stream = false;
	
	/**
	 *	Stream Constructor
	 * 	@var string $host
	 * 	@var integer $port
	 * 	@var integer $timeout
	 * 	@return Socket
	 */
	public function __construct($host = null, $port = null, $timeout = null) {
		if ($host) $this->host = $host;
		if ($port) $this->port = (int) $port;
		if ($timeout) $this->timeout = (int) $timeout;
		return $this;	
	}
	
	/**
	 *	Write a string to the stream that’s connected.
	 * 	
	 * 	Example, how to send a GET index page (on most servers) command to a
	 * 	socket (allready connected)
	 * 	<code>
	 * 	$socket->send('GET /'."\n\n");
	 * 	</code>
	 * 
	 * 	This will also store the last command successfully send in
	 * 	{@link lastCommand}. Year and it also keeps tracks about the stuff you
	 * 	send to trough the socket by increasing {@link bytesSend} everytime
	 * 	a command get through.
	 * 	
	 * 	@param string $string
	 * 	@throws SocketInvalidCommandException
	 * 	@throws SocketNotConnectedException
	 * 	@return Socket
	 */
	public function write($string) {
		$this->checkConnection();
		if (empty($string)) return $this;
		if (!is_string($string) && !is_numeric($string)) {
			throw new SocketInvalidCommandException($this, $string);
		}
		$strlen = strlen($string);
		fwrite($this->stream, $string, $strlen);
		$this->bytesSend += $strlen;
		$this->lastCommand = $string;
		return $this;
	}
	
	/**
	 *	Read data from the stream
	 * 	
	 * 	You can do very cool stuff with $return as true, like this
	 * 	<code>
	 * 	$response = '';
	 * 	while($chunk = $socket->read(true)) {
	 * 		echo 'bytes read: '.$socket->bytesRead;
	 * 		$response .= $chunk;
	 * 	}
	 * 	echo $response;
	 * 	</code>
	 * 	
	 * 	@param boolean $return set this to true to return after every read cycle
	 * 	@return string
	 */
	public function read($bufferSize = null, $return = false) {
		$this->checkConnection();
		if ($bufferSize == null) {
			$bufferSize = $this->bufferSize;
		}
		$response = '';
		while($chunk = fread($this->stream, $bufferSize)) {
			$response .= $chunk;
			$this->bytesRead += strlen($chunk);
			if ($return) return $chunk;
		}
		return $response;
	}
	
	/**
	 *	Test if a connection is establised, you can also check {@link connected}
	 * 	@return boolean
	 */
	public function connected() {
		return !empty($this->stream);
	}
	
	protected function checkConnection() {
		if (!$this->connected()) {
			throw new SocketNotConnectedException($this);
		}
	}
	
	/**
	 *	Open Socket connection to a $host, on $port and skip connection if
	 * 	$timeout seconds passed without success
	 * 	@return Socket
	 */
	public function connect() {
		if (empty($this->host)) {
			throw new SocketEmptyHostException($this);
		}
		if (empty($this->port)) {
			throw new SocketEmptyPortException($this);
		}
		if ($this->connected()) {
			$this->disconnect();
		}
		if (!($stream = fsockopen($this->host, $this->port, $errorNumber, $errorMessage, $this->timeout))) {
			throw new SocketConnectErrorException($this, $errorNumber, $errorMessage);
		}
		$this->stream = $stream;
		$this->bytesRead = 0;
		$this->bytesSend = 0;
		$this->opened = time();
		$this->afterConnect();
		return $this;
	}
	
	/**
	 *	Callback that can do stuff after a successfull connect.
	 * 	@return mixed
	 */
	public function afterConnect() {
	}
	
	/**
	 *	Close the currently opend stream if Socket is opened
	 * 	@return Socket
	 */
	public function disconnect() {
		if ($this->connected()) {
			fclose($this->stream);
			unset($this->stream);
		}
		return $this;
	}
	
	/**
	 * 	Disconnect on object instance destruction
	 */
	public function __destruct() {
		$this->disconnect();
	}
	
	/**
	 *	Disable cloning of Socket instances
	 */
	final public function __clone() {
		throw new SocketNotClonableException($this);
	}
	
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class SocketException extends Exception {
	public function __construct(Socket $socket) {
		$this->message = 'Socket Error:'.LF.$this->message;
		$socket->disconnect();
		parent::__construct();
	}
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class SocketNotClonableException extends SocketException {
	public function __construct($socket) {
		$this->message = 'Socket connections can not be cloned, sorry.';
		parent::__construct($socket);
	}
}

/**
 * 	Thrown if a Socket connection should be established on an empty host
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class SocketEmptyHostException extends SocketException {
	public function __construct($socket) {
		$this->message = 'Empty host, please specify a host to connect to.';
		parent::__construct($socket);
	}
}

/**
 * 	Thrown if a Socket connection should be established on an empty port
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class SocketEmptyPortException extends SocketException {
	public function __construct($socket) {
		$this->message = 'Empty port, please specify a port to connect to.';
		parent::__construct($socket);
	}
}

/**
 * 	Thrown if a Socket connection should be established on an empty port
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class SocketNotConnectedException extends SocketException {
	public function __construct($socket) {
		$this->message = 'Not Connected';
		parent::__construct();
	}
}

/**
 * 	Thrown if an invalid command is tried to send to the connection of a Socket
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class SocketInvalidCommandException extends SocketException {
	public function __construct($socket, $command) {
		$this->message = 'Invalid Command ('.gettype($command).') \''.$command.'\'';
		parent::__construct($socket);
	}
}

/**
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class SocketConnectErrorException extends SocketException {
	public function __construct($socket, $errno, $errstr) {
		$this->message =
			'Unable to connect to \''.$socket->host.':'.$socket->port.'\''.LF.
			'Socket Error ('.$errno.'): '.$errstr;
		parent::__construct($socket);
	}
}

?>