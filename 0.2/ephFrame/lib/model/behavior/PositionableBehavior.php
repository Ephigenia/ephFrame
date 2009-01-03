<?php

/**
 * 	Behavior for Models with position field
 * 
 * 	This is a simple Behavior that shows how behaviors could be used for models.
 * 	It will provide methods for moving model entries around in a one-dimension
 * 	hirarchy. So every Model should have a 'position' field defined as integer
 * 	and this position field can be manipulated if the model behaves as
 * 	Positionable.
 * 		
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 03.12.2008
 * 	@package nms.folio
 * 	@subpackage nms.folio.lib.model.behavior
 */
class PositionableBehavior extends ModelBehavior {
	
	const MOVE_DIRECTION_UP = 'up';
	const MOVE_DIRECTION_TOP = 'top';
	const MOVE_DIRECTION_DOWN = 'down';
	const MOVE_DIRECTION_BOTTOM = 'borrom';
	
	/**
	 *	@return Hash
	 */
	protected function collectModelConditions() {
		$conditions = array();
		/*foreach($this->model->belongsTo as $config) {
			$conditions[$config['associationKey']] = $this->model->get(substr(strchr($config['associationKey'], '.'), 1));
		}*/
		return new Hash($conditions);
	}
	
	/**
	 *	Return next model entry
	 * 	@param array(string) additional conditions to use
	 * 	@return boolean|Model
	 */
	public function next($additionalConditions = array()) {
		if (!$this->model->exists()) return false;				
		$conditions = $this->collectModelConditions();
		var_dump($conditions->toArray());
		
		$conditions->push($this->model->name.'.position > '.$this->model->position);
		$conditions->appendFromArray($additionalConditions);
		var_dump($conditions->toArray());
		
		return $this->model->find($conditions->toArray(), array($this->model->name.'.position ASC'));
	}
	
	/**
	 *	Return model entry that is before this model
	 * 	@param array(string) additional conditions to use
	 * 	@return boolean|Model
	 */
	public function previous($additionalConditions = array()) {
		if (!$this->model->exists()) return false;
		$conditions = $this->collectModelConditions();
		$conditions->push($this->model->name.'.position < '.$this->model->position);
		$conditions->appendFromArray($additionalConditions);
		$r = $this->model->find($conditions->toArray(), array($this->model->name.'.position DESC'));
		return $r;
	}
	
	/**
	 *	@param array(string) additional conditions to use
	 * 	@return boolean|Model
	 */
	public function first($additionalConditions = array()) {
		if (!$this->model->exists()) return false;
		$conditions = $this->collectModelConditions();
		$conditions->push($this->model->name.'.position < '.$this->model->position);
		$conditions->appendFromArray($additionalConditions);
		return $this->model->find($conditions->toArray(), array($this->model->name.'.position ASC'));
	}
	
	/**
	 * 	@param array(string) additional conditions to use
	 * 	@return boolean|Model
	 */
	public function last($additionalConditions = array()) {
		if (!$this->model->exists()) return false;
		$conditions = $this->collectModelConditions();
		$conditions->push($this->model->name.'.position > '.$this->model->position);
		$conditions->appendFromArray($additionalConditions);
		return $this->model->find($conditions->toArray(), array($this->model->name.'.position DESC'));
	}
	
	public function move($direction) {	
		if (!($this->model->exists())) {
			return false;
		}
		switch($direction) {
			case self::MOVE_DIRECTION_TOP:
				if ($tmpImage = $this->first()) {
					$this->model->saveField('position', $tmpImage->position - 1);
				}
				break;
			case self::MOVE_DIRECTION_UP:
				if ($tmpImage = $this->previous()) {
					$tmpPosition = $tmpImage->position;
					$tmpImage->saveField('position', $this->model->position);
					$this->model->saveField('position', $tmpPosition);
				}
				break;
			case self::MOVE_DIRECTION_DOWN:
				if ($tmpImage = $this->next()) {
					$tmpPosition = $tmpImage->position;
					$tmpImage->saveField('position', $this->model->position);
					$this->model->saveField('position', $tmpPosition);
				}
				break;
			case self::MOVE_DIRECTION_BOTTOM:
				if ($tmpImage = $this->last()) {
					$this->model->saveField('position', $tmpImage->position + 1);
				}
				break;
			default:
				return false;
				break;
		}
		return true;
	}
	
}

?>