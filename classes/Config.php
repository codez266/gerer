<?php
class Config {
	protected static $host = 'localhost';
	protected static $dbuser = 'spons';
	protected static $db = 'spons';
	protected static $dbpass = 'spons';
	public static function getHost() {
		return self::$host;
	}
	public static function getUser() {
		return self::$dbuser;
	}
	public static function getDB() {
		return self::$db;
	}
	public static function getDBPass() {
		return self::$dbpass;
	}
}
