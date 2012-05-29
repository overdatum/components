<?php namespace Layla\Module;

class Fields {

	public $fields = array();

	public function nest($method, $callback)
	{
		$arguments = func_get_args();
		$arguments = array_slice($arguments, 2);
		
		$callback($children = new Fields);
		$field = array('nest' => array_merge(array($method, $children->fields), $arguments));
		$this->fields[] = $field;
	}

	public function __call($field, $arguments)
	{

		$this->fields[] = array($field => $arguments);
	}

}