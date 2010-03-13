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
 * @modifiedby		$LastChangedBy: gs.moresleep.net $
 * @lastmodified	$Date: 2009-11-18 14:07:53 +0100 (Wed, 18 Nov 2009) $
 * @filesource		$HeadURL: svn+ssh://moresleep.net/home/51916/data/ephFrame/0.2/ephFrame/lib/model/behavior/PositionableBehavior.php $
 */

class_exists('ModelBehavior') or require dirname(__FILE__).'/ModelBehavior.php';

/**
 * Slugable Behavior
 * 
 * A Behavior that creates and updates an uri field generated from model
 * field values. See the {@link defaultConfig} array for description.
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2010-02-17
 * @package ephFrame
 * @subpackage ephFrame.lib.model.behaviors
 */
class SluggableBehavior extends ModelBehavior
{
	/**
	 * Default Sluggable Settings
	 * These get merged in the {@link setup} method with the passed config
	 * @var array(string)
	 */
	protected $defaultConfig = array(
		// model field name where slug is saved, usually this is uri
		// you should create an index for this field
		'name' => 'uri',
		
		// names of the fields from the model that are used when creating
		'fields' => array(),
		// glue between the model field names, only used it there are more than 1
		'glue' => '-',
		// maximum length of the slug
		// set this to false or less than 0 to disable
		// not that this will not include the number that is added on unique
		'maxLength' => 128,
		
		// indicates that slugs are unique for a model, checks if slug is unique
		// and if not add a number add the end of it
		'unique' => true,
		// name of model fields that are used to search for slug collisions
		'uniqueFields' => array(),
		// update slug on every save
		// true = create slug on every save
		// false = do not create slug on every save, slug only created when empty
		'autoUpdate' => false,
		
		// URI Creation callback
		// ---------------------
		// valid php callback that is used to create the slug
		'builderCallback' => array('String', 'toURL'),
		'emptyCallback' => array('String', 'random', array(8)),
		// character that replaces spaces in the slug, send to callback
		'space' => '-',
	);
	
	/**
	 * Make an unique from $slug for the $Model using $config
	 * @param string $slug
	 * @param Model $Model
	 * @param array(string) $config
	 * @return string
	 */
	protected function makeUnique($slug, Model $Model, Array $config = array())
	{
		extract($config);
		// 
		$conditions = array(
			$Model->name.'.'.$name => DBQuery::quote($slug),
		);
		if ($Model->exists()) {
			$conditions[] = $Model->name.'.'.$Model->primaryKeyName.' <> '.$Model->id;
		}
		// add additional 
		foreach((array) $uniqueFields as $index => $fieldname) {
			if (is_string($index)) {
				$conditions[$index] = $fieldname;
			} else {
				if (strrpos($fieldname, '.') === false) {
					$fieldname = $Model->name.'.'.$fieldname;
				}
				$conditions[$fieldname] = DBQuery::quote($Model->get($fieldname));
			}
		}
		$collisions = $Model->listAll($name, $conditions, null, null, null, -1);
		if (count($collisions) > 0) {
			$freeSlot = 1;
			do {
				$potentialFreeSlug = $slug.$glue.$freeSlot;
				$freeSlot++;
			} while (in_array($potentialFreeSlug, $collisions));
			$slug = $potentialFreeSlug;
		}
		return $slug;
	}
	
	/**
	 * Extracts a slug uri from a passed Model {@link Model} depending on the
	 * passed {@link config}
	 * @param Model $Model
	 * @return string
	 */
	public function createSlug(Model $Model, Array $config = array())
	{
		if (empty($config)) {
			$config = $this->config[$Model->name];
		}
		extract($config);
		// get field values used in the slug
		$parts = array();
		foreach((array) $fields as $fieldname) {
			$parts[] = $Model->data[$fieldname];
		}
		// call callback with created slug
		$slug = implode($config['glue'], array_filter($parts));
		$slug = call_user_func_array($builderCallback, array($slug, $space));
		// fallback for empty slugs
		if (empty($slug) && $emptyCallback) {
			$slug = call_user_func_array(array($emptyCallback[0], $emptyCallback[1]), $emptyCallback[2]);
		}
		// limit length
		if ((int) $maxLength > 0) {
			$slug = substr($slug, 0, $maxLength - 1);
		}
		// check for unique slugs
		if ($unique) {
			$slug = $this->makeUnique($slug, $Model, $config);
		}
		return $slug;
	}
	
	/**
	 * Called before a {@link Model} is saved
	 * @param Model $model
	 * @return Boolean
	 */
	public function beforeSave(Model $Model)
	{
		$config = @$this->config[$this->model->name];
		if (empty($config)) {
			die('No config found for '.$this->model->name);
		}
		// update uri when empty or autoUpdate is enabled
		if ($this->model->isEmpty($config['name']) || $config['autoUpdate']) {
			$this->model->set($config['name'], $this->createSlug($this->model, $config));
		}
		return true;
	}
}