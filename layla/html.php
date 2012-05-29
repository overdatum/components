<?php namespace Layla;

use Laravel\URL;
use Laravel\Input;
use Laravel\HTML as Laravel_HTML;

class HTML extends Laravel_HTML {

	public static function link($url, $title, $attributes = array(), $https = false)
	{
		$url = static::entities(URL::to($url, $https));

		return '<a href="'.$url.'"'.static::attributes($attributes).'>'.$title.'</a>';
	}

	public static function sort_link($url, $sort_by, $name)
	{
		return HTML::link($url.'?'.http_build_query(array_merge(Input::all(), array('sort_by' => $sort_by, 'order' => (Input::get('sort_by') == $sort_by ? (Input::get('order') == 'ASC' ? 'DESC' : 'ASC') : 'ASC')))), $name);
	}

}