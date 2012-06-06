<?php
/**
 * HTML extensions for Layla.
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

namespace Layla;

use Laravel\URL;
use Laravel\Input;
use Laravel\HTML as Laravel_HTML;

/**
 * This class adds some functionality to Laravel's HTML class
 */
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