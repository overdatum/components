<?php

// --------------------------------------------------------------
// Set constants
// --------------------------------------------------------------
define('MUSTACHE_EXT', '.mustache');


// --------------------------------------------------------------
// Register classes
// --------------------------------------------------------------
Autoloader::map(array(
	'Mustache' => __DIR__.DS.'mustache'.EXT,
	'LaylaMustache' => __DIR__.DS.'laylamustache'.EXT
));


// --------------------------------------------------------------
// Add event listeners
// --------------------------------------------------------------
Event::listen(View::loader, function($bundle, $view)
{
	return LaylaMustache::file($bundle, $view, Bundle::path($bundle).'views');
});

Event::listen(View::engine, function($view)
{
	// The Blade view engine should only handle the rendering of views which
	// end with the Blade extension. If the given view does not, we will
	// return false so the View can be rendered as normal.
	if ( ! str_contains($view->path, MUSTACHE_EXT))
	{
		return;
	}

	if(count($view->data) > 1)
	{
		$mustache = new LaylaMustache;
		return $mustache->render(File::get($view->path), $view->data());
	}

	return File::get($view->path);
});