<?php

Autoloader::map(array(
	'Httpful\\Http'      => __DIR__.DS.'http.php',
	'Httpful\\Mime'      => __DIR__.DS.'mime.php',
	'Httpful\\Request'   => __DIR__.DS.'request.php',
	'Httpful\\Response'  => __DIR__.DS.'response.php',
));