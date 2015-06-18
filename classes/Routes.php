<?php
use \Slim\Slim;
class Routes {
	/**
	 * Route to get login page
	 * @return
	 */
	public static function login() {
		$app = Slim::getInstance();
		$app->render( 'login.php' );
	}

	/**
	 * Routet to get a signup page
	 * @return
	 */
	public static function getSignup() {
		$app = Slim::getInstance();
		$app->render( 'signup.php' );
	}

	/**
	 * Route to post to a signup page for signing up
	 * @return
	 */
	public static function postSignup() {
		$app = Slim::getInstance();
		$request = $app->request;
		$allPostVars = $app->request->post();
		$status = User::verifyInput( $allPostVars );
		// if input passes, proceed
		if ( $status === true ) {
			$params = array(
				$allPostVars['username'],
				$allPostVars['password'],
				$allPostVars['name'],
				$allPostVars['number'],
				$allPostVars['email'],
				$allPostVars['year']
			);
			//echo User::addUser( $params );
			$status = User::addUser( $params );
			if ( $status == true ) {
				//$_SESSION['user'] = User::loadFromDb( $allPostVars['username'],
				//		$allPostVars['password']
				//	);
				//$ourRoute = $app->router->getNamedRoute( 'profile' );
				$_POST = array( array( 'name' => $allPostVars['username'],
					'password' => $allPostVars['password'] ) );
				/*$result = $ourRoute->dispatch();
				var_dump($result);
				$app->response->setStatus(200);
				$app->response->headers->set('Content-Type', 'text/html');
				$app->response->setBody( $result );*/
				$app->redirect( $app->urlFor( "profile" ) );
			}
		}
		//$_POST['err'] = $status;
		var_dump($app->response->getBody());
		$app->render( 'signup.php' );
	}

	/**
	 * Route to post to a profile page, to reach through login
	 * @return
	 */
	public static function postProfile() {
		$app = Slim::getInstance();
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
	}

	public static function getProfile() {
		$app = Slim::getInstance();
		$request = $app->request;
		$username = $request->post( 'name' );
		$pass = $request->post( 'password' );
		if ( isset( $_SESSION['user'] ) ) {
			$user = $_SESSION['user'];
			$app->render( 'profile.php', array( 'token' => $user->getToken() ) );
		}
	}

	/**
	 * Route to log out a user and return to login page
	 * @return
	 */
	public static function getLogout() {
		$app = Slim::getInstance();
		session_unset();
		$_SESSION['err'] = "Logged out";
		$app->redirect( $app->urlFor( "login" ) );
		//$app->response->headers->set('Content-Type', 'application/json');
		//$app->response->setBody( json_encode( array( "status" => "loggedout" ) ) );
	}

	/**
	 * Route to get user info, when a url as /users/name is accessed
	 * @param  string $name User name to query for
	 * @return
	 */
	public static function getUserInfo( $name ) {
		$app = Slim::getInstance();
		$request = $app->request;
		$db = $app->db;
		/*echo $request->getPath()."\n";
		echo $request->getMethod()."\n";
		echo $request->getContentType();*/
		$response = $app->response;
		$db->query("SELECT memberId,Name,Email,Mobile,Designation from `users` WHERE username=?",array($name));
		if ( $db->getResult() != false ) {
			$response->headers->set( 'Content-Type', 'application/json' );
			//header("Content-Type: application/json");
			//echo json_encode($db->getResult());
			$response->body( json_encode( $db->getResult() ) );
		} else {
			$response->headers->set( 'Content-Type: application/json' );
			$response->setBody( json_encode( array( 'error' => 'User does not exist' ) ) );
			//echo json_encode( array( 'error' => 'User does not exist' ) );
			//echo json_encode($app->router()->getCurrentRoute());
		}
	}

	/**
	 * Create a firm in database
	 * @return
	 */
	public static function createFirm() {

	}
}
