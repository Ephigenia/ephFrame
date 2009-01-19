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
	
	/**
	 *	ModelBehavior Constructor
	 *	
	 *	Expects an instance of a {@link Model} and an optional config array.
	 *	
	 * 	@param Model			$model
	 * 	@param array(string) 	$config
	 * 	@return ModelBehavior
	 */
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
	
	/**
	 *	Default beforeInsert callback
	 *
	 *	You don’t need to call this with parent::beforeInsert from child classes
	 *	because all models include the ModelBehavior by default.
	 * 
	 * 	@return boolean
	 */
	public function beforeInsert() {
		if (isset($this->model->structure['created']) && $this->model->created <= 0) {
			if ($this->model->structure['created']->quoting == ModelFieldInfo::QUOTE_STRING) {
				$this->model->created = time();
			} elseif($this->model->structure['created']->quoting == ModelFieldInfo::QUOTE_INTEGER) {
				$this->model->created = time();
			}
		}
		return true;
	}
	
	/**
	 *	Default beforeUpdate callback
	 *	
	 * 	@return boolean
	 */
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