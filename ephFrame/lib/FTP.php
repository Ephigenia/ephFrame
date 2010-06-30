<?php

/**
 * ephFTPSync: <http://code.ephpigenia.de/projects/ephFTPSync/>
 * Copyright 2007+, Ephigenia M. Eichner, Kopernikusstr. 8, 10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright Copyright 2007+, Ephigenia M. Eichner
 * @link http://code.marceleichner.de/projects/ephFTPSync/
 * @filesource
 */

/**
 * FTP Class
 * 
 * A class that can handle ftp connections, uploads, downloads ...
 * Check the doc strings for different methods for more information
 * 
 * Example that connects to a ftp host and lists all files found
 * <code>
 * $ftp = new FTP('192.168.0.1', 'user', 'pass');
 * $ftp->connect();
 * echo 'Files found in '.$ftp->pwd()."\n";
 * foreach($ftp->ls() as $filename) {
 * 	echo $filename."\n";
 * }
 * </code>
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 22.09.2008
 */
class FTP 
{
	/**
	 * Default transfer mode used when uploading or downloading files
	 * @var integer
	 */
	protected $transfermode = FTP_BINARY;
	
	/**
	 * Default timeout in seconds
	 * @var integer
	 */
	protected $timeout = 30;
	
	/**
	 * Default port which is used while connecting
	 * @var integer
	 */
	protected $port = 21;
	
	/**
	 * Username	
	 * @var string
	 */
	protected $user;
	
	/**
	 * Password
	 * @var string
	 */
	protected $pass;
	
	/**
	 * Host, IP or hostname
	 * @var string
	 */
	protected $host;
	
	/**
	 * Stores the current directory
	 * @var string
	 */
	public $curDir;
	
	/**
	 * Stores the number of bytes uploaded (files), reset on reconnect
	 * @var integer
	 */
	public $bytesUploaded = 0;
	
	/**
	 * Stores number of bytes downloaded (files), reset on reconnect
	 * @var integer
	 */
	public $bytesDownloaded = 0;
	
	/**
	 * Stores the stream ressource when connected
	 * @var ressource
	 */
	protected $stream;
	
	/**
	 * Creates a new FTP Instance
	 * 
	 * @param string $host Hostname or ip to connect to
	 * @param string $user	Username
	 * @param string $pass Password
	 * @param integer $port optional alternate post (default: 21)
	 * @param integer $timeout optional timout in seconds
	 */
	public function __construct($host, $user = null, $pass = null, $port = null, $timeout = null) 
	{
		foreach(array('host', 'user', 'pass', 'port', 'timeout') as $key) {
			if ($$key !== null) $this->{$key} = $$key;
		}
		return $this;
	}
	
	/**
	 * Checks if a connection is established and returns the result true/false
	 * @return boolean
	 */
	public function connected() 
	{
		return !empty($this->stream);
	}
	
	/**
	 * Checks for a connection, if no connection established throws a
	 * {@link FTPNotConnectedException}
	 * @return boolean
	 */
	public function checkConnection() 
	{
		if (!$this->connected()) throw new FTPNotConnectedException($this);
		return true;
	}
	
	/**
	 * Tries to establish a ftp connection to the host.
	 * Host is validated so only found ips for hostnames and ips with hostnames
	 * are valid. {@link afterConnect} is called after a connection was
	 * successfully set up.
	 * @throws FTPInvalidHostException if host is invalid
	 * @throws FTPInvalidHostException
	 * @return FTPConnectionErrorException thrown if connection could not be established
	 */
	public function connect() 
	{
		$this->disconnect();
		// check ip / or host
		if (empty($this->host)) {
			throw new FTPInvalidHostException($this, $this->host);
		} elseif (preg_match('/\\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\b/', $this->host)) {
			if (!@gethostbyaddr($this->host)) {
				throw new FTPInvalidHostException($this, $this->host);
			}
		} elseif (in_array(gethostbyname($this->host), array('', $this->host))) {
			throw new FTPInvalidHostException($this, $this->host);
		}
		// establish connection
		$started = time();
		if (!($this->stream = ftp_connect($this->host, $this->port, $this->timeout))) {
			if (time() - $started >= $this->timeout) {
				throw new FTPConnectionErrorException($this, 'Timeout hit while connecting.');
			} else {
				throw new FTPConnectionErrorException($this);
			}
		}
		$this->afterConnect();
		return $this;
	}
	
	/**
	 * Callback after successfull {@link connect}
	 * @return FTP
	 */
	public function afterConnect() 
	{
		ftp_set_option($this->stream, FTP_TIMEOUT_SEC, $this->timeout);
		$this->login();
		$this->curDir = $this->pwd();
		return $this;
	}
	
	/**
	 * Close the current ftp connection
	 * @return FTP
	 */
	public function disconnect() 
	{
		$this->bytesDownloaded = $this->bytesUploaded = 0;
		if ($this->connected()) {
			ftp_close($this->stream);
			unset($this->stream);
		}
		return $this;
	}
	
	/**
	 * Logs into the ftp server
	 * @throws FTPLoginException if the login failed
	 * @return boolean
	 */
	public function login() 
	{
		$this->checkConnection();
		if (!@ftp_login($this->stream, $this->user, $this->pass)) {
			throw new FTPLoginException($this, $this->user, $this->pass);
		}
		return true;
	}
	
	/**
	 * Try to determine the time differnce between the local and remote system
	 * by uploading a file and compare the modified time values. The file that
	 * is uploaded is _this_ file, after time difference the file is deleted.
	 * @return integer
	 */
	public function detectTimeDifference() 
	{
		$remoteName = $this->curDir.'.ftpTimdifferenceTestFile';
		$this->upload(__FILE__, $remoteName);
		$modified = $this->lastModified($remoteName);
		$result = time() - $modified;
		if ($this->exists($remoteName)) {
			$this->delete($remoteName);
		}
		if ($result > 5) {
			return $result;
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns a simple list of files and directories in the current directory.
	 * If you want a more precise list use {@link rawList}
	 * 
	 * Note: This won't show you hidden files on -nix systems (the files with
	 * the dot at the beginning!)
	 * 
	 * @param string $filename name of directory to list files from
	 * @return array(string)
	 */
	public function ls($filename = null) 
	{
		$this->checkConnection();
		if ($filename === null) {
			$filename = $this->curDir;
		}
		// check if directory exits
		$dirname = dirname($filename.'/s');
		if (!$this->exists($dirname.'/')) {
			throw new FTPDirNotFoundException($this, $dirname);
		}
		return ftp_nlist($this->stream, $filename);
	}
	
	/**
	 * Set the passive mode to true or false
	 * @param boolean $value
	 * @return boolean
	 */
	public function pasv($value) 
	{
		$this->checkConnection();
		ftp_pasv($this->stream, (bool) $value);
		return true;
	}
	
	/**
	 * Returns a more precise list of files and directories than {@link ls}.
	 * 
	 * <code>
	 * $exampleReturn = array(
	 * 	'file1.txt' => array('isDirectory' => false, 'flags' => '-rw-rw-rw', 'filename' => 'file1.txt'),
	 * 	'directory' => array('isDirectory' => true, 'flags' => 'drw-rw-rw', 'filename' => 'directory/')
	 * );
	 * </code>
	 * 
	 * Note that this method also returns hidden files.
	 * 
	 * @param string $filename
	 * @param boolean $hiddenFiles	List hidden files
	 * @return array(string)
	 */
	public function rawList($filename = null, $hiddenFiles = true) 
	{
		$this->checkConnection();
		if ($filename === null) {
			$filename = $this->curDir;
		}
		// extract dirname, cause we need to change this directory
		$dirname = dirname($filename.'/a');
		$this->pasv(true);
		$lastDir = $this->curDir;
		$this->cd($dirname);
		$result = ftp_rawlist($this->stream, '-al');
		$this->pasv(false);
		if ($this->curDir !== $this->lastDir) {
			$this->cd($lastDir);
		}
		// parse result
		$files = array();
		foreach ($result as $line) {
			if (!($match = preg_match_all('/
				^(?P<flags>[-dl][rwxst-]+)\s+
				(\d+)\s(\d+)\s+(\d+)\s+
				(?P<filesize>\d+)\s+
				(?P<datetime>
					\w{3}\s+\d{1,2}\s+[0-9:]+
				)\s+
				(?P<filename>.*)
				$/x', $line, $found))
				) {
				continue;
			}
			// don't include up/current dir parts of listing
			if (in_array($found['filename'][0], array('.', '..', ''))) {
				continue;
			}
			$firstFlag = substr($found['flags'][0], 0, 1);
			// ignore symbolic links
			if ($firstFlag == 'l') continue;
			// default values for file return array
			$fileInfo = array( 
				'modified' => strtotime($found['datetime'][0]),
				'flags' => $found['flags'][0]
			);
			switch($firstFlag) {
				case 'd':
					$fileInfo['isDirectory'] = true;
					$found['filename'][0] .= '/';
					$fileInfo['dirname'] = $found['filename'][0];
					break;
				case '-':
					$fileInfo['isDirectory'] = false;
					$fileInfo['filename'] = $found['filename'][0];
					break;
			}
			$files[$found['filename'][0]] = $fileInfo;
		}
		return $files;
	}
	
	/**
	 * Uploads a local file to the remote server and returns the transferrate
	 * in bytes per second
	 * @param string $filename
	 * @param string $remoteFilename
	 * @param boolean $overwrite
	 * @param integer $mode
	 * @return integer	Number of bytes per second transferred
	 */
	public function upload($filename, $remoteFilename = null, $overwrite = false, $mode = null) 
	{
		$this->checkConnection();
		if ($mode == null) {
			$mode = $this->transfermode;
		}
		if (!file_exists($filename)) {
			throw new FTPFileNotFoundException($this, $filename);
		} elseif (is_dir($filename)) {
			// todo enable recursive directory upload
		}
		if ($remoteFilename === null) {
			$remoteFilename = $filename;
		}
		// change to directory the file should go to or create the directory
		if (strpos($remoteFilename, '/') !== false) {
			$lastDir = $this->curDir;
			try {
				$this->cd(dirname($remoteFilename));
			} catch (FTPDirNotFoundException $e) {
				$this->mkdir(dirname($remoteFilename));
			}
		}
		// perform the upload and get the time needed
		$fileSize = filesize($filename);
		$transferStart = microtime(true);
		$this->pasv(true);
		$result = ftp_put($this->stream, $remoteFilename, $filename, $this->transfermode);
		$this->pasv(false);
		$lastError = error_get_last();
		if (isset($lastError['message']) && preg_match('@PORT command successful@i', $lastError['message'])) {
			$result = true;
		}
		$transferEnd = microtime(true);
		if (!empty($lastDir) && $lastDir !== $this->curDir) {
			$this->cd($lastDir);
		}
		if ($result) {
			$this->bytesUploaded = $fileSize;
			return $fileSize / ($transferEnd - $transferStart);
		} else {
			false;
		}
	}
	
	/**
	 * Downloads a remote file to local directory
	 * @param string $remoteFilename
	 * @param string $localFilename
	 * @param boolean $overwrite
	 * @param integer $mode
	 * @return FTP
	 */
	public function download($remoteFilename, $localFilename, $overwrite = false, $mode = null) 
	{
		$this->checkConnection();
		if (file_exists($localFilename) && $overwrite !== true) {
			throw new FTPException('File allready exists on the local machine.');
		}
		if (!@ftp_get($this->stream, $localFilename, $remoteFilename, $mode)) {
			return false;
		}
		return $this;
	}
	
	/**
	 * Returns the current directory
	 * @return string
	 */
	public function pwd() 
	{
		$this->checkConnection();
		return ftp_pwd($this->stream);
	}
	
	/**
	 * Changes directory to $path
	 * @throws FTPDirNotFoundException if directory was not found
	 * @param string $path
	 * @return boolean
	 */
	public function cd($path) 
	{
		$this->checkConnection();
		if (substr($path, -1, 1) !== '/' && strlen($path) > 1) {
			$path .= '/';
		}
		if (!@ftp_chdir($this->stream, $path)) {
			throw new FTPDirNotFoundException($this, $path);
		}
		$this->curDir = $path;
		return true;
	}
	
	/**
	 * Create a directory $path
	 * <code>
	 * if(!$ftp->mkdir('test')) {
	 * 	die('failed to create directory');
	 * }
	 * </code>
	 * @return boolean
	 */
	public function mkdir($path) 
	{
		$this->checkConnection();
		$result = @ftp_mkdir($this->stream, $path);
		return $result;
	}
	
	/**
	 * Delete a file or directory $path
	 * @return boolean
	 */
	public function delete($path) 
	{
		$this->checkConnection();
		if (!@ftp_delete($this->stream, $path)) {
			return false;
		}
		return $this;
	}
	
	/**
	 * Tests if a file or directory $filename exists on the remote system
	 * @param string $filename
	 * @return boolean
	 */
	public function exists($filename) 
	{
		if (empty($filename)) {
			return false;
		}
		$this->checkConnection();
		// file sizes are always true and good
		if ($size = $this->filesize($filename)) {
			return $size;
		}
		// check if directory exists by trying to change to it
		$curDir = $this->curDir;
		if (!@ftp_chdir($this->stream, $filename)) {
			$this->cd($curDir);
			return false;
		}
		$this->cd($curDir);
		return true;
	}
	
	/**
	 * Returns the size of a file in bytes, if file was not found it returns
	 * false, so check it with ===:
	 * <code>
	 * if ($ftp->size('notexistentfile') === false) {
	 * 	die('file not found');
	 * }
	 * </code>
	 * @param string $filename
	 * @return integer|boolean
	 */
	public function size($filename) 
	{
		if (empty($filename)) {
			return false;
		}
		$this->checkConnection();
		$size = ftp_size($this->stream, $filename);
		if ($size == -1) {
			return false;
		}
		return $size;
	}
	
	/**
	 * Alias for {@link size}
	 * @param string $filename
	 * @return integer|boolean
	 */
	public function filesize($filename) 
	{
		return $this->size($filename);
	}
	
	/**
	 * Returns timestamp of last modification of a file.
	 * Note that the timestamp is the time on the server, not your local time.
	 * @param string $filename
	 * @return integer|boolean
	 */
	public function lastModified($filename) 
	{
		if (empty($filename)) {
			return false;
		}
		// use raw command to retreive modified timestamp, ftp_mdtm does not work :(
		$command = 'MDTM '.$filename;
		$datePacked = $this->raw($command);
		if (preg_match('@^\d+\s+(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$@', $datePacked[0], $found)) {
			list($b, $year, $month, $day, $hour, $minute, $second) = $found;
			$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
			return $timestamp;
		} else {
			return false;
		}
	}
	
	/**
	 * Sends a raw command to the server and returns a response
	 * @param string $command
	 * @return array(string)
	 */
	public function raw($command) 
	{
		$this->checkConnection();
		if (empty($command)) return '';
		return ftp_raw($this->stream, $command);
	}
	
	/**
	 * Returns a string that contains the answer to the SYS command
	 * @return string
	 */
	public function systemType() 
	{
		$this->checkConnection();
		return ftp_systype($this->stream);
	}
	
	public function __destroy() 
	{
		$this->disconnect();
	}
	
	public function __clone() 
	{
		throw new FTPNotClonableException();
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FTPException extends Exception 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FTPDirNotFoundException extends FTPException 
{
	public function __construct(FTP $ftp, $dirname) 
	{
		parent::__construct('Directory \''.$dirname.'\' not found.');
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FTPInvalidHostException extends FTPException 
{
	public function __construct(FTP $ftp, $host) 
	{
		if (empty($host)) {
			parent::__construct('Invalid Host (empty)');
		} else {
			parent::__construct(sprintf('Invalid host: \'%s\'', $host));
		}
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FTPFileNotFoundException extends FTPException 
{
	public function __construct(FTP $ftp, $filename) 
	{
		parent::__construct(sprintf('File not found \'%s\'.', $filename));
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FTPConnectionErrorException extends FTPException 
{
	public function __construct(FTP $ftp, $reason = null) 
	{
		if (empty($reason)) {
			$lastPHPError = error_get_last();
			parent::__construct(!empty($lastPHPError['message']) ? $lastPHPError['message'] : 'unknown reason');
		} else {
			parent::__construct($reason);
		}
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FTPNotConnectedException extends FTPException 
{
	public function __construct(FTP $ftp) 
	{
		parent::__construct('No FTP connection established.');
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FTPLoginException extends FTPException 
{
	public function __construct(FTP $ftp, $user, $pass) 
	{
		$lastError = error_get_last();
		// add information about user and password used (but don’t show password)
		$message = 'Failed login on as user \''.$user.'\' using password: ';
		if ($pass) {
			$message .= 'yes';
		} else {
			$message .= 'no';
		}
		// add error message from last login if found
		if (isset($lastError['message'])) {
			$message .= ', \''.$lastError['message'].'\'';	
		}
		parent::__construct($message);
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class FTPNotClonableException extends FTPException 
{
	public function __construct() 
	{
		parent::__construct('FTP not clonable.');
	}
}