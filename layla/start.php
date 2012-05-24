<?php

Autoloader::namespaces(array(
	'Components\\Layla' => __DIR__
));

// --------------------------------------------------------------
// Set Aliases
// --------------------------------------------------------------
Autoloader::alias('Components\\Layla\\Notification', 'Notification');
Autoloader::alias('Components\\Layla\\HTML', 'HTML');