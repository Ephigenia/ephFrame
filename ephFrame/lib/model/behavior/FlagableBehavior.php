<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                    Kopernikusstr. 8
 *                    10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright		copyright 2007+, Ephigenia M. Eichner
 * @link			http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

/**
 * Behavior that supports "flagging"-methods for a model field
 * http://en.wikipedia.org/wiki/Flag_(computing)
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.models.behaviors
 * @author Ephigenia // Marcel Eichner <love@ephigenia.de>
 * @since 29.01.2009
 */
class FlagableBehavior extends ModelBehavior
{	
	/**
	 * Name of the field that stores the flags
	 * @var string
	 */
	public $flagFieldname = 'flags';
	
	/**
	 * Test the model to have at least one of the passed flags
	 * 
	 * <code>
	 * if ($User->hasFlag(User::FLAG_ADMIN, User::FLAG_EDITOR)) {
	 * echo 'permission to edit entry';
	 * }
	 * </code>
	 * @param array(integer) $flag
	 * @return boolean
	 */
	public function hasFlags($flag)
	{
		if (!is_array($flag)) {
			$flags = (func_num_args() > 1) ? func_get_args() : array($flag);
		} else {
			$flags = $flag;
		}
		$flags = array_map('intval', $flags);
		foreach($flags as $flag) {
			if (!$this->hasFlag($flag)) continue;
			return true;
		}
		return false;
	}
	
	/**
	 * Tests if one flag is set, you can also use {@link hasFlags} for multiple
	 * flags
	 * @param integer $flag
	 * @return boolean
	 */
	public function hasFlag($flag)
	{
		if ($flag == 0 && $this->model->isEmpty($this->flagFieldname)) {
			return true;
		}
		return (((int) $this->model->{$this->flagFieldname} & (int) $flag) != 0);
	}
	
	/**
	 * Add a flag to the flags
	 * <code>
	 * $User->addFlag(User::FLAG_ADMIN);
	 * $User->save();
	 * </code>
	 * @param integer $flag
	 * @return Model
	 */
	public function addFlag($flag)
	{
		if (func_num_args() > 1) {
			$args = func_get_args();
			return $this->callMethod('addFlags', $args); 
		}
		(int) $this->model->{$this->flagFieldname} |= (int) $flag;
		return $this->model;
	}
	
	/**
	 * Set multiple flags in one method call
	 * @return Model
	 */
	public function addFlags()
	{
		$args = func_get_args();
		foreach($args as $flag) $this->addFlag($flag);
		return $this->model;
	}
	
	/**
	 * Removes a flag
	 * @param integer $flag
	 * @return Model
	 */
	public function removeFlag($flag)
	{
		(int) $this->model->{$this->flagFieldname} &= ~(int) $flag;
		return $this->model;
	}
	
	/**
	 * Alias for {@link removeFlag}
	 * @param $flag
	 * @return Model
	 */
	public function deleteFlag($flag)
	{
		return $this->removeFlag($flag);
	}
	
	/**
	 * Sets a flag to a on or off
	 * @param integer $flag
	 * @param boolean $value
	 * @return Model
	 */
	public function setFlag($flag, $value)
	{
		if ((bool) $value) {
			$this->addFlag($flag);
		} else {
			$this->removeFlag($flag);
		}
		return $this->model;
	}
	
	/**
	 * Toggles a flag from on if it’s of and from off if it was on
	 * @param integer $flag
	 * @return Model
	 */
	public function toggleFlag($flag)
	{
		(int) $this->model->{$this->flagFieldname} ^= (int) $flag;
		return $this->model;
	}
}