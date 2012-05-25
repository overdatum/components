<?php
Autoloader::map(array(
    'Laravel\\Asset_Container' => path('sys').'asset.php'
));

Autoloader::namespaces(array(
	'AssetCompressor' => __DIR__
));

Autoloader::alias('AssetCompressor\\Asset', 'Asset');