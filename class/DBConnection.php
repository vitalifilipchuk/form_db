<?php
class DBConnection {
	private $connection;
	private static $instance;
	private $host = "localhost";
	private $db_username = "root";
	private $db_password = "";
	private $database = "formdb";
	//Get an instance of the Database
	public static function getInstance() {
		// If no instance then make one
		if (!self::$instance) { 
			self::$instance = new self();
		}
		return self::$instance;
	}
	// Constructor
	private function __construct() {
		$this->connection = new mysqli($this->host, $this->db_username, 
			$this->db_password, $this->database);
	
		// Error handling
		if(mysqli_connect_error()) {
			trigger_error("Failed to conencto to MySQL: " . mysql_connect_error(),
				 E_USER_ERROR);
		}
	}
	// Preventing duplication of connection via clone
	private function __clone() { }
	// Get connection
	public function getConnection() {
		return $this->connection;
	}
}
?>