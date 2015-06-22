<?php
class User {
	/**
	 * Array of fields of user table, first one is unique
	 * @var array
	 */
	private static $_fields = array( 'username', 'password', 'Name', 'Mobile', 'Email', 'Designation' );
	private $_username;
	private $_name;
	private $_email;
	private $_number;
	private $_designation;
	private $_level;
	private $_token;
	private $_password;
	public function construct( $username, $password, $name='', $email='', $number='', $designation='', $level = 1 ) {
		$this->_username = $username;
		$this->_name = $name;
		$this->_email = $email;
		$this->_number = $number;
		$this->_designation = $designation;
		$this->_level = $level;
		$this->_password = $password;
	}
	/**
	 * Adds user with data to database
	 * @param array $data array of values to insert into user table in the same order
	 */
	public function addUser( $data ) {
		$user = self::$_fields[0];
		$query = "SELECT * from `users` where username=?";
		$db = DB::getInstance( 'Config' );
		$name = $data[0];
		$db->query( $query , array( $name ) );
		if ( $db->getCount() > 0 ) {
			return "User already exists!";
		}
		$hash = password_hash($data[1],PASSWORD_DEFAULT);
		$data[1] = $hash;
		$db->insert( "users", self::$_fields , $data );
		if ( $db->getError() != false ) {
			return $db->getErrorInfo();
		}
		return true;
	}
	public function loadFromDb( $username, $password ) {
		$query = "SELECT * from `users` where username=?";
		$db = DB::getInstance( 'Config' );
		$db->query( $query , array( $username ) );
		if ( $db->getError() === true || $db->getCount() == 0 ) {
			return false;
		} else {
			$result = $db->getResult()[0];
			if ( password_verify( $password, $result['password'] ) ) {
				//return new User( $result['username'], $result['Name'], $result['Mobile'], $result['Email'], $result['Designation'], $result['level'] );
				return $result;
			} else {
				return false;
			}
		}
	}
	public function verifyInput( $data ) {
		$error = "";
		if( !isset( $data['username'], $data['name'],$data['password'],$data['email'],$data['number'],$data['year']) ) {
			$error = "One of the fields is missing";
			$_SESSION['err'] = $error;
			//header("Location:register.php");
		} else if( strlen( $data['name'] ) > 50 || strlen( $data['password'] ) > 50 ) {
			$error = "name or password is too long(max 50)";
			$_SESSION['err'] = $error;
			//errorRedirect($error,"register.php");
			//preg_match( '/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/', $data[ 'email' ] )
		}else if ( !filter_var($data['email'],FILTER_VALIDATE_EMAIL)) {
			$error = "Invalid email";
			$_SESSION['err'] = $error;
			//errorRedirect($error,"register.php");
		} else if(!filter_var($data['year'],FILTER_VALIDATE_INT)) {
			$error = "Age must be numeric";
			$_SESSION['err'] = $error;
		}
		if ( !empty( $error ) ) {
			return $error;
		} else {
			return true;
		}
	}
	public function setToken( $token ) {
		$this->_token = $token;
	}
	public function getToken() {
		return $this->_token;
	}
	public function getLevel() {
		return $this->_level;
	}
	public function getUserName() {
		return $this->_username;
	}
	public function getEmail() {
		return $this->_email;
	}
	public function getNumber() {
		return $this->_number;
	}
	public function getDesignation() {
		return $this->_designation;
	}
}
