<?php
class sql {
	private static $con;
	public function sql($server = null, $user = null, $pass = null, $db = null) {
		try {
			self::$con = new PDO("mysql:host=".$server.";dbname=".$db.";charset=utf8", $user, $pass);
			// set the PDO error mode to exception
			self::$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return self::$con;
		} catch(PDOException $e) {
			echo "Connection failed: " . $e->getMessage();
		}
	}
	public static function get($sql) {
		if($sql !== "") {
			try {
				$q = self::$con->prepare($sql);
				$q->execute();
				return $q->fetchAll(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				echo "Fel vid inhämntning från databasen: " . $e->getMessage();
				return false;
			}
		} else {
			return false;
		}
	}
	public static function set($sql) {
		if($sql !== "") {
			try {
				$q = self::$con->prepare($sql);
				return $q->execute();
			} catch(PDOException $e) {
				echo "Fel vid skrivning till databasen: " . $e->getMessage();
				return false;
			}
		} else {
			return false;
		}
	}
	public static function lastId() {
		return self::$con->lastInsertId();
	}
}
?>