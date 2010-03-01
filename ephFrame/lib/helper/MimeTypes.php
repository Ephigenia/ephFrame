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
 * MIME Types
 * 
 * This Class is a collection of mime types you can use this class to get mime
 * type for filename or get extension of a file based on the given mime type.
 * The class is _not_ analyzing the file. It's using the file extension and
 * then searching the lib for the fitting mime type.
 *
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 02.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.helper
 * @version 0.1
 */
class MimeTypes extends Helper {

	/**
	 * Returns the MIME-Type for the given $filename or file extension.
	 * If no MIME-Type was found false is returned.
	 * @param string $filenameOrExtension Filename or Extension
	 * @return string|boolean
	 */
	public static function mimeType($filenameOrExtension) {
		if (strpos($filenameOrExtension, '.') !== false) {
			$extension = strtolower(substr(strrchr(basename($filenameOrExtension), '.'), 1));
		} else {
			$extension = $filenameOrExtension;
		}
		if (array_key_exists($extension, self::$mimeTypes)) {
			return self::$mimeTypes[$extension];
		}
		return false;
	}
	
	public static function get($filenameOrExtension) {
		return self::mimeType($filenameOrExtension);
	}

	/**
	 * Alias Function for {@link mimeType}
	 * @param string $filenameOrExtension
	 * @return string|boolean
	 */
	public static function type($filenameOrExtension) {
		return self::mimeType($filenameOrExtension);
	}

	/**
	 * Collection of mime types and their file extensions
	 * @var array(string)
	 */
	public static $mimeTypes = array(
	
		// application needed types
		'dwg'	=> 'application/acad',
		'asd'	=> 'application/astound',
		'asn'	=> 'application/astound',
		'tsp'	=> 'application/dsptype',
		'dxf'	=> 'application/dxf',
		'spl'	=> 'application/futuresplash',
		'gz'	=> 'application/gzip',
		'ptlk'	=> 'application/listenup',
		'hqx'	=> 'application/mac-binhex40',
		'mbd'	=> 'application/mbedlet',
		'mif'	=> 'application/mif',
		'xls'	=> 'application/msexcel',
		'xla'	=> 'application/msexcel',
		'hlp'	=> 'application/mshelp',
		'chm'	=> 'application/mshelp',
		'ppt'	=> 'application/mspowerpoint',
		'ppz'	=> 'application/mspowerpoint',
		'pps'	=> 'application/mspowerpoint',
		'pot'	=> 'application/mspowerpoint',
		'doc'	=> 'application/msword',
		'dot'	=> 'application/msword',
		'bin'	=> 'application/octet-stream',
		'exe'	=> 'application/octet-stream',
		'com'	=> 'application/octet-stream',
		'dll'	=> 'application/x-msdownload',
		'class'	=> 'application/octet-stream',
		'oda'	=> 'application/oda',
		'pdf'	=> 'application/pdf',
		'ai'	=> 'application/postscript',
		'eps'	=> 'application/postscript',
		'ps'	=> 'application/postscript',
		'rtc'	=> 'application/rtc',
		'smp'	=> 'application/studiom',
		'tbk'	=> 'application/toolbook',
		'vmd'	=> 'application/vocaltec-media-desc',
		'vmf'	=> 'application/vocaltec-media-file',
		'xhtml'	=> 'application/xhtml+xml',
		'bcpio'	=> 'application/x-cpio',
		'csh'	=> 'application/x-csh',
		'dcr'	=> 'application/x-director',
		'dir'	=> 'application/x-director',
		'dxr'	=> 'application/x-director',
		'dvi'	=> 'application/x-dvi',
		'evy'	=> 'application/x-envoy',
		'gtar'	=> 'application/x-gtar',
		'hdf'	=> 'application/x-hdf',
		'php'	=> 'application/x-httpd-php',
		'phtml'	=> 'application/x-httpd-php',
		'latex'	=> 'application/x-latex',
		'bin'	=> 'application/x-macbinary',
		'mif'	=> 'application/x-mif',
		'nc'	=> 'application/x-netcdf',
		'cdf'	=> 'application/x-netcdf',
		'nsc'	=> 'application/x-nschat',
		'sh'	=> 'application/x-sh',
		'shar'	=> 'application/x-shar',
		'swf'	=> 'application/x-shockwave-flash',
		'spr'	=> 'application/x-sprite',
		'sprite'=> 'application/x-sprite',
		'sit'	=> 'application/stuffit',
		'sca'	=> 'application/supercard',
		'tar'	=> 'application/x-tar',
		'tcl'	=> 'application/x-tcl',
		'tex'	=> 'application/x-text',
		'zip'	=> 'application/zip',
		'z'		=> 'application/x-compress',
	
		// audio types
		'au'	=> 'audio/basic',
		'snd'	=> 'audio/basic',
		'es'	=> 'audio/echospeech',
		'tsi'	=> 'audio/tsplayer',
		'vox'	=> 'audio/voxware',
		'aif'	=> 'audio/x-aiff',
		'aiff'	=> 'audio/x-aiff',
		'aifc'	=> 'audio/x-aiff',
		'dus'	=> 'audio/x-dspeeh',
		'cht'	=> 'audio/x-dspeeh',
		'mid'	=> 'audio/x-midi',
		'midi'	=> 'audio/x-midi',
		'mp2'	=> 'audio/x-mpeg',
		'mp3'	=> 'audio/mpeg',
		'ram'	=> 'audio/x-pn-realaudio',
		'ra'	=> 'audio/x-pn-realaudio',
		'rpm'	=> 'audio/x-pn-realaudio-plugin',
		'stream'=> 'audio/x-qt-stream',
		'wav'	=> 'audio/x-wav',
	
		'dwf'	=> 'drawing/x-dwf',
	
		// image types
		'cod'	=> 'image/cis-cod',
		'ras'	=> 'image/cmu-raster',
		'fif'	=> 'image/fif',
		'gif'	=> 'image/gif',
		'ief'	=> 'image/ief',
		'jpg'	=> 'image/jpeg',
		'jpeg'	=> 'image/jpeg',
		'jpe'	=> 'image/jpeg',
		'png'	=> 'image/png',
		'svg'	=> 'image/svg+xml',
		'tif'	=> 'image/tiff',
		'tiff'	=> 'image/tiff',
		'mcf'	=> 'image/vasa',
		'wmbp'	=> 'image/vnd.wap.wbmp',
		'fh4'	=> 'image/x-freehand',
		'fh5'	=> 'image/x-freehand',
		'fhc'	=> 'image/x-freehand',
		'rgb'	=> 'image/x-rgb',
	
		'wrl'	=> 'model/vrml',
	
		// textual types
		'csv'	=> 'text/comma-separated-values',
		'css'	=> 'text/css',
		'htm'	=> 'text/html',
		'html'	=> 'text/html',
		'shtml'	=> 'text/html',
		'js'	=> 'text/javascript',
		'txt'	=> 'text/plain',
		'rtf'	=> 'text/rtf',
		'xml'	=> 'text/xml',
		'c'		=> 'text/plain',
		'htc'	=> 'text/x-component',
	
		// video types
		'mpg'	=> 'video/mpeg',
		'mpeg'	=> 'video/mpeg',
		'mpe'	=> 'video/mpeg',
		'qt'	=> 'video/quicktime',
		'mov'	=> 'video/quicktime',
		'avi'	=> 'video/x-msvideo',
		'asx'	=> 'video/x-ms-asf',
		'asr'	=> 'video/x-ms-asf',
		'asf'	=> 'video/x-ms-asf'
		
	);	

}