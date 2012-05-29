<?php namespace Bootsparks;

use Laravel\HTML as Laravel_HTML;

class HTML extends Laravel_HTML {

	public static function h1($title)
	{
		return '<h1>'.$title.'</h1>';
	}

	public static function div($contents, $attributes)
	{
		return '<div'.static::attributes($attributes).'>'.$contents.'</div>';
	}

}