<?php
require 'vendor/autoload.php';
require_once 'dbcon.php';
//require_once 'classes/DB.php';
/*require 'vendor/slim/slim/Slim/Slim-Extras/Views/Mustache.php';
\Slim\Extras\Views\Mustache::$mustacheDirectory = 'vendor/mustache/mustache/';*/
spl_autoload_register( function( $class ) {
		require_once 'classes/' . $class . '.php';
	});
$db = DB::getInstance( 'Config' );
$auth = new Auth( 'sha256' );
$app = new \Slim\Slim( array(
			'debug' => true,
			'mode' => 'production',
			'templates.path' => './templates'
		)
	);
// Define DB resource
$app->container->singleton('db', function () {
	return DB::getInstance( 'Config' );
} );
$app->add( $auth );
//$app = new \Slim\Slim();
$app->get('/', function () {
	echo "Hello";
});
$app->get('/users/:name', 'Routes:getUserInfo');

$app->get( '/login', 'Routes:login')->name("login");

$app->post( '/profile', 'Routes:postProfile')->name("profile");

$app->get( '/profile', 'Routes:getProfile')->name("profileget");

$app->get( '/signup', 'Routes:getSignup');

$app->post( '/signup', 'Routes:postSignup');

$app->get( '/logout', 'Routes:getLogout');

$app->post( '/createfirm', 'Routes:createFirm' );

$app->run();
