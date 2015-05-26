<?php
require 'vendor/autoload.php';
$app = new \Slim\Slim(
		'debug' => true
	);
$app->get('/', function () {
	echo "Hello";
});
$app->get('/:name', function ($name) {
	echo "Hello".$name;
});
$app->run();
