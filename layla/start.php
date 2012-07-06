<?php
Autoloader::namespaces(array(
	'Layla' => __DIR__
));

// --------------------------------------------------------------
// Set Aliases
// --------------------------------------------------------------
Autoloader::alias('Layla\\Notification', 'Notification');
Autoloader::alias('Layla\\HTML', 'HTML');
Autoloader::alias('Layla\\Artifact', 'Artifact');
Autoloader::alias('Layla\\Route', 'Route');