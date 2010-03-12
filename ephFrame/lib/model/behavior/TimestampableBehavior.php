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
 * Timestampable Behavior
 * 
 * This behavior is set to all {@link Models} within an ephFrame application
 * and updates table fields with the current timestamp when a new row is
 * inserted or updated.
 * 
 * You can change the fields that are updated passind other fieldnames using
 * the config.
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.model.behavior
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2010-03-12
 */
class TimestampableBehavior extends ModelBehavior
{
	/**
	 * DB Action Field Mappings
	 * @var array(string)
	 */
	protected $defaultConfig = array(
		'insert' => 'created',
		'update' => 'updated',
	);

	public function beforeInsert()
	{
		$fieldname = $this->config[$this->model->name]['insert'];
		if ($fieldname && $this->model->hasField($fieldname)) {	
			$this->model->set($fieldname, 'NOW()');
		}
		return true;
	}
	
	public function beforeUpdate()
	{
		$fieldname = $this->config[$this->model->name]['update'];
		if ($fieldname && $this->model->hasField($fieldname)) {	
			$this->model->set($fieldname, 'NOW()');
		}
		return true;
	}
}