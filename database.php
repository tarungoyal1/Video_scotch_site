<?php
require_once ('config.php');

class MySQLDatabase {
	private $connection;

	function __construct() {
		$this->open_connection();
	}

	public function open_connection() {
		$this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS);
			if (!$this->connection) {
				die("Database connection Failed");
			} else {
				$db_select = mysqli_select_db($this->connection, DB_NAME);
				if (!$db_select) {
					die("Database selection Failed");
				}
			}
	}

	public function close_connection() {
		if (isset($this->connection)) {
			mysqli_close($this->connection);
			unset($this->connection);
		}
	}

	//common database functions 

	public function perform_query($query) {
		$result = mysqli_query($this->connection, $query);
		$this->confirm_query($result);
		return $result;
	}

	public function confirm_query($result) {
		if (!$result) {
			echo "Database Query Failed";
			// die("Database Query Failed");
		}
	}

	public function string_prep($string) {
		$escaped_string = mysqli_real_escape_string($this->connection, $string);
		return $escaped_string;
	}

	public function fetch_array($result) {
		$array = mysqli_fetch_assoc($result);
		return $array;
	}

	public function count_rows($array) {
		$count = mysqli_num_rows($array);
		return $count;
	}

	public function get_affected_rows(){
		return $this->connection->affected_rows;
	}
	
	public function fetchAll($result){
		$array = mysqli_fetch_all($result,MYSQLI_ASSOC);
		return $array;
	}

}

$db = new MySQLDatabase();
?>