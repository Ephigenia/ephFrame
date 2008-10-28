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
 * 	Simple Model Behavior
 * 	
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.model
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 15.10.2008
 */
class ModelBehavior extends Object {
	
	/**
	 * 	@var array(string)
	 */
	protected $config = array();
	
	/**
	 * 	@var Model
	 */
	protected $model;
	
	public function __construct(Model $model, Array $config = array()) {
		$this->model = $model;
		if (is_array($config)) {
			$this->config = $config;
		}
		return $this;
	}
	
	public function afterConstruct() {
		return true;
	}
	
	public function beforeInsert() {
		return true;
	}
	
	public function beforeUpdate() {
		return true;
	}
	
	public function beforeFind() {
		return true;
	}
	
	public function afterFind() {
		
	}
	
	public function beforeSave() {
		return true;
	}
	
	public function afterSave() {
		
	}
	
	public function beforeDelete() {
		return true;
	}
	
	public function afterDelete() {
		
	}
	
}

?>