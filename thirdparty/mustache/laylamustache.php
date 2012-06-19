<?php

class LaylaMustache extends Mustache {

	/**
	 * Get the path to a view using the default folder convention.
	 *
	 * @param  string  $bundle
	 * @param  string  $view
	 * @param  string  $directory
	 * @return string
	 */
	public static function file($bundle, $view, $directory)
	{
		$directory = str_finish($directory, DS);

		// Views may have either the default PHP file extension of the "Blade"
		// extension, so we will need to check for both in the view path
		// and return the first one we find for the given view.
		if (file_exists($path = $directory.$view.EXT))
		{
			return $path;
		}
		elseif (file_exists($path = $directory.$view.BLADE_EXT))
		{
			return $path;
		}
		elseif (file_exists($path = $directory.$view.MUSTACHE_EXT))
		{
			return $path;
		}
	}

}