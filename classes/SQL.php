<?php
/* Setup JIT MySQL */
class SQL {
	static private $mysqli = null;
	static public function init() {
		if(is_null(self::$mysqli)) {
			self::$mysqli = mysqli_connect(SQL_HOSTNAME, SQL_USERNAME, SQL_PASSWORD, SQL_DATABASE);
			if(self::$mysqli->connect_error) {
				throw new Exception('SQL connection failed: ' . $this->mysqli->connect_error);
			}
		}
		return self::$mysqli;
	}
}
return;
?>
