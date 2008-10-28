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

require_once FRAME_LIB_DIR.'Controller.php';
require_ONCE FRAME_LIB_DIR.'Console.php';

/**
 * 	[add description here]
 * 	@todo add doc for this!
 * 
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 28.10.2008
 */
class ConsoleController extends Controller {
	
	protected $optParseClassname = 'OptParse';
	
	/**
	 * 	@var Console
	 */
	protected $console;
	
	public function afterConstruct() {
		parent::afterConstruct();
		$this->console = new Console;
		// load optParse
		$optParseClass = $this->optParseClassname;
		if (strpos($this->optParseClassname, ClassPath::$classPathDevider) !== false) {
			
		} else {
			$optParseClassName = ClassPath::className($this->optParseClassname);
			$optParseClassPath = 'ephFrame.lib.OptParse';
		}
		
	}
	
}

?>