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
 * 	Default Model Behavior
 * 
 * 	This Behavior implements basic callbacks for every standard model from
 * 	ephFrame. This included defualt created, updated time stamp setting in
 * 	beforeInsert and beforeUpdate.
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
		if (isset($this->structure['created']) && $this->created <= 0) {
			if ($this->structure['created']->quoting == ModelFieldInfo::QUOTE_STRING) {
				$this->created = time();
			} elseif($this->structure['created']->quoting == ModelFieldInfo::QUOTE_INTEGER) {
				$this->created = time();
			}
		}
		return true;
	}
	
	public function beforeUpdate() {
		if (isset($this->model->structure['updated'])) {
			if ($this->model->structure['updated']->quoting == ModelFieldInfo::QUOTE_STRING) {
				$this->model->updated = time();
			} elseif($this->model->structure['updated']->quoting == ModelFieldInfo::QUOTE_INTEGER) {
				$this->model->updated = time();
			}
		}
		return true;
	}
	
}

?>