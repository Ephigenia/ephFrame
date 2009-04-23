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

class_exists('FormField') or require(dirname(__FILE__).'/FormField.php');

/**
 * 	Hidden Form field
 * 		
 * 	This class represents a type=hidden form input field.
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 21.04.2009
 * 	@package ephFrame
 *	@subpackage ephFrame.lib.component.Form.Field
 */
class FormFieldHidden extends FormField {
	
	public $type = 'hidden';
	
}

?>