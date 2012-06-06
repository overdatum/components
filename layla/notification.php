<?php
/**
 * Notification library for Layla.
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

use Laravel\Session;
use Laravel\View;

/**
 * This class provides a simple interface for adding and displaying notifications
 */
class Notification  {

	/**
	 * All of the added notifications.
	 *
	 * @var array
	 */
	public static $notifications = array();

	/**
	 * Method for adding notifications to be shown on the next request.
	 *
	 * <code>
	 *		// Add a succes notification that will show for 3 seconds with a close button
	 *		echo Notification::success('It worked!', 3000, true);
	 * </code>
	 * 
	 * @param  string  	$message	The message to display
	 * @param  int  	$time		The time to display the message
 	 * @param  boolean  $close		Add a close button
	 * @return void
	 */
	public static function success($message, $time = 5000, $close = false)
	{
		static::add('success', $message, $time, $close);
	}

	/**
	 * Method for adding notifications to be shown on the next request.
	 *
	 * <code>
	 *		// Add a error notification that will show for 3 seconds with a close button
	 *		echo Notification::error('Whoops...', 3000, true);
	 * </code>
	 * 
	 * @param  string  	$message	The message to display
	 * @param  int  	$time		The time to display the message
 	 * @param  boolean  $close		Add a close button
	 * @return void
	 */
	public static function error($message, $time = 5000, $close = false)
	{
		static::add('error', $message, $time, $close);
	}

	/**
	 * Method for adding notifications to be shown on the next request.
	 *
	 * <code>
	 *		// Add a warning notification that will show for 3 seconds with a close button
	 *		echo Notification::warning('Are you sure?!', 3000, true);
	 * </code>
	 * 
	 * @param  string  	$message	The message to display
	 * @param  int  	$time		The time to display the message
 	 * @param  boolean  $close		Add a close button
	 * @return void
	 */
	public static function warning($message, $time = 5000, $close = false)
	{
		static::add('warning', $message, $time, $close);
	}

	/**
	 * Method for adding notifications to be shown on the next request.
	 *
	 * <code>
	 *		// Add a info notification that will show for 3 seconds with a close button
	 *		echo Notification::info('Just so you know...', 3000, true);
	 * </code>
	 * 
	 * @param  string  	$message	The message to display
	 * @param  int  	$time		The time to display the message
 	 * @param  boolean  $close		Add a close button
	 * @return void
	 */
	public static function info($message, $time = 5000, $close = false)
	{
		static::add('info', $message, $time, $close);
	}

	/**
	 * Method for displaying all notifications that have been added on the previous request.
	 *
	 * <code>
	 *		// Add a info notification that will show for 3 seconds with a close button
	 *		echo Notification::show();
	 * </code>
	 * 
	 * @author Nick <nick@getlayla.com>
	 */
	public static function show()
	{
		$notifications = Session::get('notifications');
			if(count($notifications) > 0)
			{
				return View::make('admin::layouts.notifications')->with('notifications', $notifications);
			}
	}

	/**
	 * Add a notification of any sort
	 *
	 * @param  string  	$type
	 * @param  string  	$message
	 * @param  int  	$time
 	 * @param  boolean  $close
	 * @return void
	 */
	protected static function add($type, $message, $time, $close)
	{
		static::$notifications[] = array(
			'type' => $type,
			'message' => $message,
			'time' => $time,
			'close' => $close
		);
		
		Session::flash('notifications', static::$notifications);
	}

}