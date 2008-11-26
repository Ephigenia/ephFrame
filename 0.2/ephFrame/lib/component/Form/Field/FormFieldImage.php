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

require_once dirname(__FILE__).'/FormFieldFile.php';
require_once dirname(__FILE__).'/../../../Image.php';

/**
 * 	Image Upload Form Field
 * 	
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component.Form.Field
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 04.11.2008
 */
class FormFieldImage extends FormFieldFile {
	
	public $fileClassName = 'Image';
	
}

?>