<?php
use \Slim\Slim;
class Routes {
	/**
	 * Route to get login page
	 * @return
	 */
	public static function getLogin() {
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
		$post = $app->request->post();
		$user = new User(
				$post['username'],
				$post['password'],
				$post['name'],
				$post['number'],
				$post['email'],
				$post['year'],
				1
			);
		$status = $user->verifyInput( $post );
		// if input passes, proceed
		if ( $status === true ) {
			$params = array(
				$post['username'],
				$post['password'],
				$post['name'],
				$post['number'],
				$post['email'],
				$post['year']
			);
			$status = $user->addUser( $params );
			$_SESSION['err'] = $status;
			if ( $status === true ) {
				$_SESSION['user'] = $user->loadFromDb( $post['username'], $post['password'] );
				$app->redirect( $app->urlFor( "profile" ) );
			}
		}
		$_SESSION['err'] = $status;
		$app->render( 'ignup.php' );
	}

	/**
	 * Route to post to a profile page, to reach through login
	 * @return
	 */
	public static function postProfile() {
		$app = Slim::getInstance();
		$request = $app->request;
		$username = $request->post( 'name' );
		$username = strip_tags( trim( $username ) );
		$pass = $request->post( 'password' );
		$pass = strip_tags( trim( $pass ) );
		$user = new User( $username, $pass );
		if ( ( $user = $user->loadFromDb( $username, $pass ) ) !== false ) {
			//var_dump($user->getUserName());
			unset($user['password'],$user['memberId']);
			$_SESSION['user'] = $user;
			//$user->setToken( md5(uniqid(mt_rand(), true)) );
			$app->render( 'index.html' );
		} else {
			$_SESSION['err'] = "Invalid username or password";
			$_SESSION['username'] = $username;
			//$app->redirect( $app->urlFor( "login" ) );
		}
	}

	public static function getProfile() {
		$app = Slim::getInstance();
		if ( isset( $_SESSION['user'] ) ) {
			$app->render( 'index.html' );
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
	 * @param  string $[name] Name of firm
	 * @return
	 */
	public static function createFirm( $name ) {
		$app = Slim::getInstance();
		if ( isset( $_SESSION['user'] ) ) {
			if ( $_SESSION['user']->getLevel() == 1 ) {
				$response = $app->response;
				JsonResponse::encode( $response, array( 'status' => 'not allowed' ) );
			} else {
				// only level 2 access can create
				$data = array();
				$data['request'] = $app->request;
				$data['name'] = $name;
				$data['designation'] = $request->post( 'designation' );
				$data['amount'] = $request->post( 'amount' );
				$data['finalized'] = $request->post( 'finalized' );
				$data['description'] = $request->post( 'description' );
				$data['addedBy'] = $_SESSION['user']['username'];
				$status = Firm::verifyInput( $data );
				if ( $status === true ) {
					$result = Firm::addFirm( $data );
					if ( $result === true ) {
						JsonResponse::encode( $response, array( 'status' => 'success' ) );
					} else {
						JsonResponse::encode( $response, array( 'status' => $result ) );
					}
				} else {
					JsonResponse::encode( $response, array( 'status' => $status ) );
				}
			}
		}
	}

	/**
	 * Get a firm from database
	 * @param  string $[name] Name of firm
	 * @return
	 */
	public static function getFirm( $name ) {
		$app = Slim::getInstance();
		$db = $app->db;
		$response = $app->response;
		if ( isset( $_SESSION['user'] ) ) {
			// only allowed one's given access
			$query = "SELECT name,designation,amount,finalized,description,addedBy from `firm` WHERE firm.id=(SELECT firmId from `firmAccess` WHERE firmId=(SELECT id from `firm` WHERE name=?) AND memberId=(SELECT memberId from `users` WHERE username=?) )";
			$username = $_SESSION['user'];
			$db->query( $query, array( $name, $_SESSION['user']['username'] ) );
			if( $db->getCount() > 0 ) {
				$result = $db->getResult()[0];
				JsonResponse::encode( $response, $result );
			}
		}
	}

	/**
	 * Returns list all firms accessible to the user
	 * @return
	 */
	public static function getAllFirms() {
		$app = Slim::getInstance();
		$db = $app->db;
		$response = $app->response;
		if ( isset( $_SESSION['user'] ) ) {
			if ( $_SESSION['user']['level'] == 1 ) {
				$username = $_SESSION['user']['username'];
				$query = "SELECT id,name,designation,amount,finalized,description,addedBy FROM `firm` WHERE id IN (SELECT firmId from `firmAccess` WHERE memberId=(SELECT memberId from `users` WHERE username=?))";
				$db->query( $query, array( $username ) );
				if( $db->getCount() > 0 ) {
					$result = $db->getResult();
					JsonResponse::encode( $response, $result );
				}
			} else {
				$query = "SELECT id,name,designation,amount,finalized,description,addedBy FROM `firm` WHERE 1";
				$db->query( $query, array() );
				if( $db->getCount() > 0 ) {
					$result = $db->getResult();
					JsonResponse::encode( $response, $result );
				}
			}
		}
	}

	/**
	 * Initialize with user
	 * @return
	 */
	public static function getInit() {
		$app = Slim::getInstance();
		$response = $app->response;
		$req = $app->request;
		$fullPath = $req->getPath();
		$virtualPath = $req->getPathInfo();
		$basePath = substr( $fullPath,0, -strlen( $virtualPath ) );
		if ( isset( $_SESSION['user'] ) ) {
			$user = $_SESSION['user'];
			$res = array(
					'user' => $user,
					'server' => $basePath + '/'
				);
			JsonResponse::encode( $response, $res );
		}
	}

	/**
	 * Add firm visit
	 * @return
	 */
	public static function addFirmVisit( $firmId, $contactId ) {
		$app = Slim::getInstance();
		$db = $app->db;
		$request = $app->request;
		$response = $app->response;
		$post = $response->post();
		$res = '';
		$status = '';
		if ( isset( $_SESSION['user'] ) ) {
			$user = $_SESSION['user'];
			if ( !isset( $post['memberId'], $post['contactId'] ) ) {
				$status = "Incomplete data";
			} else {
				$firmId = htmlspecialchars( $id, ENT_QUOTES );
				$memberId = $user['memberId'];
				$contactId = htmlspecialchars( $post['contactId'], ENT_QUOTES );
				$query = "SELECT * from `firmVisit` WHERE firmId=? AND memberId=? AND contactId=?";
				$db->query( $query, array( $firmId, $memberId, $contactId ) );
				// we're dealing with an update of record
				if ( $db->getCount() > 0 ) {
					$vtime = $post( 'visitTime' );
					$comment = $post( 'comment' );
					$done = $post( 'done' );
					$comment = htmlentities ( trim ( $comment , ENT_NOQUOTES ) );
					$result = $db->getResult()[0];
					$query = "UPDATE `firmVisit` SET visitTime=?, comment=?, done=?";
					$db->query( $query, array( $vtime, $comment, $done ) );
					if ( $db->getError() === false ) {
						$status = 'success';
					} else {
						$status = 'error';
					}
				}
				// we're dealing with a new record
				else {
					// only allowed if level 2 user
					if ( $user['level'] == 2 ) {
						$db->insert( 'firmVisit', array( 'firmId', 'contactId', 'memberId' ),
						array( $firmId, $memberId, $contactId ) );
						$status = 'success';
					} else {
						$status = "not enough permissions";
					}
				}
			}
		}
		JsonResponse::encode( $response, array( 'status' => $status ) );
	}

	/**
	 * Get info of all visits to a firm
	 * @return [type] [description]
	 */
	public static function getFirmVisits( $firmId ) {
		$result = '';
		$firmId = strip_tags( $firmId );
		$app = Slim::getInstance();
		$db = $app->db;
		$response = $app->response;
		if ( isset( $_SESSION['user'] ) ) {
			$level = $_SESSION['user']['level'];
			$status = '';
			if ( $level == 2 ) {
				$query = "SELECT firmId,contactId,memberId,visitTime,comment,done,time from `firmVisit` WHERE firmId=?";
				$db->query( $query, array( $firmId ) );
				if( $db->getError() === false ) {
					$result = $db->getResult();
				} else {
					$status = $db->getErrorInfo();
					$result = array( 'status' => $status );
				}
			} else {
				$status = 'not enough permissions';
				$result = array( 'status' => $status );
			}
		}
		JsonResponse::encode( $response, $result );
	}

	/**
	 * Get info of all users
	 * @return [type] [description]
	 */
	public static function getAllUsers() {
		$result = '';
		$app = Slim::getInstance();
		$db = $app->db;
		$response = $app->response;
		if ( isset( $_SESSION['user'] ) ) {
			$level = $_SESSION['user']['level'];
			$status = '';
			if ( $level == 2 ) {
				$query = "SELECT memberId,username,Name FROM `users` WHERE 1";
				$db->query( $query, array() );
				if( $db->getError() === false ) {
					$result = $db->getResult();
				}
			} else {
				$status = 'not enough permissions';
				$result = array( 'status' => $status );
			}
		}
		JsonResponse::encode( $response, $result );
	}
}
