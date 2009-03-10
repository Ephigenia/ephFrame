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
 * 	Nested Set Behavior
 * 
 * 	A behavior for models that are arranged like a nested set in the database.
 * 
 *	@author Ephigenia // Marcel Eichner <love@ephigenia.de>
 *	@since 15.01.2009
 *	@package ephFrame
 *	@subpackage ephFrame.lib.models.behaviors
 */
class NestedSetBehavior extends ModelBehavior {
	
	/**
	 *	@return integer
	 */
	public function distance() {
		return (int) ($this->model->rgt - $this->model->lft);
	}
	
	/**
	 *	Returns the number of children in this node
	 *	<code>
	 *	printf('ther$node->children();
	 *	</code>
	 * 	@return integer
	 */
	public function numChildren() {
		return (int) floor(($this->distance() - 1) / 2);
	}
	
	/**
	 *	Does the current node has children?
	 * @return boolean
	 */
	public function hasChildren() {
		return $this->numChildren() > 0;
	}
	
	public function beforeSave() {
		if (isset($this->model->Parent)) {
			$this->model->parent_id = $this->model->Parent->id;
		}
		return true;
	}
	
	public function beforeInsert() {
		// lock tables ?
		$this->model->query('UPDATE '.$this->model->tablename.' SET rgt=rgt+2 WHERE rgt>='.$this->model->Parent->rgt);
		$this->model->query('UPDATE '.$this->model->tablename.' SET lft=lft+2 WHERE lft>'.$this->model->Parent->rgt); // AND rgt>'.$this->model->Parent->rgt);
		$this->model->lft = (int) $this->model->Parent->rgt;
		$this->model->rgt = (int) $this->model->Parent->rgt + 1;
		return true;
	}
	
	/**
	 *	Add a new Child to this model
	 * 	@var Model $child
	 * 	@return Model|boolean
	 */
	public function addChild(Model $child) {
		$child->Parent = $this->model;
		return $child->save();
	}
	
	public function afterDelete() {
		$this->model->query(new DeleteQuery($this->model->tablename, array('lft >= '.$this->model->lft, 'rgt <= '.$this->model->rgt)));
		$this->model->query('UPDATE '.$this->model->tablename.' SET lft=lft-ROUND(' . ($this->model->rgt - $this->model->lft + 1) . ') WHERE lft > ' . $this->model->rgt);
		$this->model->query('UPDATE '.$this->model->tablename.' SET rgt=rgt-ROUND(' . ($this->model->rgt - $this->model->lft + 1) . ') WHERE rgt > ' . $this->model->rgt);
		return true;
	}
	
	/**
	 *	Return children from the current node
	 * 	@param $depth
	 * 	@return Set
	 */
	public function children($depth = null) {
		return $this->tree($depth);
	}
	
	/**
	 *	Returns indexed array of tree elements with tree leaves depth
	 *	
	 *	@param integer			$depth 		recursion depth of tree as number of levels to return, pass null for all levels
	 *	@param integer			$depthModel	association depth of models that are found, pass null for default model depth
	 * 	@return array(Model)
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
//		echo '<pre>'.$q.'</pre>';
		$r = $this->model->query($q, $depthModel);
		// crappy implementation of depth parameter!
		if ($depth > 0 && $r instanceof Set) {
			$baseLevel = 20000;
			foreach($r as $node){
				if ($node->level < $baseLevel) $baseLevel = $node->level;
			}
			foreach($r as $index => $Node) {
				if ($Node->level - $baseLevel > $depth - 1) {
					$r->delete($index);
				}
			}
		}
		// setting parents
		return $r;
	}
	
	/**
	 *	Returns the parent node of the current node if there’s any
	 *
	 *	This will return the Model that acts as parent for the current node if
	 *	there’s any. (if this Node isn’t the root node)
	 *	The result is cached in the model as $Model->Parent.
	 *	
	 *	@param integer $modelDepth optional model association depth when parent is found
	 * 	@return Model|boolean
	 */
	public function parent($modelDepth = null) {
		if (!$this->model->exists() || (int) $this->model->lft <= 1) {
			return false;
		}
		// retreive parent from model
		if (empty($this->model->Parent)) {
			$this->model->Parent = $this->model->findById($this->model->parent_id, $modelDepth);
		}
		return $this->model->Parent;
	}
	
	/**
	 *	Returns the path to the current node
	 *
	 *	This will return an indexed array with all parent nodes sorted by depth
	 *	including the current node if you set $includeCurrent to true.
	 *	Print path to current node:
	 *	<code>
	 *
	 *	</code>
	 *
	 *	The results of this method are also cached in this behavior in {$cachedPath}.
	 *
	 *	@param boolean $includeCurrent	Set to false if you don’t want to have the current node as first element of the returning array
	 *	@param	integer	$modelDepth		Model association depth of found nodes, deault is 0
	 * 	@return array(Node)|false
	 */
	public function path($includeCurrent = true, $modelDepth = 0) {
		if (!$this->model->exists()) {
			return false;
		}
		if (!empty($this->cachedPath)) {
			return $this->cachedPath;
		}
		$q = $this->model->createSelectQuery();
		$q->addComment($this->model->name.'->'.get_class($this).'->path(depth: '.$modelDepth.') id:'.$this->model->get($this->model->primaryKeyName));
		$q->where($this->model->lft.' BETWEEN '.$this->model->name.'.lft AND '.$this->model->name.'.rgt');
		if (!$includeCurrent) {
			$q->where($this->model->name.'.'.$this->model->primaryKeyName.' <> '.$this->model->get($this->model->primaryKeyName));
		}
		$q->orderBy($this->model->name.'.lft');
		$path = array();
		if ($r = $this->model->query($q, $modelDepth)) {
			$path = $r;
		}
		$this->cachedPath = $path;
		return $path;
	}
	
}

?>