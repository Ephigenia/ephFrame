<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright (c) 2007+, Ephigenia M. Eichner
 * 						 Kopernikusstr. 8
 * 						 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 
 * 	@license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright		copyright 2007+, Ephigenia M. Eichner
 * 	@link			http://code.ephigenia.de/projects/ephFrame/
 * 	@version		$Revision$
 * 	@modifiedby		$LastChangedBy$
 * 	@lastmodified	$Date$
 * 	@filesource		$HeadURL$
 */

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class IntrusionException extends BasicException {
	public $stdMessage = 'Possible Intrusion detected';
	public function __construct($message = null) {
		$this->level = self::INTRUSION;
		if ($message !== null) {
			$this->message = $message;	
		} else {
			$this->message = $this->stdMessage;
		}
		parent::__construct();
	}
}
/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class XSSException extends IntrusionException {
	public $message = 'Possible Cross Site Scription (XSS) detected.';
}
/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class InjectionException extends IntrusionException {
	public $message = 'Possible Injection detected.';
}
/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class HeaderInjectionException extends InjectionException {
	public $message = 'Possible Header Injection detected.';
}

?>