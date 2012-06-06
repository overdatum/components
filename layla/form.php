<?php
/**
 * Form Extensions for Layla.
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

use Laravel\Form as Laravel_Form;

/**
 * This class adds some functionality to Laravel's Form class
 */
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