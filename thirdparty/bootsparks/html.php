<?php namespace Bootsparks;

use Layla\HTML as Layla_HTML;

class HTML extends Layla_HTML {

	public static function h1($title)
	{
		return '<h1>'.$title.'</h1>';
	}

	public static function div($contents, $attributes = array())
	{
		return static::element('div', $contents, $attributes);
	}

	public static function element($type, $contents, $attributes = array())
	{
		return '<'.$type.static::attributes($attributes).'>'.$contents.'</'.$type.'>';
	}

}