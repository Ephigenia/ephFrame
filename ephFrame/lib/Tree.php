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
 * Abstract Tree class that represents a tree data structure
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 03.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @version 0.2 
 */
class Tree extends Object implements Countable, Iterator, Renderable
{	
	/**
	 * Stores the value for the current {@link Tree} entry
	 * @var mixed
	 */
	public $value;
	
	/**
	 * Stores an internal index value for the array (used for {@link isLast}
	 * @var integer
	 */
	public $index;
	
	/**
	 * Array of child branches
	 * @var array(Tree)
	 */
	public $children = array();
	
	/**
	 * The $level is the dimension of this tree branch
	 * @var integer
	 */
	public $level = 0;
	
	/**
	 * Parent Tree element, empty if no parent 
	 * @var Tree
	 */
	protected $parent = false;
	
	/**
	 * Internal iterator position
	 * @var integer
	 */
	protected $iteratorPosition = 0;
	
	/**
	 * Tree Constructor
	 * You can pass an array here to initial set the tree, see 
	 * docu for {@link fromArray} for more info, or you can pass
	 * this tree node's value
	 * @param string|array	$valueOrInitialArray
	 * @return Tree
	 */
	public function __construct($valueOrInitialArray = null) {
		if (is_array($valueOrInitialArray)) {
			$this->fromArray($valueOrInitialArray);
		} elseif (!empty($valueOrInitialArray)) {
			$this->value = $valueOrInitialArray;
		}
		return $this;
	}
	
	/**
	 * Creates a new Child Instance and returns it
	 * @return Tree
	 */
	public function newChild() {
		$className = get_class($this);
		$newChild = new $className();
		$newChild->level = $this->level + 1;
		return $newChild;
	}

	/**
	 * Adds a new Child to this tree branch
	 * @param Tree $child
	 * @return Tree
	 */
	public function addChild(Tree $child) {
		$child->parent($this);
		$child->index = count($this);
		$this->children[] = &$child;
		return $this;
	}
	
	/**
	 * Appends a new child Tree Element to the tree
	 * @param Tree
	 * @return Tree
	 */
	public function append(Tree $child) {
		return $this->addChild($child);
	}
	
	/**
	 * Prepend $child to the beginning of the children of this Treenode
	 * @param Tree
	 * @return Tree
	 */
	public function prepend(Tree $child) {
		$child->parent($this);
		array_unshift($this->children, $child);
		// renumber children indexes
		foreach($this->children as $index => $child) {
			$child->index = $index;
		}
		return $this;
	}
	
	/**
	 * Insert a Tree element to before this tree element in the parent
	 * children
	 * @param Tree
	 * @return Tree
	 */
	public function insertBefore(Tree $tree) {
		$tree->parent($this->parent);
		$newChildren = array_slice($this->parent->children, 0, $this->index);
		$newChildren[] = $tree;
		$newChildren = array_merge($newChildren, array_slice($this->parent->children, $this->index));
		foreach($newChildren as $index => $child) {
			$child->index = $index;
		}
		$this->parent->children = $newChildren;
		return $this;
	}
	
	/**
	 * Inserts a Tree element to the end of the parents children
	 * @param Tree
	 * @return Tree
	 */
	public function insertAfter(Tree $tree) {
		$tree->parent($this->parent);
		$newChildren = array_slice($this->parent->children, 0, $this->index+1);
		$newChildren[] = $tree;
		$newChildren = array_merge($newChildren, array_slice($this->parent->children, $this->index+1));
		foreach($newChildren as $index => $child) {
			$child->index = $index;
		}
		$this->parent->children = $newChildren;
		return $this;
	}
	
	/**
	 * Deletes this node from the tree and returns the result. False if this
	 * node has no parent or could not be deleted.
	 * @return boolean
	 */
	public function delete() {
		if (!$this->parent) return false;
		foreach($this->parent->children as $index => $child) {
			if ($child->index != $this->index) continue;
			unset($this->parent->children[$index]);
			foreach($this->parent->children as $index => $child) {
				$child->index = $index;
			}
		}
		return false;
	}
	
	/**
	 * Parses an Array and tries to put it into the tree
	 * strucure.
	 * 
	 * <code>
	 * $array = array(
	 * 	'first level', 'second branch',
	 * 	'more dimension' => array('I\'m more deep in', 'me also'
	 * 	));
	 * $tree = new Tree();
	 * 
	 * </code>
	 * @param Array(mixed) $input
	 * @return Tree
	 */
	public function fromArray(Array $input) {
		$className = get_class($this);
		foreach ($input as $key => $value) {
			$newChild = new $className($value);
			if (is_array($value)) {
				$newChild->value = $key;
			}
			$this->addChild($newChild);
		}
		return $this;
	}
	
	/**
	 * Increases the level of a branch
	 * @return Tree
	 */
	final private function increaseLevel() {
		$this->level += 1;
		if ($this->hasChildren()) {
			foreach ($this->children() as $child) {
				$child->increaseLevel();
			}
		}
		return $this;
	}
	
	/**
	 * Updates the level of this branch by looking
	 * at the parent. It also updates the children's levels
	 * @return Tree
	 */
	final private function updateLevel() {
		$this->level = $this->parent->level + 1;
		if ($this->hasChildren()) {
			foreach ($this->children() as $child) {
				$child->updateLevel();
			}
		}
		return $this;
	}
	
	/**
	 * Returns or sets the parent for this tree branch
	 * returns false if no parent was found (this branch may be the root
	 * element then)
	 * 
	 * @param Tree	$parent
	 * @return Tree
	 */
	final public function parent(Tree $parent = null) {
		if (func_num_args() == 0) return $this->parent;
		$this->parent = $parent;
		$this->updateLevel();
		return $this;
	}
	
	/**
	 * Returns the Root Tree Element for this Tree Element
	 * @return Tree
	 */
	final public function root(Tree $treeBranch = null) {
		if ($treeBranch === null) $treeBranch = $this;
		if ($treeBranch->isRoot()) return $treeBranch;
		return $this->root($this->getParent());
	}
	
	/**
	 * Alias for {@link root}
	 * @return Tree
	 */
	final public function getRoot() {
		return $this->root();	
	}
	
	/**
	 * Tries to iterate the tree for finding a tree node using the
	 * {@link ArrayHelper} method. If the search has no success false
	 * is returned
	 * @param string $path
	 */
	final public function extract($path) {
		class_exists('ArrayHelper') or require dirname(__FILE__).'helper/ArrayHelper.php';
		return ArrayHelper::extract($this->hash, $path);
	}
	
	/**
	 * Checks if this Tree Element is the Root Element
	 * @return boolean
	 */
	final public function isRoot() {
		return ($this->level === 0);
	}
	
	/**
	 * Checks if this tree entry (should be child)
	 * is the last one
	 * @return boolean
	 */
	final public function isLast() {
		if ($this->isRoot()) return false;
		if (empty($this->parent)) return false;
		return (count($this->parent)-1) == $this->index;
	}
	
	/**
	 * Returns all Children of this tree branch
	 *
	 * @return array(Tree)
	 */
	final public function children() {
		return $this->children;
	}
	
	/**
	 * Test if this Branch has Children
	 * @return boolean
	 */
	final public function hasChildren() {
		return count($this->children()) !== 0;
	}
	
	final public function isChild() {
		return ($this->level > 0 && !empty($this->parent));
	}
	
	final public function isParent() {
		return ($this->level === 0 && empty($this->parent));
	}
	
	/**
	 * Returns the first Child if found
	 * @return Tree|boolean
	 */
	final public function firstChild() {
		if (count($this) > 0) {
			return $this->children[0];	
		}
		return false;
	}
	
	/**
	 * Returns the child by index of the child
	 * @param integer	$index
	 */
	final public function getByIndex($index) {
		if (!is_integer()) throw new IntegerExpectedException();
		if (count($this) != $index) {
			throw new TreeIndexNotFoundException($this, $index);
		}
		return $this->children[$index];
	}
	
	/**
	 * Returns the Path to this child
	 * @return string
	 */
	public function path() {
		$path = '';
		if ($this->parent()) {
			$path .= $this->parent()->path().' > ';
		}
		$path .= $this->tagName;
		return $path;
	}
	
	/**
	 * Returns the last Child if found, you also can use {@link firstChild} if
	 * you need the very first child
	 * @return Tree
	 */
	final public function lastChild() {
		if (count($this) > 0) {
			return $this->children(count($this)-1);
		}
		return false;
	}
	
	/**
	 * Returns the rendered Tree as a string
	 * @return string
	 */
	public function render() {
		if (!$this->beforeRender()) return false;
		$rendered = '';
		if (!$this->isRoot()) {
			if ($this->level < 2) {
				$rendered = str_repeat('|--', $this->level - 1);
			} else {
				$rendered = str_repeat('|  ', $this->level - 1);
			}
			if ($this->isLast()) {
				$rendered .= '`--';
			} else {
				$rendered .= '|--';
			}
		}
		$rendered .= $this->value.LF;
		if ($this->hasChildren()) {
			foreach ($this->children() as $child) $rendered .= $child->render();
		}
		return $this->afterRender($rendered);
	}
	
	public function beforeRender() {
		return true;
	}
	
	public function afterRender($rendered) {
		return $rendered;
	}
	
	/**
	 * Returns the Tree rendered
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}
	
	/**
	 * Returns the number of children of this Tree in the
	 * first deeper dimension. Children from dimensions > 1 are
	 * not counted!
	 * @return integer
	 */
	public function count() {
		return count($this->children);
	}

	/**
	 * Part of countable interface integration
	 * @return boolean
	 */
	public function rewind() {
		$this->iteratorPosition = 0;
		reset($this->children);
		return true;
	}

	/**
	 * Part of countable interface integration
	 * @return Tree
	 */
	public function next() {
		$this->iteratorPosition++;
		next($this->children);
	}

	/**
	 * @return integer
	 */
	public function key() {
		return key($this->children);
	}

	/**
	 * Part of countable interface integration
	 * @return Tree
	 */
	public function current() {
		return current($this->children);
	}

	/**
	 * Part of countable interface integration
	 * @return boolean
	 */
	public function valid() {
		return ($this->iteratorPosition < $this->count());
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class TreeException extends ComponentException
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class TreeIndexNotFoundException extends TreeException
{
	public function __construct(Tree $tree, $index) {
		$this->message = 'There was no index like '.var_export($index, true).' in this Tree.';
		parent::__construct();
	}
}