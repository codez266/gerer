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
$app->add( $auth );
//$app = new \Slim\Slim();
$app->get('/', function () {
	echo "Hello";
});
$app->get('/users/:name', function ($name) use($app) {
	var_dump($_SESSION);
	global $db;
	//echo "Hello".$name;
	$request = $app->request;
	/*echo $request->getPath()."\n";
	echo $request->getMethod()."\n";
	echo $request->getContentType();*/
	$db->query("SELECT memberId,Name,Email,Mobile,Designation from `users` WHERE username=?",array($name));
	if ( $db->getResult() != false ) {
		header("Content-Type: application/json");
		echo json_encode($db->getResult());
	} else {
		echo json_encode( array( 'error' => 'User does not exist' ) );
		echo json_encode($app->router()->getCurrentRoute());
	}
});
$app->get( '/login', function () use($app) {
	//session_start();
	$app->render( 'login.php' );
})->name("login");
$app->post( '/profile', function () use($app) {
	//session_start();
	$request = $app->request;
	$username = $request->post( 'name' );
	$pass = $request->post( 'password' );
	if ( ( $user = User::loadFromDb( $username, $pass ) ) != false ) {
		$_SESSION['user'] = $user;
		$user->setToken( md5(uniqid(mt_rand(), true)) );
		$app->render( 'profile.php', array( 'token' => $user->getToken() ) );
	} else {
		$_SESSION['err'] = "Invalid username or password";
		$_SESSION['username'] = $username;
		$app->redirect( $app->urlFor( "login" ) );
	}
})->name("profile");

$app->get( '/signup', function () use($app) {
	//$app->render('login.mustache',array());
	$app->render( 'signup.php' );
});
$app->post( '/signup', function () use($app) {
	//session_start();
	$request = $app->request;
	$allPostVars = $app->request->post();
	$status = User::verifyInput( $allPostVars );
	// if input passes, proceed
	if ( $status === true ) {
		$params = array( $allPostVars['username'], $allPostVars['password'], $allPostVars['name'], $allPostVars['number'], $allPostVars['email'], $allPostVars['year'] );
		//echo User::addUser( $params );
		$status = User::addUser( $params );
		if ( $status == true ) {
			$app->redirect( $app->urlFor( "profile" ) );
		}
	}
	$_POST['err'] = $status;
	$app->render( 'signup.php' );
});
$app->get( '/logout', function () use($app) {
	//$app->render('login.mustache',array());
	if ( $_SESSION['user'] ) {
		$_SESSION['user'] = null;
	}
});
$app->run();
