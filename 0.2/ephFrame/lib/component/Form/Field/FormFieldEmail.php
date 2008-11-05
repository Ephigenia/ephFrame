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

require_once dirname(__FILE__).'/FormFieldText.php';

/**
 * 	Email Form Field Text
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component.Form.Field
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 04.11.2008
 */
class FormFieldEmail extends FormFieldText {

	/**
	 *	Default validation rules for emails, should be emails
	 * 	@var array(string)
	 */
	public $validate = array(
		'invalid' => array(
			'regexp' => Validator::EMAIL,
			'message' => 'The email adress you\'ve entered seemes to be invalid. Please enter a valid email adress.'
		)
	);
	
}

?>