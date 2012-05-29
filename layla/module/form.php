<?php namespace Layla\Module;

class Form {

	public static $messages = array();

	public function validates()
	{
		$validator = Validation::make(Input::all(), static::$rules, static::$messages);

		if($validator->fails())
		{
			$this->errors = $validator;
			return false;
		}

		return true;
	}

}