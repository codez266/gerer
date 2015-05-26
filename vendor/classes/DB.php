<?php
/**
 * Wrapper class for database
 */
class DB {
	private static $_dbInstance;
	private static $_pdo,
	$_count,
	$_error = false,
	$_query;
	;
	private function __construct( $dbhost, $dbname, $dbuser, $dbpass ) {
		try{
			$this->_pdo = new PDO(
				"mysql:host=$dbhost;dbname=$dbname;charset=utf8",
				$dbuser,
				$dbpass
			);
		} catch ( PDOExcpetion $e ) {
			die( $e->getMessage() );
		}
	}
	public static getInstance( $dbhost, $dbname, $dbuser, $dbpass ) {
		if ( !isset( slef::$_dbInstance ) ) {
			self::$_dbInstance = new DB( $dbhost, $dbname, $dbuser, $dbpass );
		}
		return self::$_dbInstance;
	}

	public function query( $query, $params = array() ) {
		$this->_error = false;
		if ( $this->_query = $this->_pdo->prepare( $query ) ) {
			if ( count( $params ) ) {
				if ( $this->_query->execute( $params ) ) {
					$this->_result = $this->_query->fetchAll( PDO::FETCH_ASSOC );
					$this->_count = $this->_query->rowCount();
				} else {
					$this->_error = true;
				}
			}
		}
	}
}
