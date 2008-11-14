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
 *	Application Database Configuration
 * 
 * 	This class just stores one or more database connection strings that are used
 * 	by the {@link Model} as $useDBConfig and the {@link DBConnectionManager}
 * 	which establishes the connection.
 * 
 * 	With this you can easily implement different Database configs for local
 * 	and online development or even per-developer config:
 * 	<code>
 * 	// example switch of default configs, depending on server hostname
 * 	class DBConfig {
 * 		public $localhost = 'mysql:...';
 * 		public $default = '';
 * 		public $online = 'mysql:...';
 * 		public function __construct() {
 * 			if ($_SERVER['SERVER_NAME'] == 'localhost') {
 * 				$this->default = $this->localhost;
 * 			} else {
 * 				$this->default = $this->online;
 * 			}
 * 		}
 *  }
 * 	</code>
 * 
 * 	Per developer example, assuming we have developer virtual hosts like
 * 	http://ephigenia.testprojectname.local/:
 * 	<code>
 * 	class DBConfig {
 * 		public $default = 'mysql://defaultconfig';
 * 		public $ephigenia = 'mysql://ephigeniaconfig';
 * 		public function __construct() {
 * 			if (preg_match('@([a-z0-9_~-]+)\.@i', $_SERVER['SERVER_NAME'], $found) && isset($this->$found[1])) {
 *			$this->default = $this->$found[1];
 *		}
 * 	}
 * 	</code>
 * 	
 * 	@package app
 * 	@subpackage app.config
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 */
class DBConfig {
	
	/**
	 * 	Default database connection string, see {@link DBDSN} 
	 *	@var string
	 */
	public $default = 'mysql://root:gDE3ge@localhost:3306/ephFrameTest/#utf8';

	public function __construct() {
		// switch DB Configs depending on user name (example how to switch)
		switch(get_current_user()) {
			case 'yourUsername':
				$this->default = 'mysql://';
				break;
		}
	}
	
}

?>