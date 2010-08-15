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

ephFrame::loadClass('ephFrame.lib.Collection');

/**
 * Abstract DB Query Class
 * 
 * This class should be father of all query classes in the framework and
 * in your project.
 * 
 * See the different kinds of query classes, such as {@link DBSelectQuery},
 * {@DBDeleteQuery} for their usage.
 * 
 * // todo create class constants for possible verbs
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 19.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.model.DB
 * @version 0.2.1
 * @uses Collection
 * @uses Hash
 */
abstract class DBQuery extends Object implements Renderable
{	
	const ORDER_ASC = 'ASC';
	const ORDER_DESC = 'DESC';
	
	const JOIN = 'JOIN';
	const JOIN_LEFT = 'LEFT JOIN';
	const JOIN_RIGHT = 'RIGHT JOIN';
	
	const FLAG_LOW_PRIORITY = 'LOW_PRIORITY';
	const FLAG_HIGH_PRIORITY = 'HIGH_PRIORITY';
	const FLAG_DELAYED = 'DELAYED';
	const FLAG_IGNORE = 'IGNORE';
	
	/**
	 * @var string
	 */
	public $verb;
	
	/**
	 * @var Hash
	 */
	public $select;
	/**
	 * @var Collection
	 */
	public $tables;
	/**
	 * @var Collection
	 */
	public $join;
	/**
	 * @var Collection
	 */
	public $orderBy;
	/**
	 * @var Collection
	 */
	public $groupBy;
	/**
	 * @var Collection
	 */
	public $having;
	/**
	 * @var Hash
	 */
	public $values;
	/**
	 * @var Hash
	 */
	public $where;
	
	public $offset = 0;
	/**
	 * @var integer
	 */
	public $count;
	/**
	 * Stores the flags for this query
	 * @var Collection
	 */
	public $flags;
	
	/**
	 * Some flags that can be turned of using {@link addFlag}
	 * @var boolean
	 */
	public $highPriority = false;
	public $lowPriority = false;
	public $delayed = false;
	public $ignore = false;
	
	/**
	 * Adds a New Line Character after every part of the query
	 * @var boolean
	 */
	public $autoNewLine = true;
	
	/**
	 * Creates a Database SQL Query object with the passed verb as command.
	 * @param string $verb
	 * @return DBQuery
	 */
	public function __construct($verb = null, $table = null, $conditions = array(), $values = array()) 
	{
		if ($verb !== null) $this->verb($verb);
		ephFrame::loadClass('ephFrame.lib.component.Collection');
		$this->reset();
		// initial values
		if ($table !== null) {
			$this->table($table);
		}
		if (!is_string($values) && @count($values) > 0) {
			$this->values($values);
		} elseif (!empty($values)) {
			$this->value(null, $values);
		}
		if (is_string($conditions)) {
			$this->where($condition);
		} elseif (is_array($conditions) && count($conditions) > 0) {
			$this->where->merge($conditions);
		}
		return $this;
	}
	
	/**
	 * Clears all settings for this query
	 * @return DBQuery
	 */
	public function reset() 
	{
		$this->select = new Hash();
		$this->tables = new Collection();
		$this->join = new Hash();
		$this->having = new Collection();
		$this->groupBy = new Collection();
		$this->orderBy = new Collection();
		$this->where = new Hash();
		$this->values = new Hash();
		$this->flags = new Collection();
		return $this;
	}
	
	/**
	 * Quote the argument so that should be safe agains SQL Injections.
	 * 
	 * The passed $val is quoted after it's type. Some types can not be quoted
	 * correctly: Arrays, Objects, Resources - if one of these types arrives in
	 * Quote only their type will be quoted.
	 * 
	 * String values don't need any quoting around it:
	 * <code>
	 * // will result in "SELECT * FROM user WHERE username = 'username'"
	 * $query = 'SELECT * FROM user WHERE username = '.DBQuery::quote('username');
	 * </code>
	 * 
	 * You also can force a specific quoting type by passing the second param
	 * $type to the method.
	 * 
	 * @param mixed $val
	 * @param string $type other type to use while quoting
	 * @return string
	 */
	public static function quote($val, $type = null)
	{
		if ($type === null) {
			$type = gettype($val);
		}
		// do not quot mysql NOW() and NULL
		if (preg_match('@^(NOW\(\)|NULL)$@i', $val))  {
			$quoted = $val;
		// quote everything else
		} else {
			switch($type) {
				case 'NULL':
					$quoted = 'NULL';
					break;
				case 'integer':
					$quoted = (int) $val;
					break;
				case 'float':
				case 'double':
					$quoted = (float) $val;
					break;
				case 'boolean':
					if ($val) {
						$quoted = 1;
					} else {
						$quoted = 0;
					}
					break;
				case 'string':
					$quoted = '\''.mysql_real_escape_string($val).'\'';
					break;
				// non scalar values are not added, but their variable type
				default:
					$quoted = '\''.mysql_real_escape_string(gettype($val)).'\'';
					break;
			}
		}
		return $quoted;
	}
	
	/**
	 * Template for the rendered query, every keyword starting with a % character
	 * is translated into a render[keyword] method call. This is some kind
	 * of meta language for query rendering ;-)
	 * @var array(string)
	 */
	public $renderTemplate = '%verb %flags %select FROM %tables %join %where %groupBy %having %orderBy %limit';

	/**
	 * Renders the query using the {@link renderTemplate} and returns it
	 * @return string
	 */
	public function render() 
	{
		if (!$this->beforeRender()) return false;
		$renderParts = explode(' ', $this->renderTemplate);
		$rendered = '';
		foreach ($renderParts as $index => $renderPartName) {
			if (substr($renderPartName, 0, 1) == '%') {
				$keyword = substr($renderPartName, 1);
				if ($keyword == 'verb') {
					$renderedPart = $this->verb;
				} else {
					$renderMethodName = 'render'.ucFirst($keyword);
					$renderedPart = $this->$renderMethodName();
				}
			} else {
				$renderedPart = $renderPartName;
			}
			if (!empty($renderedPart)) {
				if ($this->autoNewLine) {
					$rendered .= $renderedPart.LF;
				} else {
					$rendered .= $renderedPart.' ';
				}
			}
		}
		return $this->afterRender($rendered);
	}
	
	/**
	 * This method can be overwritten in any subclass ot add pre-rendering
	 * logic to the query. This is used in this abstract class to add the 
	 * flags to the query.
	 * This method should always return true if the query should be rendered.
	 * A false halts the rendering process.
	 * @return boolean
	 */
	public function beforeRender() 
	{
		if ($this->highPriority) {
			$this->addFlag(DBQuery::FLAG_HIGH_PRIORITY);
		}
		if ($this->lowPriority) {
			$this->addFlag(DBQuery::FLAG_LOW_PRIORITY);
		}
		if ($this->delayed) {
			$this->addFlag(DBQuery::FLAG_DELAYED);
		}
		if ($this->ignore) {
			$this->addFlag(self::FLAG_IGNORE);
		}
		return true;
	}
	
	/**
	 * Here you can add after-rendering logic. The easiest example is to lowercase
	 * everything in the query or check for xss attacks (which should be
	 * impossible somehow)
	 * @param string $rendered
	 * @return string
	 */
	public function afterRender($rendered) 
	{
		$rendered = trim($rendered);
		return $rendered;
	}
	
	/**
	 * Adds an other table to the set of tables to run the sql command on.
	 * <code>
	 * // ... FROM my_blog_entries entries ...
	 * $query->from('my_blog_entries', 'entries');
	 * // pass some more tables
	 * $query->from(array('my_blog_entries', 'my_comments'));
	 * </code>
	 * @param string $tablename Name of the table in the DB, optional with Database Name
	 * @param string $alias optional table alias
	 */
	public function table($tablename, $alias = null) 
	{
		if (is_array($tablename)) {
			foreach($tablename as $value) {
				$this->table($tablename);	
			}
		} else {
			$tablename = trim($tablename);
			$alias = trim($alias);
			assert(is_string($tablename) && !empty($tablename));
			$this->tables->add(array($tablename, $alias));
		}
		return $this;
	}
	
	/**
	 * Add a join statement
	 * @param string $tablename name of table that should be joined
	 * @param string $alias optional table alias
	 * @param string $type join type to use, use the JOIN_* constants of this class
	 * @param array() $conditions Join conditions, rendered as where statement
	 */
	public function join($tablename, $alias = null, $type = self::JOIN, $conditions = array()) 
	{
		if (!is_array($conditions)) {
			$conditions = array($conditions);
		}
		$this->join->add($alias, array($tablename, $alias, $type, $conditions));
		return $this;
	}
	
	/**
	 * Selects the verb for the SQL Query, that is the command for the query.
	 * Some options may be SELECT, UPDATE, INSERT ...
	 * @param string $verb
	 * @return DBQuery
	 */
	public function verb($verb = null) 
	{
		if (func_num_args() == 0) return $this->verb;
		if (empty($verb)) throw new StringExpectedException();
		assert(is_string($verb) && !empty($verb));
		$this->verb = strtoupper($verb);
		return $this;
	}
	
	/**
	 * Adds a single value statement to the values/insert statement, consisting
	 * of the fieldname and the desired value. Some Examples:
	 * <code>
	 * // .... VALUES(created=120382103, title=`ephFrame seemes to be goood!`) ...
	 * $query->value('created' => time());
	 * $query->value('title' => 'ephFrame seemes to be goood!');
	 * </code>
	 * @param string $fieldname
	 * @param mixed $value
	 * @return DBQuery
	 */
	public function value($fieldname, $value) 
	{
		$fieldname = trim($fieldname);
		assert(is_string($fieldname) && !empty($fieldname));
		$this->values->add($fieldname, $value);
		return $this;
	}
	
	/**
	 * Adds multiple pairs of key values to the values array of the query that
	 * is rendered depending on the {@link verb} property
	 * <code>
	 * $query->values(array('foo' => 'bar', 'country', 23);
	 * </code>
	 * @param array(string) $array
	 * @return DBQuery
	 */
	public function values(Array $array = array()) 
	{
		$this->values = new Hash($array);
		return $this;
	}
	
	/**
	 * Adds a groupBy Statement
	 *
	 * @param string $fieldname
	 * @return DBQuery
	 */
	public function groupBy($fieldname) 
	{
		$fieldname = trim($fieldname);
		assert(is_string($fieldname) && !empty($fieldname));
		$this->groupBy->add($fieldname);
		return $this;
	}
	
	/**
	 * Adds an other statement to the orderby statement :)
	 * The second parameter is interpreted as a second orderby statement if it's
	 * not a valid order Direction
	 * <code>
	 * // ORDERBY created DESC
	 * $query->orderBy('created', DBQuery::DESC);
	 * // ORDERBY created DESC, id DESC
	 * $query->orderBy('created DESC', 'id DESC');
	 * </code>
	 * 
	 * You can remove previously made orderBy Statements by removing them:
	 * <code>
	 * $query->orderBy->remove('created');	
	 * </code>
	 * @param string $fieldname
	 * @param string $direction
	 * @return DBQuery
	 */
	public function orderBy($fieldname, $direction = null) 
	{
		$fieldname = trim($fieldname);
		$direction = trim($direction);
		assert(is_string($fieldname) && !empty($fieldname));
		$this->orderBy->add($fieldname.($direction !== null ? ' '.$direction : ''));
		return $this;
	}
	
	/**
	 * Shortcut for setting the LIMIT parameters. You can also use both
	 * seperated by adressing {@link offset} and {@link count}
	 * <code>
	 * // limit a query ... LIMIT 20, 30
	 * $query->limit(20, 30);
	 * </code>
	 * @param integer $offset
	 * @param integer $count
	 * @return DBQuery
	 */
	public function limit($offset, $count) 
	{
		return $this->offset($offset)->count($count);
	}
	
	/**
	 * Sets the LIMIT offset of this query or returns it
	 * @param integer $offset
	 * @return integer|DBQuery
	 */
	public function offset($offset = null) 
	{
		if ($offset < 0) return $this;
		return $this->__getOrSet('offset', $offset);
	}
	
	/**
	 * Sets the LIMIT count of this query or returns it
	 * @param integer $count
	 * @return integer
	 */
	public function count($count = null) 
	{
		if ($count < 0 || $count == null) return $this;
		return $this->__getOrSet('count', $count);
	}
	
	/**
	 * Adds some stuff to the where statement of the query.
	 * Some examples below should explain the different possibilities.
	 * <code>
	 * // add a where statement just as it is, this may be unsave if not
	 * // checked or protected against manipulation from 3rd persons.
	 * $query->where('foo=bar');
	 * </code>
	 *
	 * @param string $key
	 * @param string $value
	 * @return DBQuery
	 */
	public function where($key, $right = null) 
	{
		if (func_num_args() == 1) {
			$this->where->add($key);
		} else {
			$this->where->add($key, $right);
		}
		return $this;
	}
	
	/**
	 * Add a flag statement to the query, like IGNORE, LOW_PRIORITY etc.
	 * The sub-classes, like {@link DBUpdateQuery}, {@link DBSelectQuery}
	 * implement own properties for the flags, so you can use them.
	 * 
	 * <code>
	 * // results in something like SELECT HIGH_PRIORITY FROM ...
	 * $query->addFlag('HIGH_PRIORITY');
	 * </code>
	 * An other possible usage for the flags is adding comment style for 
	 * enabling query running post processing - like directing queries to
	 * other hosts with a specific comment or other statistic comments.
	 * <code>
	 * $query->addFlag('host2', true);
	 * </code>
	 * 
	 * Double values will be ignored
	 * @param string $flag
	 * @return DBQuery
	 */
	public function addFlag($flag, $asComment = false) 
	{
		if ($asComment) {
			$flag = '/* '.$flag.' */';
		}
		$this->flags->add($flag);
		return $this;
	}
	
	/**
	 * Adds a comment to the query
	 * @param string $comment
	 */
	public function addComment($comment) 
	{
		$this->flags->add('/* '.$comment.' */');
		return $this;
	}
	
	/**
	 * @return string
	 */
	protected function renderValues() {
		$rendered = '';
		foreach($this->values as $key => $value) {
			$rendered .= '`'.$key.'`='.$value.', ';
		}
		return substr($rendered, 0, -2);
	}
	
	/**
	 * @return string
	 */
	protected function renderSelect() {
		if (count($this->select) == 0) {
			return '';
		}
		$rendered = '';
		foreach($this->select as $fieldname => $alias) {
			if (empty($fieldname)) {
				$rendered .= $alias;
			} else {
				$rendered .= $fieldname;
				if (!empty($alias)) {
					$rendered .= ' as \''.$alias.'\'';
				}
			}
			$rendered .= ', ';
		}
		return substr($rendered, 0, -2);
	}
	
	/**
	 * @return string
	 */
	protected function renderOrderBy()
	{
		$rendered = $this->orderBy->implode(', ');
		if (!empty($rendered)) {
			return ' ORDER BY '.LF.TAB.$rendered;
		} else {
			return false;
		}
	}
	
	/**
	 * Renders the statement of the SQL Query with the tables in it
	 * @param array(string) $tables
	 * @return string
	 */
	protected function renderTables($tables = array())
	{
		if (func_num_args() == 0) {
			$tables = $this->tables;
		}
		if (!count($tables)) {
			return null;
		}
		$from = '';
		foreach ($tables as $fromArr) {
			if (!empty($fromArr[1])) {
				$from .= $fromArr[0].' `'.$fromArr[1].'`, ';
			} else {
				$from .= $fromArr[0].', ';
			}
		}
		if (count($tables) > 1) {
			return '('.substr($from, 0, -2).')';
		} else {
			return substr($from, 0, -2);
		}
	}
	
	/**
	 * @return string
	 */
	protected function renderFlags() {
		return $this->flags->implode(' ');
	}
	
	/**
	 * @param array(string) $where
	 * @return string
	 */
	protected function renderWhere($whereConditions = array(), $quote = false) {
		if (func_num_args() == 0) {
			$whereConditions = $this->where;
		}
		if ($rendered = $this->renderConditions($whereConditions, $quote)) {
			return 'WHERE '.LF.$rendered;
		}
	}
	
	public function renderConditions($conditions, $quote = true) 
	{
		if (count($conditions) == 0) {
			return null;
		}
		$rendered = '';
		foreach($conditions as $left => $right) {
			$connector = ' = ';
			if (is_array($right)) {
				$right = 'IN ('.implode(',', array_map(array($this, 'quote'), $right) ).')';
			// skip connector if allready there (bad workaround)	
			} elseif ($right === null) {
				$right = 'NULL';
				$connector = ' is ';
			}
			if (preg_match('@^\s*(<|>|=|LIKE|IN)@i', $right) || preg_match('@<>@', $left)) {
				$connector = ' ';
			}
			// todo create cool condition array that can map all conditions possible
			if (is_int($left)) {
				$rendered .= $right.' ';
			} else {
				if ($quote) {
					$rendered .= $left.$connector.self::quote($right);
				} else {
					$rendered .= $left.$connector.$right;
				}
			}
			if (!preg_match('/((AND|OR)\s*$)|(^\s*(AND|OR))/', $right)) {
				$rendered .= ' AND ';
			}
		}
		return substr($rendered, 0, -4);
	}
	
	/**
	 * @param array(string) $join
	 * @return string
	 */
	public function renderJoin($join = array()) 
	{
		if (func_num_args() == 0) {
			$join = $this->join;
		}
		if (count($join) == 0) {
			return null;
		}
		$rendered = '';
		foreach($join as $joinData) {
			list($tablename, $alias, $joinType, $conditions) = $joinData;
			$rendered .= $joinType.' `'.$tablename.'`';
			if (!empty($alias)) {
				$rendered .= ' '.$alias;
			}
			if ($conditionsRendered = $this->renderConditions($conditions, false)) {
				$rendered .= ' ON '.$conditionsRendered;
			}
			$rendered .= LF;
		}
		return substr($rendered,0,-1);
	}
	
	/**
	 * @return string
	 */
	private function renderGroupBy() {
		if (count($this->groupBy) <= 0) {
			return null;
		}
		return 'GROUP BY '.$this->groupBy->implode(', ');
	}
	
	/**
	 * @return string
	 */
	private function renderHaving() {
		if (!count($this->having)) {
			return null;
		}
		return 'HAVING '.$this->having->implode(', ');
	}
	
	/**
	 * Renders the limit part of the query
	 * @return string
	 */
	final private function renderLimit() {
		if (empty($this->offset) && empty($this->count)) return null;
		$rendered = 'LIMIT '.(int) $this->offset;
		if ($this->count > 0) {
			$rendered .= ', '.(int) $this->count;
		}
		return $rendered;
	}
	
	/**
	 * Returns the rendered query
	 * @return string
	 */
	public function __toString() 
	{
		return $this->render();
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class DBQueryException extends BasicException
{}