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

class_exists('ModelBehavior') or require dirname(__FILE__).'/ModelBehavior.php';

/**
 * Behavior for Models with position field
 * 
 * This is a simple Behavior that shows how behaviors could be used for models.
 * It will provide methods for moving model entries around in a one-dimension
 * hirarchy. So every Model should have a 'position' field defined as integer
 * and this position field can be manipulated if the model behaves as
 * Positionable.
 * 
 * @todo add config that defines some conditions that must be equel when getting positions
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 03.12.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.model.behavior
 */
class PositionableBehavior extends ModelBehavior
{	
	const MOVE_DIRECTION_UP = 'up';
	const MOVE_DIRECTION_TOP = 'top';
	const MOVE_DIRECTION_DOWN = 'down';
	const MOVE_DIRECTION_BOTTOM = 'bottom';
	
	/**
	 * Name of the field that stores the current position
	 * @var integer
	 */
	protected $config = array(
		// field that should be used
		'field' => 'position',
	);
	
	/**
	 * Callback called whenever a new item is inserted, it will increase
	 * its position by one
	 * @return boolean
	 */
	public function beforeInsert()
	{
		$newPosition = 0;
		$fieldname = $this->config[$this->model->name]['field'];
		if ($lastModel = $this->model->findOne(null, array($this->model->name.'.position DESC'))) {
			$newPosition = (int) $lastModel->get($fieldname) + 1;
		}
		$this->model->set($fieldname, $newPosition);
		return true;
	}
	
	/**
	 * Collects all conditions that are important for retreiving lower or
	 * higher positioned items and returns them.
	 * @return Hash
	 */
	protected function collectModelConditions()
	{
		$conditions = array();
		foreach($this->model->belongsTo as $config) {
			$conditions[$config['associationKey']] = $this->model->get(substr(strchr($config['associationKey'], '.'), 1));
		}
		return new Hash($conditions);
	}
	
	/**
	 * Return next model entry
	 * @param array(string) additional conditions to use
	 * @param boolean $looped begin at the first element when model is last element, double linked
	 * @return boolean|Model
	 */
	public function next($additionalConditions = array(), $looped = false)
	{
		if (!$this->model->exists()) return false;
		$fieldname = $this->config[$this->model->name]['field'];
		$conditions = $this->collectModelConditions();
		$conditions->push($this->model->name.'.'.$fieldname.' > '.$this->model->position);
		$conditions->appendFromArray($additionalConditions);
		$result = $this->model->find($conditions->toArray(), array($this->model->name.'.'.$fieldname.' ASC'));
		if (!$result && $looped) {
			return $this->first($additionalConditions);
		} else {
			return $result;
		}
	}
	
	/**
	 * Return model entry that is before this model
	 * @param array(string) additional conditions to use
	 * @param boolean $looped return the last element if your in the first element (double linked)
	 * @return boolean|Model
	 */
	public function previous($additionalConditions = array(), $looped = false)
	{
		if (!$this->model->exists()) return false;
		$fieldname = $this->config[$this->model->name]['field'];
		$conditions = $this->collectModelConditions();
		$conditions->push($this->model->name.'.'.$fieldname.' < '.$this->model->get($fieldname));
		$conditions->appendFromArray($additionalConditions);
		$result = $this->model->find($conditions->toArray(), array($this->model->name.'.'.$fieldname.' DESC'));
		if (!$result && $looped) {
			return $this->this->last($additionalConditions);
		} else {
			return $result;
		}
	}
	
	/**
	 * Returns the first element from all positionable models including the
	 * belongsTo and hasOne Rules of the model.
	 * @param array(string) additional conditions to use
	 * @return boolean|Model
	 */
	public function first($additionalConditions = array())
	{
		if (!$this->model->exists()) return false;
		$fieldname = $this->config[$this->model->name]['field'];
		$conditions = $this->collectModelConditions();
		$conditions->push($this->model->name.'.'.$fieldname.' < '.$this->model->get($fieldname));
		$conditions->appendFromArray($additionalConditions);
		return $this->model->find($conditions->toArray(), array($this->model->name.'.'.$fieldname.' ASC'));
	}
	
	/**
	 * Returns the last element from all positionable models including the
	 * belongsTo and hasOne Rules of the model.
	 * @param array(string) additional conditions to use
	 * @return boolean|Model
	 */
	public function last($additionalConditions = array())
	{
		if (!$this->model->exists()) return false;
		$fieldname = $this->config[$this->model->name]['field'];
		$conditions = $this->collectModelConditions();
		$conditions->push($this->model->name.'.'.$fieldname.' > '.$this->model->get($fieldname));
		$conditions->appendFromArray($additionalConditions);
		return $this->model->find($conditions->toArray(), array($this->model->name.'.'.$fieldname.' DESC'));
	}
	
	/**
	 * Tests if this model entry is the last element in the list
	 * @return boolean
	 */
	public function isLast()
	{
		return (!$this->next(null, false));
	}
	
	/**
	 * Tests if this model entry is the first element in the list
	 * @return boolean
	 */
	public function isFirst()
	{
		return (!$this->previous(null, false));
	}
	
	/**
	 * Move field in different directions, use the MOVE_DIRECTION_* constants
	 * to pass the direction to set
	 * @param integer $direction
	 * @param array(string) $additionalConditions
	 * @return Model
	 */
	public function move($direction, $additionalConditions = array())
	{	
		if (!($this->model->exists())) {
			return false;
		}
		$fieldname = $this->config[$this->model->name]['field'];
		switch($direction) {
			case self::MOVE_DIRECTION_TOP:
				if ($tmpImage = $this->first($additionalConditions, false)) {
					$this->model->saveField($fieldname, $tmpImage->get($fieldname) - 1);
				}
				break;
			case self::MOVE_DIRECTION_UP:
				if ($tmpImage = $this->previous($additionalConditions, false)) {
					$tmpPosition = $tmpImage->get($fieldname);
					$tmpImage->saveField($this->positionFieldName, $this->model->get($fieldname));
					$this->model->saveField($fieldname, $tmpPosition);
				}
				break;
			case self::MOVE_DIRECTION_DOWN:
				if ($tmpImage = $this->next($additionalConditions, false)) {
					$tmpPosition = $tmpImage->get($fieldname);
					$tmpImage->saveField($this->positionFieldName, $this->model->get($fieldname));
					$this->model->saveField($fieldname, $tmpPosition);
				}
				break;
			case self::MOVE_DIRECTION_BOTTOM:
				if ($tmpImage = $this->last($additionalConditions, false)) {
					$this->model->saveField($fieldname, $tmpImage->get($fieldname) + 1);
				}
				break;
			default:
				return false;
				break;
		}
		return true;
	}	
}