<?php namespace Layla;

use Laravel\Form as Laravel_Form;

class Form extends Laravel_Form {
	
	/**
	 * Create a HTML button element.
	 *
	 * @param  string  $value
	 * @param  array   $attributes
	 * @return string
	 */
	public static function button($value, $attributes = array())
	{
		return '<button'.HTML::attributes($attributes).'>'.$value.'</button>';
	}

}