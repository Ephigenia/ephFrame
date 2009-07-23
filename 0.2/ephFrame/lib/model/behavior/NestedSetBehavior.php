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
 * Nested Set Behavior
 * 
 * A behavior for models that are arranged like a nested set in the database.
 * 
 * @author Ephigenia // Marcel Eichner <love@ephigenia.de>
 * @since 15.01.2009
 * @package ephFrame
 * @subpackage ephFrame.lib.models.behaviors
 */
class NestedSetBehavior extends ModelBehavior {
	
	const MOVE_UP = 'previous';
	const MOVE_DOWN = 'next';
	
	public function isRoot() {
		return ($this->model->lft == 1);
	}
	
	public function isChild() {
		return !$this_>isRoot();
	}
	
	/**
	 * @return integer
	 */
	public function distance() {
		return (int) ($this->model->rgt - $this->model->lft);
	}
	
	/**
	 * Returns the number of children in the subtree of this node.
	 * @return integer
	 */
	public function numChildren() {
		return (int) floor(($this->distance() - 1) / 2);
	}
	
	/**
	 * Alias for {@link numChildren}
	 * @return unknown_type
	 */
	public function childrenCount() {
		return $this->numChildren();
	}
	
	/**
	 * Determine level of depth of the current node and return it (cached)
	 * @return integer
	 */
	public function level() {
		if (!isset($this->model->data['level'])) {
			$this->model->set('level', count($this->path(false, 0)));
		}
		return $this->model->level;
	}
	
	/**
	 * Does the current node has children?
	 * @return boolean
	 */
	public function hasChildren() {
		return $this->numChildren() > 0;
	}
	
	/**
	 * Before Save callback
	 * @return boolean
	 */
	public function beforeSave() {
		if (isset($this->model->Parent)) {
			$this->model->parent_id = $this->model->Parent->id;
			// increase level
			if ($this->model->hasField('level')) {
				$this->model->set('level', $this->model->Parent->get('level', 0) + 1);
			}
		}
		return true;
	}
	
	public function beforeInsert() {
		if (isset($this->model->Parent)) {
			$this->model->query('UPDATE '.$this->model->tablename.' SET rgt = rgt + 2 WHERE rgt >= '.$this->model->Parent->rgt);
			$this->model->query('UPDATE '.$this->model->tablename.' SET lft = lft + 2 WHERE lft > '.$this->model->Parent->rgt);
			$this->model->lft = (int) $this->model->Parent->rgt;
			$this->model->rgt = (int) $this->model->Parent->rgt + 1;
		}
		return true;
	}
	
	/**
	 * Add a new Child to this model
	 * @var Model $child
	 * @return Model|boolean
	 */
	public function addChild(Model $child) {
		$child->Parent = $this->model;
		return $child->save();
	}
	
	public function afterDelete() {
		if ($children = $this->tree(1, 1)) {
			foreach($children as $child) {
				$child->delete();
			}
		}
		$this->model->query(new DeleteQuery($this->model->tablename, array('lft >= '.$this->model->lft, 'rgt <= '.$this->model->rgt)));
		$this->model->query('UPDATE '.$this->model->tablename.' SET lft=lft-ROUND(' . ($this->model->rgt - $this->model->lft + 1) . ') WHERE lft > ' . $this->model->rgt);
		$this->model->query('UPDATE '.$this->model->tablename.' SET rgt=rgt-ROUND(' . ($this->model->rgt - $this->model->lft + 1) . ') WHERE rgt > ' . $this->model->rgt);
		return true;
	}
	
	/**
	 * Return children from the current node
	 * @param $depth
	 * @return IndexedArray
	 */
	public function children($depth = null, $depthModel = null) {
		return $this->tree($depth, $depthModel);
	}
	
	/**
	 * Returns indexed array of tree elements with tree leaves depth
	 * 
	 * @param integer			$depth 		recursion depth of tree as number of levels to return, pass null for all levels
	 * @param integer			$depthModel	association depth of models that are found, pass null for default model depth
	 * @return array(Model)
	 */
	public function tree($depth = null, $depthModel = null) {
		$q = $this->model->createSelectQuery(null, null, null, null, $depthModel);
		$q->addComment($this->model->name.'->'.get_class($this).'->depth(depth: '.$depth.', depthModel: '.$depthModel.')');
		$q->select('COUNT(p.id)-1 AS level');
		// general conditions
		$q->from($this->model->tablename, 'p');
		$q->where($this->model->name.'.lft BETWEEN p.lft AND p.rgt');
		if ($depth == null) {
			$q->orderBy->clear();
		}
		$q->orderBy->append($this->model->name.'.lft');
		$q->groupBy($this->model->name.'.lft');
		
		// view on part of the tree
		if ($this->model->exists()) {
			$q->where($this->model->name.'.lft > '.$this->model->lft.'');
			$q->where($this->model->name.'.rgt < '.$this->model->rgt.'');
		}
		// optimize query by using level field if possible
		if ($this->model->hasField('level')) {
			if ($depth > 0) {
				$q->where($this->model->name.'.level BETWEEN '.$this->model->level.' AND '.($this->model->level + $depth));
			} elseif ($this->model->level > 0) {
				$q->where($this->model->name.'.level > '.$this->model->level);
			}
		}
		$r = $this->model->query($q, $depthModel);
		// crappy implementation of depth parameter!
		if ($depth > 0 && $r instanceof IndexedArray) {
			$baseLevel = 20000;
			foreach($r as $node) {
				if ($node->level < $baseLevel) $baseLevel = $node->level;
			}
			foreach($r as $index => $Node) {
				if ($Node->level - $baseLevel > $depth - 1) {
					$r->delete($index);
				}
			}
		}
		return $r;
	}
	
	/**
	 * Returns the parent node of the current node if there’s any
	 *
	 * This will return the Model that acts as parent for the current node if
	 * there’s any. (if this Node isn’t the root node)
	 * The result is cached in the model as $Model->Parent.
	 * 
	 * @param integer $modelDepth optional model association depth when parent is found
	 * @return Model|boolean
	 */
	public function parent($modelDepth = null) {
		if (!$this->model->exists() || $this->isRoot()) {
			return false;
		}
		// retreive parent from model
		if (empty($this->model->Parent)) {
			$this->model->Parent = $this->model->findById($this->model->parent_id, $modelDepth);
		}
		if ($this->model->Parent) {
			return $this->model->Parent;
		}
		return false;
	}
	
	/**
	 * Returns the path to the current node
	 *
	 * This will return an indexed array with all parent nodes sorted by depth
	 * including the current node if you set $includeCurrent to true.
	 * Print path to current node:
	 * <code>
	 *
	 * </code>
	 *
	 * The results of this method are also cached in this behavior in {$cachedPath}.
	 *
	 * @param boolean $includeCurrent	Set to false if you don’t want to have the current node as first element of the returning array
	 * @param	integer	$modelDepth		Model association depth of found nodes, deault is 0
	 * @return array(Node)|false
	 */
	public function path($includeCurrent = true, $modelDepth = 0) {
		if (!$this->model->exists()) {
			return false;
		}
		if (!empty($this->cachedPath)) {
			return $this->cachedPath;
		}
		$q = $this->model->createSelectQuery(null, null, null, null, 0);
		$q->where($this->model->lft.' BETWEEN '.$this->model->name.'.lft AND '.$this->model->name.'.rgt');
		if (!$includeCurrent) {
			$q->where($this->model->name.'.'.$this->model->primaryKeyName.' <> '.$this->model->get($this->model->primaryKeyName));
		}
		$q->orderBy->prepend($this->model->name.'.lft');
		$path = array();
		if ($r = $this->model->query($q, $modelDepth)) {
			$path = $r;
		}
		$this->cachedPath = $path;
		return $path;
	}
	
	/**
	 * Returns the node thas is on the left of the current node.
	 * @return Model|boolean
	 */
	public function previous() {
		if ($this->isRoot()) return false;
		if ($previousLeaf = $this->model->findBy('rgt', $this->model->lft - 1)) {
			return $previousLeaf;
		}
		return false;
	}
	
	/**
	 * Returns the node to the right of the current node.
	 * @return Model|boolean
	 */
	public function next() {
		if ($this->isRoot()) return false;
		if ($nextLeaf = $this->model->findBy('lft', $this->model->rgt + 1)) {
			return $nextLeaf;
		}
		return false;
	}
	
	/**
	 * Move node and subtree to an other node
	 * @param $model
	 * @return model
	 */
	public function moveTo(Model $model) {
		
	}
	
	/**
	 * Move node in his level of the tree into one direction
	 * @param string $direction
	 * @return Model
	 */
	public function move($direction) {
		switch(String::lower((string) $direction)) {
			case self::MOVE_DOWN:
				$this->moveToNext();
				break;
			case self::MOVE_UP:
				$this->moveToPrevious();
				break;
		}
		if (is_int($direction)) {
			$to = $direction;
			$treeSize = $this->model->rgt - $this->model->lft + 1;
			$this->_shiftRLValues($to, $treeSize);
			if($this->model->lft >= $to){ // src was shifted too?
                $this->model->lft += $treeSize;
                $this->model->rgt += $treeSize;
            }
            /* now there's enough room next to target to move the subtree*/
            $newpos = $this->_shiftRLRange($this->model->lft, $this->model->rgt, $to - $this->model->lft);
            /* correct values after source */
            $this->_shiftRLValues($this->model->rgt + 1, -$treeSize);
            if($this->model->lft <= $to){ // dst was shifted too?
                $this->model->lft -= $treeSize;
                $this->model->rgt -= $treeSize;
            }
		}
		return $this->model;
	}
	
	/**
	 * Move Node on down on the same level
	 * @return Model
	 */
	public function moveToNext() {
		if ($next = $this->next()) {
			
			$this->move($next->rgt+1);
		}
	}
	
	public function moveToPrevious() {
		if ($prev = $this->previous()) {
			$this->move($prev->lft);
		}
	}
	
	public function _shiftRLValues($first, $delta) {
		$this->model->query('UPDATE '.$this->model->tablename.' SET lft = lft + '.$delta.' WHERE lft >= '.$first);
		$this->model->query('UPDATE '.$this->model->tablename.' SET rgt = rgt + '.$delta.' WHERE rgt >= '.$first);
	}
	
	public function _shiftRLRange($first, $last, $delta) {
		$this->model->query('UPDATE '.$this->model->tablename.' SET lft = lft + '.$delta.' WHERE lft >= '.$first.' AND lft <= '.$last);
		$this->model->query('UPDATE '.$this->model->tablename.' SET rgt = rgt + '.$delta.' WHERE rgt >= '.$first.' AND rgt <= '.$last);
		return array('l' => $first+$delta, 'r' => $last+$delta);
	}
	
}