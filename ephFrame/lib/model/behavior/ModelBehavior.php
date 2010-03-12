<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

/**
 * Default Model Behavior
 * 
 * This Behavior implements basic callbacks for every standard model from
 * ephFrame. This included defualt created, updated time stamp setting in
 * beforeInsert and beforeUpdate.
 * 
 * You can create your own {@link Model} Behaviors by extending from this
 * class. See the examples allready there like {@link HitCountBehavior}, 
 * {@link FlagableBehavior}, {@link NestedSetBehavior} etc.
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.model.behavior
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 15.10.2008
 */
class ModelBehavior extends Object
{	
	/**
	 * Place where configuration is stored
	 * @var array(string)
	 */
	protected $config = array();
	
	/**
	 * Default configuration, is merged with incoming configuration
	 * @var array(string)
	 */
	protected $defaultConfig = array();
	
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
	public function __construct(Model $model, Array $config = array())
	{
		$this->model = $model;
		if (is_array($config) && $this->defaultConfig) {
			$this->config[$model->name] = array_merge($this->defaultConfig, $config);
		}
		return $this;
	}
	
	public function afterConstruct()
	{
		return true;
	}
	
	public function beforeDelete()
	{
		return true;
	}
	
	public function afterDelete()
	{
		return true;
	}

	public function beforeInsert()
	{
		return true;
	}
	
	public function afterInsert()
	{
		return true;
	}
	
	public function beforeSave(Model $Model)
	{
		return true;
	}
	
	public function afterSave()
	{
		return true;
	}
	
	public function beforeUpdate()
	{
		return true;
	}
	
	public function afterUpdate()
	{
		return true;
	}	
}