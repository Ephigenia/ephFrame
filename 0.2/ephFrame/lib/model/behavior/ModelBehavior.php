<?php

/**
 * ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

/**
 * Default Model Behavior
 * 
 * This Behavior implements basic callbacks for every standard model from
 * ephFrame. This included defualt created, updated time stamp setting in
 * beforeInsert and beforeUpdate.
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.model
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 15.10.2008
 */
class ModelBehavior extends Object {
	
	/**
	 * @var array(string)
	 */
	protected $config = array();
	
	/**
	 * @var Model
	 */
	protected $model;
	
	/**
	 * ModelBehavior Constructor
	 * 
	 * Expects an instance of a {@link Model} and an optional config array.
	 * 
	 * @param Model			$model
	 * @param array(string) 	$config
	 * @return ModelBehavior
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
	
	public function beforeDelete() {
		return true;
	}
	
	public function afterDelete() {
		return true;
	}
	
	/**
	 * Default beforeInsert callback
	 *
	 * You don’t need to call this with parent::beforeInsert from child classes
	 * because all models include the ModelBehavior by default.
	 * 
	 * @return boolean
	 */
	public function beforeInsert() {
		if ($this->model->hasField('created')
			&& $this->model->created <= 0
			&& in_array($this->model->structure['created']->quoting, array(ModelFieldInfo::QUOTE_STRING, ModelFieldInfo::QUOTE_INTEGER))
			) {
			$this->model->set('created', time());
		}
		return true;
	}
	
	public function afterInsert() {
		return true;
	}
	
	public function beforeSave() {
		return true;
	}
	
	public function afterSave() {
		return true;
	}
	
	/**
	 * Default beforeUpdate callback
	 * 
	 * @return boolean
	 */
	public function beforeUpdate() {
		if ($this->model->hasField('updated')
			&& in_array($this->model->structure['updated']->quoting, array(ModelFieldInfo::QUOTE_STRING, ModelFieldInfo::QUOTE_INTEGER))
			) {
			$this->model->set('updated', time());
		}
		return true;
	}
	
	public function afterUpdate() {
		return true;
	}
	
}