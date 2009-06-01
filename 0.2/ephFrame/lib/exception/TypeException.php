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
 *	Collected Exception Classes for invalid Types
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 10.06.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */

/**
 *	Thrown if an argument is missing
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ArgumentExpectedException extends BasicException {}

/**
 * 	Thrown if an invalid variable type was detected
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class TypeException extends BasicException {
	public function __construct($expectedtype = null) {
		if ($expectedtype !== null) {
			$message = 'Invalid type detected. The expected type was \''.$expectedtype.'\'.'; 
		}
		parent::__construct();
	}
}

/**
 * 	Thrown if a variable was not the type of string
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class StringExpectedException extends TypeException {
	public function __construct() {
		parent::__construct('String');
	}
}

/**
 * 	Thrown when a variable is not a numeric type
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class NumericExpectedException extends TypeException {
	public function __construct() {
		parent::__construct('Numeric');
	}
}

/**
 * 	Thrown if an integer value was expected
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class IntegerExpectedException extends NumericExpectedException {
	public function __construct() {
		parent::__construct('Integer');
	}
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class FloatExpectedException extends NumericExpectedException {
	public function __construct() {
		parent::__construct('Float');
	}
}

/**
 * 	Thrown when an object was expected
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ObjectExpectedException extends TypeException {
	/**
	 * 	@param string $objectType Originally expected object class name
	 */
	public function __construct($objectType = 'Object') {
		parent::__construct($objectType);
	}
}

/**
 * 	Thrown when a expected object was not a child of a class
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ObjectIsNotChildOfException extends ObjectExpectedException {
	public function __construct($grandfatherClassName = '') {
		$this->message = 'The object passed was not a child of the expected parent class called \''.$grandfatherClassName.'\'';
		parent::__construct();
	}
}

/**
 * 	Thrown when an array was expected
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ArrayExpectedException extends TypeException {
	public function __construct() {
		parent::__construct('Array');
	}
}


?>