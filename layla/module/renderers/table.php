<?php namespace Layla\Module\Renderers;

use Laravel\Session;

use Layla\Module;
use Layla\Module\Catcher;

use Bootsparks\Form;
use Bootsparks\HTML;

class Table extends Renderer {

	public $columns = array();

	public $sortable = array();

	public $rows = array();

	public $no_results;

	public function header($columns)
	{
		$this->columns = $columns;
	}

	public function sortable($sortable)
	{
		$this->sortable = $sortable;
	}

	public function rows($rows)
	{
		$this->rows = $rows;
	}

	public function no_results($children)
	{
		$this->no_results = Module::render($children);
	}

	public function display($options)
	{
		extract((array) $this);

		if(isset($rows->results))
		{
			$rows = $rows->results;
		}

		if(count($rows) === 0)
		{
			return $this->no_results;
		}

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

		return Module::render($display);
	}

}