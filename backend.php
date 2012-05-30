<?php
try {

	require_once('config.php');

	/* Setup MySQL */
	class SQL {
		static private $mysqli = null;
		static public function init() {
			if(is_null($this->mysqli) {
				$this->mysqli = mysqli_connect(SQL_HOSTNAME, SQL_USERNAME, SQL_PASSWORD, SQL_DATABASE);
				if($this->mysqli->connect_error) {
					throw new Exception('SQL connection failed: ' . $this->mysqli->connect_error);
				}
			}
			return $this->mysqli;
		}
	}

	if(isset($_POST['send_msg'])) {
		$msg = $_POST['msg'];
		$sql = SQL::init();
		if( $sql->query('INSERT INTO `' . SQL_DATABASE . 'log` (msg) VALUES (' . $sql->escape_string($msg) . ')') === FALSE) {
			throw new Exception('SQL error: ' . $sql->error());
		}
	}

	throw new Exception('Unknown request');
} catch(Exception $e) {
	header("Status: 400 Bad Request");
	echo 'Exception: ' . $e->getMessage() . "\n"
	   . '@code: ' . $e->getCode() . "\n"
	   . '@file: ' . $e->getFile() . ':' . $e->getLine() . "\n"
	   . '@stack/['."\n"
	   . $e->getTrace(). "\n"
	   . ']/@stack'."\n";
}
return;
?>
