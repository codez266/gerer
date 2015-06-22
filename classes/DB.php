<?php
/**
 * Wrapper class for database
 */
class DB {
	private static $_dbInstance;
	private $_pdo,
	$_count,
	$_error = false,
	$_errorInfo,
	$_query,
	$_result;
	private function __construct( $dbhost, $dbname, $dbuser, $dbpass ) {
		try{
			$this->_pdo = new PDO(
				"mysql:host=$dbhost;dbname=$dbname;charset=utf8",
				$dbuser,
				$dbpass
			);
			//$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch ( PDOExcpetion $e ) {
			die( $e->getMessage() );
		}
	}
	public static function getInstance( $config ) {
		$dbhost = $config::getHost();
		$dbname = $config::getUser();
		$dbuser = $config::getDB();
		$dbpass = $config::getDBPass();
		if ( !isset( self::$_dbInstance ) ) {
			self::$_dbInstance = new DB( $dbhost, $dbname, $dbuser, $dbpass );
		}
		return self::$_dbInstance;
	}

	public function query( $query, $params = array() ) {
		$this->_error = false;
		if ( $this->_query = $this->_pdo->prepare( $query ) ) {
			//if ( count( $params ) ) {
				if ( $this->_query->execute( $params ) ) {
					$this->_result = $this->_query->fetchAll( PDO::FETCH_ASSOC );
					$this->_count = $this->_query->rowCount();
				} else {
					$this->_error = true;
					$this->_count = 0;
					$this->_result = null;
					$this->_errorInfo = $this->_pdo->errorInfo();
				}
			//} else {
				$this->_errorInfo = "Parameters empty";
			//}
		}
	}

	public function insert( $table, $keys, $data ) {
		$keys = array_filter( $keys );
		$data = array_filter( $data );
		$query = "INSERT INTO $table (";
		$values = " VALUES (";
		$first = true;
		foreach ( $keys as $field ) {
			if ( !$first ) {
				$query .= ",";
				$values .= ",";
			}
			$query = $query . $field;
			$values .= "?";
			$first = false;
		}
		$query .= ")";
		$values .= ")";
		$query .= $values;
		$this->query( $query , $data );
	}

	public function update( $table, $keys, $values, $where ) {
		$keys = array_filter( $keys );
		$data = array_filter( $data );
		$query = "UPDATE $table SET ";
		$i = 0;
		foreach ( $keys as $key ) {
			$value = $values[$i];
			$query .= "$key=$value, ";
			$i++;
		}
		$query = "WHERE $where=";
	}
	public function getResult() {
		return $this->_result;
	}
	public function getCount() {
		return $this->_count;
	}
	public function getError() {
		return $this->_error;
	}
	public function getErrorInfo() {
		return $this->_errorInfo;
	}
	public function getQuery() {
		return $this->_query;
	}
}
