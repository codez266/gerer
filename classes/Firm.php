<?php
use \Slim\Slim;
class Firm {
	public static $fields = array( 'name', 'designation', 'amount', 'finalized', 'description', 'addedBy' );
	public static function verifyInput( $data ) {
		$error = "";
		if ( !isset( $data['name'] ) {
			$error = "Firm name cannot be empty";
		} elseif( strlen( $data['name'] ) > 100 ) {
			$error = "Firm name too long";
		} else {
			return true;
		}
		return $error;
	}
	public static function addFirm( $data ) {
		$app = Slim::getInstance();
		$db = $app->db;
		$name = $data['name'];
		$query = "SELECT * from `firm` WHERE name=?";
		$db->query( $query , array( $name ) );
		if ( $db->getCount() == 0 ) {
			$data = self::createValues( $data );
			$db->insert( "firm", $data[0], $data[1] );
			return "Records added";
		} else {
			// do not add 'addedBy' on updating records
			unset( $data['addedBy'] );
			return self::updateFirm( $data );
		}
	}
	public static function updateFirm( $data ) {
		$app = Slim::getInstance();
		$db = $app->db;
		$name = $data['name'];
		$data = self::createValues( $data );
		$query = "UPDATE `firm` SET ";
		foreach ( $data[0] as $key ) {
			$query .= "$key=?, ";
		}
		$data[1][] = $name;
		$query .= "WHERE name=?";
		$db->query( $query , $data[1] );
		if ( $db->getError() !== false ) {
			return "updated records";
		} else {
			return "error updating";
		}
	}
	public static function createValues( $data ) {
		$keys = array();
		$values = array();
		foreach ( $fields as $field ) {
			if ( isset( $data[$field] ) ) {
				$keys[] = $field;
				$values[] = $data[$field];
			}
		}
		return array( $keys, $values );
	}
}
