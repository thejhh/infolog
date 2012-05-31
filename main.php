<?php
	error_reporting(E_ALL|E_STRICT);

	function myErrorHandler($errno, $errstr, $errfile, $errline) {
		header("Content-Type: application/javascript");
		header("Status: 400 Bad Request");
		echo json_encode(array("message" => "$errno $errstr at $errfile:$errline"));
		exit;
	}
	set_error_handler("myErrorHandler");
	function my_exception_handler($e) {
		try {
			$data = array(
				'message' => ''.$e->getMessage(),
				'code' => $e->getCode(),
				'file' => ''.$e->getFile(),
				'line' => $e->getLine()
			);

			$stack = $e->getTrace();
			if(count($stack) !== 0) {
				$data['trace'] = array();
				foreach($stack as $tmp) { $data['trace'][] = $tmp; }
			}

			header("Content-Type: application/javascript");
			header("Status: 400 Bad Request");
			echo json_encode($data) . "\n";
		} catch(Exception $e2) {
			header("Status: 400 Bad Request");
			echo '{message="Got another exception `' . $e2 . '` while handling exception `' . $e . '`"}';
		}
		exit;
	}
	set_exception_handler('my_exception_handler');

	if(!file_exists(dirname(__FILE__) . '/config.php')) { throw new Exception('No configuration!'); }
	require_once(dirname(__FILE__) . '/config.php');

	/* Setup MySQL */
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
