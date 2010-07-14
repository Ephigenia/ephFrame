<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Brunnenstr. 10
 *                      10119 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

class_exists('ModelBehavior') or require dirname(__FILE__).'/ModelBehavior.php';

/**
 * Versionable Behavior
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.model.behavior
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2010-03-14
 */
class VersionableBehavior extends ModelBehavior
{
	/**
	 * DB Action Field Mappings
	 * @var array(string)
	 */
	protected $defaultConfig = array(
		'field' => 'revision',
	);
	
	/**
	 * Before Update Callback
	 * @return boolean
	 */
	public function beforeUpdate()
	{
		$fieldname = $this->config[$this->model->name]['field'];
		if ($this->model->hasField($fieldname)) {
			$this->model->set($fieldname, $this->model->get($fieldname) + 1);
		}
		return parent::beforeUpdate();
	}
	
	public function beforeInsert()
	{
		$fieldname = $this->config[$this->model->name]['field'];
		if ($this->model->hasField($fieldname)) {
			$this->model->set($fieldname, 1);
		}
		return true;
	}
}