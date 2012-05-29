<?php
/**
 * Part of the Nesty bundle for Laravel.
 *
 * @package    Nesty
 * @version    1.0
 * @author     Cartalyst LLC
 * @license    MIT License
 * @copyright  (c) 2011 - 2012, Cartalyst LLC
 * @link       http://cartalyst.com
 */
namespace Nesty;

use DB;
use Crud;
use Log;

/**
 * Nesty model class.
 *
 * @author Ben Corlett
 */
class Nesty extends Crud
{

	/**
	 * Array of nesty column default names
	 * 
	 * We are using `lft` and `rgt` because
	 * `left` and `right` are reserved words
	 * in many databases, including MySQL
	 * 
	 * @var array
	 */
	protected static $_nesty_cols = array(
		'left'  => 'lft',
		'right' => 'rgt',
		'name'  => 'name',
		'tree'  => 'tree_id',
	);

	/**
	 * An array that contains all children models
	 * that have been retrieved from the database.
	 *
	 * @var array
	 */
	public $children = array();

	/**
	 * @todo support 
	 */
	protected static $_timestamps = false;

	/**
	 * Reloads the current model from the database.
	 *
	 * If you choose the 'override' option, all of your
	 * fields will be overridden with those from the database.
	 * If you don't, only the fields relevent to Nesty's operations
	 * will be.
	 *
	 * @param   bool  $override
	 * @return  Nesty
	 */
	public function reload($override_non_nesty = true)
	{
		if ($this->is_new())
		{
			throw new NestyException('You cannot call reload_nesty_cols() on a model that hasn\'t been persisted to the database');
		}

		// Get Attributes
		$attributes = $this->query()->where(static::key(), '=', $this->{static::key()})->first();

		// Overriding all
		if ($override_non_nesty === true)
		{
			$this->fill($attributes);
		}

		// Only overriding nesty cols
		else
		{
			$to_override = array();

			foreach (static::$_nesty_cols as $attribute)
			{
				$to_override[$attribute] = $attributes->{$attribute};
			}

			$this->fill($to_override);
		}

		return $this;
	}

	/**
	 * Alias for reload(), but only reloads
	 * Nesty cols.
	 *
	 * @return  Nesty
	 */
	public function reload_nesty_cols()
	{
		return $this->reload(false);
	}

	/**
	 * Get the size in the tree of this nesty.
	 *
	 * @return  int
	 */
	public function size()
	{
		return $this->{static::$_nesty_cols['right']} - $this->{static::$_nesty_cols['left']};
	}

	/*
	|--------------------------------------------------------------------------
	| Creating new trees / roots
	|--------------------------------------------------------------------------
	*/

	/**
	 * Makes the current model a root nesty.
	 *
	 * @todo Allow existing objects to move to
	 *       be root objects.
	 *
	 * @return  bool
	 */
	public function root()
	{
		// Create a new root nesty
		if ($this->is_new())
		{
			// Set the left and right limit of the nesty
			$this->{static::$_nesty_cols['left']}  = 1;
			$this->{static::$_nesty_cols['right']} = 2;

			// Tree identifier
			$this->{static::$_nesty_cols['tree']} = (int) $this->query()->max(static::$_nesty_cols['tree']) + 1;

			return $this->save();
		}

		// Already a root node
		elseif ($this->is_root())
		{
			return $this;
		}

		// Make an existing nesty a root
		else
		{
			// Remove existing from tree
			$this->remove_from_tree();

			// Move to new tree
			$this->move_to_tree((int) $this->query()->max(static::$_nesty_cols['tree']) + 1);

			// Reinsert in the tree, make our left 1 as
			// we're on a new tree
			return $this->reinsert_in_tree(1);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Assigning Children
	|--------------------------------------------------------------------------
	*/

	/**
	 * Make the current model the first child of
	 * the given parent.
	 *
	 * @param   Nesty  $parent
	 * @return  Nesty
	 */
	public function first_child_of(Nesty &$parent)
	{
		return $this->child_of($parent, 'first');
	}

	/**
	 * Make the current model the last child of
	 * the given parent.
	 *
	 * @param   Nesty  $parent
	 * @return  Nesty
	 */
	public function last_child_of(Nesty &$parent)
	{
		return $this->child_of($parent, 'last');
	}

	/**
	 * Make the current model either the first or
	 * last child of the given parent.
	 *
	 * @param   Nesty  $parent
	 * @return  Nesty
	 */
	public function child_of(Nesty &$parent, $position)
	{
		if ($parent->is_new())
		{
			throw new NestyException('The parent Nesty model must exist before you can assign children to it.');
		}

		if ( ! in_array($position, array('first', 'last')))
		{
			throw new NestyException(sprintf('Position %s is not a valid position.', $position));
		}

		// Reset cached children
		$parent->children = array();

		// If we haven't been saved to the database before
		if ($this->is_new())
		{
			// Setup our limits
			$this->{static::$_nesty_cols['left']} = ($position === 'first') ? $parent->{static::$_nesty_cols['left']} + 1 : $parent->{static::$_nesty_cols['right']};
			$this->{static::$_nesty_cols['right']} = $this->{static::$_nesty_cols['left']} + 1;

			// Set our tree identifier to match the parent
			$this->{static::$_nesty_cols['tree']} = $parent->{static::$_nesty_cols['tree']};

			// Create a gap a gap in the tree that starts at
			// our left limit and is 2 wide (the width of an empty
			// nesty)
			$this->gap($this->{static::$_nesty_cols['left']});

			// Reload parent
			$parent->reload_nesty_cols();

			$this->save();
		}

		// If we are existent in the database
		else
		{
			// Remove from tree
			$this->remove_from_tree();

			// Reload parent
			$parent->reload_nesty_cols();

			// If we are moving between trees
			if ($this->{static::$_nesty_cols['tree']} !== $parent->{static::$_nesty_cols['tree']})
			{
				$this->move_to_tree($parent->{static::$_nesty_cols['tree']});
			}

			// Determine our new left position
			$new_left = ($position === 'first') ? $parent->{static::$_nesty_cols['left']} + 1 : $parent->{static::$_nesty_cols['right']};

			// Reinsert in tree
			$this->reinsert_in_tree($new_left);

			// Because we have moved, reset our cached children
			$this->children = array();
		}

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| Assigning Siblings
	|--------------------------------------------------------------------------
	*/

	/**
	 * Make this model the previous sibling before
	 * given sibling.
	 *
	 * @param   Nesty  $sibling
	 */
	public function previous_sibling_of(Nesty &$sibling)
	{
		return $this->sibling_of($sibling, 'previous');
	}

	/**
	 * Make this model the next sibling after the
	 * given sibling.
	 *
	 * @param   Nesty  $sibling
	 */
	public function next_sibling_of(Nesty &$sibling)
	{
		return $this->sibling_of($sibling, 'next');
	}

	/**
	 * Make this model a sibling of the given sibling.
	 *
	 * @param   Nesty  $sibling
	 * @param   string $position
	 * @return  Nesty
	 */
	public function sibling_of(Nesty &$sibling, $position)
	{
		if ($sibling->is_new())
		{
			throw new NestyException('The sibling Nesty model must exist before you can assign new siblings to it.');
		}

		if ( ! in_array($position, array('previous', 'next')))
		{
			throw new NestyException(sprintf('Position %s is not a valid position.', $position));
		}

		// Reset cached children
		$sibling->children = array();

		// If we haven't been saved to the database before
		if ($this->is_new())
		{
			// Setup our limits
			$this->{static::$_nesty_cols['left']}  = ($position === 'previous') ? $sibling->{static::$_nesty_cols['left']} : $sibling->{static::$_nesty_cols['right']} + 1;
			$this->{static::$_nesty_cols['right']} = $this->{static::$_nesty_cols['left']} + 1;

			// Set our tree identifier to match the sibling
			$this->{static::$_nesty_cols['tree']} = $sibling->{static::$_nesty_cols['tree']};

			$this->gap($this->{static::$_nesty_cols['left']})
			     ->save();
		}

		// If we are existent in the database
		else
		{
			// Remove from tree
			$this->remove_from_tree();

			// Reload sibling
			$sibling->reload_nesty_cols();

			// If we are moving between trees
			if ($this->{static::$_nesty_cols['tree']} !== $sibling->{static::$_nesty_cols['tree']})
			{
				$this->move_to_tree($sibling->{static::$_nesty_cols['tree']});
			}

			// Determine our new left position
			$new_left = ($position === 'previous') ? $sibling->{static::$_nesty_cols['left']}: $sibling->{static::$_nesty_cols['right']} + 1;

			// Reinsert in tree
			$this->reinsert_in_tree($new_left);

			// Because we have moved, reset our cached children
			$this->children = array();
		}

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| Reading - getting children
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get the children for this model.
	 *
	 * @param   int   $limit
	 * @param   array $columns
	 * @return  array
	 */
	public function children($limit = false, $columns = array('*'))
	{
		// If we have set the children property as
		// false, there are no children
		if ($this->children === false)
		{
			return array();
		}

		// Lazy load children
		if (empty($this->children))
		{
			// Get an array of children from the database
			$children_array = $this->query_children_array($limit, $columns);

			// If we got an empty array of children
			if (empty($children_array))
			{
				$this->children === false;
				return $this->children();
			}

			// Hydrate our children. If hydrate children
			// returns false, there are no children for this
			// model. That means that $this->children === false,
			// so we call this same method again which handles empty
			// children
			if ($this->fill_children($children_array) === false)
			{
				return $this->children();
			}
		}

		return $this->children;
	}

	/**
	 * Queries the database for all children
	 * nodes of the current nesty model.
	 *
	 * This method is used in conjunction with
	 * Nesty::hydrate_children() by
	 * Nesty::get_children() [the public method]
	 * to retrieve a hierarchical array of children.
	 *
	 * @param   int   $limit
	 * @param   array $columns
	 * @return  array
	 */
	protected function query_children_array($limit = false, $columns = array('*'))
	{
		// Table name
		$table = static::table();

		// Primary key
		$key   = static::key();

		// Nesty cols
		extract(static::$_nesty_cols, EXTR_PREFIX_ALL, 'n');

		// Work out the columns to select
		$sql_columns = '';
		foreach ($columns as $column)
		{
			$sql_columns .= ' `nesty`.'.($column == '*' ? $column : '`'.$column.'`');
		}


		// This is the magical query that is the sole
		// reason we're using the MPTT pattern
		$sql = <<<QUERY
SELECT   $sql_columns,
         (COUNT(`parent`.`$key`) - (`sub_tree`.`depth` + 1)) AS `depth`

FROM     `$table` AS `nesty`,
         `$table` AS `parent`,
         `$table` AS `sub_parent`,
         (
             SELECT `nesty`.`$key`,
                    (COUNT(`parent`.`$key`) - 1) AS `depth`

             FROM   `$table` AS `nesty`,
                    `$table` AS `parent`

             WHERE  `nesty`.`$n_left`  BETWEEN `parent`.`$n_left` AND `parent`.`$n_right`
             AND    `nesty`.`$key`     = {$this->{static::key()}}
             AND    `nesty`.`$n_tree`  = {$this->{$n_tree}}
             AND    `parent`.`$n_tree` = {$this->{$n_tree}}

             GROUP BY `nesty`.`$key`

             ORDER BY `nesty`.`$n_left`
         ) AS `sub_tree`

WHERE    `nesty`.`$n_left`   BETWEEN `parent`.`$n_left`     AND `parent`.`$n_right`
AND      `nesty`.`$n_left`   BETWEEN `sub_parent`.`$n_left` AND `sub_parent`.`$n_right`
AND      `sub_parent`.`$key` = `sub_tree`.`$key`
AND      `nesty`.`$n_tree`   = {$this->{$n_tree}}
AND      `parent`.`$n_tree`  = {$this->{$n_tree}}

GROUP BY `nesty`.`$key`

HAVING   `depth` > 0
QUERY;

		// If we have a limit
		if ($limit)
		{
			$sql .= PHP_EOL.'AND      `depth` <= '.$limit;
		}

		// Finally, add an ORDER BY
		$sql .= str_repeat(PHP_EOL, 2).'ORDER BY `nesty`.`'.$n_left.'`';

		// And return the array of results
		return DB::query($sql);
	}

	/**
	 * Fills the children property of this model
	 * hierarchically using the flat array provided
	 *
	 * @param   array  $children
	 * @return  Nesty
	 */
	protected function fill_children(array $children_array = array())
	{
		// Set up some vars used for
		// iterating
		$l     = 0;
		$stack = array();

		foreach ($children_array as $child)
		{
			// Create an existing model
			$nesty = new static($child);

			// Number of stack items
			$l = count($stack);

			// Check if we're dealing with different levels
			while ($l > 0 and $stack[$l - 1]->depth >= $nesty->depth)
			{
				array_pop($stack);
				$l--;
			}

			// Stack is empty (we are inspecting the root)
			if ($l == 0)
			{
				// Assigning the root nesty
				$i = count($this->children);
				$this->children[$i] = $nesty;
				$stack[] = &$this->children[$i];
			}

			// Add nesty to parent
			else
			{
				$i = count($stack[$l - 1]->children);
				$stack[$l - 1]->children[$i] = $nesty;
				$stack[] = &$stack[$l - 1]->children[$i];
			}

			// If the child has no children,
			// set the children property to false
			// so next time they're queried it saves
			// another database query
			if (empty($nesty->children))
			{
				$nesty->children = false;
			}
		}

		// If we have no children, return false
		// as Nesty::children() handles that for us
		if (empty($this->children))
		{
			return false;
		}

		return $this;
	}

	/*
	|--------------------------------------------------------------------------
	| Static Usage
	|--------------------------------------------------------------------------
	*/

	/**
	 * Returns the name of a Nesty column
	 *
	 * @param  string  $column
	 * @return string
	 */
	public static function nesty_col($column)
	{
		return static::$_nesty_cols[$column];
	}

	/**
	 * Creates or updates a Nesty tree structure based on
	 * the hierarchical array of items passed through. 
	 *
	 * @param  int    $id
	 * @param  array  $items
	 * @return Nesty
	 */
	public static function from_hierarchy_array($id, array $items)
	{
		if ($id)
		{
			$root = static::find($id);

			if ($root === null)
			{
				throw new \Exception('Trying to update from non-existent root Nesty model.');
			}

			if ( ! $root->is_root())
			{
				throw new \Exception('Passing ID of non-root Nesty model.');
			}
		}
		else
		{
			// Firstly, create a root model
			$root = new static(array(
				'name' => 'Root Item',
			));
			$root->root();
		}

		// Loop through items
		foreach ($items as $item)
		{
			$root->reload_nesty_cols();
			// $root = static::find(1);
			static::recursive_from_array($item, $root);
		}
	}

	protected static function recursive_from_array(array $item = array(), Nesty &$parent)
	{
		if ($children = (isset($item['children']) and is_array($item['children']) and count($item['children']) > 0) ? $item['children'] : false)
		{
			unset($item['children']);
		}

		// Are we creating a new item or
		// updating existing item?
		if (isset($item[static::key()]))
		{
			$item_m = static::find($item[static::key()]);

			if ($item_m === null)
			{
				throw new \Exception('Trying to update from non-existent Nesty model.');
			}

			$item_m->last_child_of($parent)
			       ->reload_nesty_cols()
			       ->save();
		}
		else
		{
			$item_m = new static($item);
			$item_m->last_child_of($parent)->reload_nesty_cols()
			       ->save();
		}

		if ($children !== false)
		{
			foreach ($children as $child)
			{
				static::recursive_from_array($child, $item_m);
			}
		}
	}

	/*
	|-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
	| Nesty helper methods
	|-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*-*
	*/

	/**
	 * Get all the attributes of the model.
	 *
	 * @return array
	 */
	public function attributes()
	{
		$attributes = get_object_public_vars($this);
		unset($attributes['children']);
		return $attributes;
	}

	/**
	 * Determines if the nesty is a root model.
	 *
	 * @return bool
	 */
	public function is_root()
	{
		return $this->{static::$_nesty_cols['left']} == 1;
	}

	/**
	 * Move a nesty to a new tree
	 *
	 * @param   int  $tree
	 * @return  Nesty
	 */
	protected function move_to_tree($tree)
	{
		// $this->{static::$_nesty_cols['tree']} = $tree;
		// $this->save();

		// We move this and all children to the new tree
		$this->query()
		     ->where(static::$_nesty_cols['left'], 'BETWEEN', DB::raw($this->{static::$_nesty_cols['left']}.' AND '.$this->{static::$_nesty_cols['right']}))
		     ->where(static::$_nesty_cols['tree'], '=', $this->{static::$_nesty_cols['tree']})
		     ->update(array(
		     	static::$_nesty_cols['tree'] => $tree,
		     ));

		// Reset cached children
		$this->children = array();

		return $this->reload_nesty_cols();
	}

	/**
	 * Used to keep a nesty model in the database but
	 * remove it from the tree structure. Used when
	 * moving nesties around.
	 *
	 * @return  Nesty
	 */
	protected function remove_from_tree()
	{
		// We need to move our nesty so it's outside the tree.
		// For this, our change must bring our right limit to be
		// 0.
		$delta = 0 - $this->{static::$_nesty_cols['right']};

		// Move this model and it's children outside
		// the tree by changing all limits by the delta
		// calculdated above
		$this->query()
		     ->where(static::$_nesty_cols['left'], 'BETWEEN', DB::raw($this->{static::$_nesty_cols['left']}.' AND '.$this->{static::$_nesty_cols['right']}))
		     ->where(static::$_nesty_cols['tree'], '=', $this->{static::$_nesty_cols['tree']})
		     ->update(array(
		     	static::$_nesty_cols['left'] => DB::raw('`'.static::$_nesty_cols['left'].'` + '.$delta),
		     	static::$_nesty_cols['right'] => DB::raw('`'.static::$_nesty_cols['right'].'` + '.$delta),
		     ));

		// Remove the gap we created - notice the '-' on the second param
		$this->gap($this->{static::$_nesty_cols['left']}, - ($this->size() + 1));

		return $this->reload_nesty_cols();
	}

	/**
	 * Reverse of Nesty::remove_from_tree(). Used to
	 * reinsert a nesty back into the tree structure
	 *
	 * @param   int  $left
	 * @return  Nesty
	 */
	protected function reinsert_in_tree($left)
	{
		// Create a gap
		$this->gap($left);

		// Reinsert in new gap by moving everything between
		// the limits the right delta 
		$this->query()
		     ->where(static::$_nesty_cols['left'], 'BETWEEN', DB::raw((0 - $this->size()).' AND 0'))
		     ->where(static::$_nesty_cols['tree'], '=', $this->{static::$_nesty_cols['tree']})
		     ->update(array(
		     	static::$_nesty_cols['left'] => DB::raw('`'.static::$_nesty_cols['left'].'` + '.($left + $this->size())),
		     	static::$_nesty_cols['right'] => DB::raw('`'.static::$_nesty_cols['right'].'` + '.($left + $this->size())),
		     ));

		return $this;
	}

	/**
	 * Create a gap in the tree.
	 *
	 * @param   int  $start
	 * @param   int  $size
	 * @param   int  $tree
	 * @return  Nesty
	 */
	protected function gap($start, $size = null, $tree = null)
	{
		if ($size === null)
		{
			$size = $this->size() + 1;
		}

		if ($tree === null)
		{
			$tree = $this->{static::$_nesty_cols['tree']};
		}

		$this->query()
		      ->where(static::$_nesty_cols['left'], '>=', $start)
		      ->where(static::$_nesty_cols['tree'], '=', $tree)
		      ->update(array(
		      	static::$_nesty_cols['left'] => DB::raw('`'.static::$_nesty_cols['left'].'` + '.$size),
		      ));

		$this->query()
		      ->where(static::$_nesty_cols['right'], '>=', $start)
		      ->where(static::$_nesty_cols['tree'], '=', $tree)
		      ->update(array(
		      	static::$_nesty_cols['right'] => DB::raw('`'.static::$_nesty_cols['right'].'` + '.$size),
		      ));

		return $this;
	}

}