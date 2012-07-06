<?php
/**
 * Artifact - A View abstraction taken from Layla.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file licence.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@getlayla.com so I can send you a copy immediately.
 *
 * @package    Layla Components
 * @version    1.0
 * @author     Koen Schmeets <koen@getlayla.com>
 * @license    MIT License
 * @link       http://getlayla.com
 */

namespace Layla\Artifact\Renderers;

use Layla\Artifact;
use Layla\Artifact\Catcher;

use Laravel\Session;
use Laravel\Paginator;

use Bootsparks\Form;
use Bootsparks\HTML;

/**
 * This class provides a handy interface for working with tables
 */
class Table extends Renderer {

	/**
	 * The table columns
	 * 
	 * @var array
	 */
	public $columns = array();

	/**
	 * The columns that are sortable
	 * 
	 * @var array
	 */
	public $sortable = array();

	/**
	 * The rows or pagination object
	 * 
	 * @var mixed
	 */
	public $rows = array();

	/**
	 * The view to display when no rows were found
	 */
	public $no_results;

	/**
	 * Set the columns
	 * 
	 * @param array $columns
	 */
	public function header($columns)
	{
		$this->columns = $columns;
	}

	/**
	 * Set the sortable columns
	 * 
	 * @param array $sortable
	 */
	public function sortable($sortable)
	{
		$this->sortable = $sortable;
	}

	/**
	 * Set the rows
	 * 
	 * @param mixed $rows
	 */
	public function rows($rows)
	{
		$this->rows = $rows;
	}

	/**
	 * Set the no results view
	 * 
	 * @param Closure $view
	 */
	public function no_results($view)
	{
		$this->no_results = $view;
	}

	/**
	 * Display the table
	 * 
	 * // Add the description to the "roles" column 
	 * <code>
	 * $table->display(array(
	 * 		'roles' => function($account)
	 * 		{
	 * 			$html = '';
	 * 			if(isset($account->roles))
	 * 			{
	 * 				foreach($account->roles as $role)
	 * 				{
	 * 					$html .= '<b>'.$role->name.'</b><br>'.$role->description;
	 * 				}
	 *			}
	 * 			return $html;
	 * 		}
	 * ));
	 * </code>
	 * 
 	 * @param array $options custom display functions per column
 	 * @return string
	 */
	public function display($options)
	{
		$columns = $this->columns;
		$sortable = $this->sortable;
		$rows = $this->rows;

		if($rows instanceof Paginator)
		{
			$rows = $rows->results;
		}

		// Render and return the no_results view when no rows are found
		if(count($rows) === 0)
		{
			return Artifact::render($this->no_results);
		}

		// Create the table calls that will be sent off to the default Renderer later
		$display = function($table) use ($options, $columns, $sortable, $rows)
		{
			$table->thead(function($table) use ($columns, $sortable)
			{
				foreach ($columns as $column => $title)
				{
					if(is_array($title))
					{
						$title = array_key_exists('title', $title) ? $title['title'] : '';
					}

					$table->th(in_array($column, $sortable) ? HTML::sort_link('', $column, $title) : $title);
				}
			});

			$table->tbody(function($table) use ($rows, $columns, $options)
			{
				foreach ($rows as $row)
				{
					$table->tr(function($table) use ($row, $columns, $options)
					{
						foreach ($columns as $column => $title)
						{
							$attributes = array();	
							if(is_array($title))
							{
								extract($title);
							}
							
							$table->td( ! empty($column) ? (array_key_exists($column, $options) ? call_user_func($options[$column], $row) : $row->$column) : '', $attributes);
						}
					});						
				}
			});
		};

		// Render the table with the default Renderer and return the html
		return Artifact::render($display);
	}

}