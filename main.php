<?php
	// Set PHP settings
	error_reporting(E_ALL|E_STRICT);

	/* */
	class ErrorIdentifier {
		static private $id = 0;
		static public function get($data) {
			$key = '';
			if(isset($data['error'])) {
				$key .= $data['error'];
				if(isset($data['file'])) $key .= '|' . $data['file'];
				if(isset($data['line'])) $key .= '|' . $data['line'];
			} else {
				$key = json_encode($data);
			}
			self::$id++;
			return sha1($key) + '-' + self::$id;
		}
	}

	/* Write to stderr */
	function writeln_stderr($msg) {
		file_put_contents("php://stderr", $msg . "\n");
	}

	/* Write to stderr */
	function output_error_object(array $data) {
		$json = json_encode($data);
		writeln_stderr($json);
		header("Content-Type: application/javascript");
		header("Status: 400 Bad Request");
		echo json_encode(array('error' => 'Internal error with identifier ' . ErrorIdentifier::get($data))) . "\n";
		exit;
	}

	/* PHP error handler */
	function my_error_handler($errno, $errstr, $errfile, $errline) {
		output_error_object(array("error" => "$errno $errstr at $errfile:$errline"));
	}

	/* Exception handler */
	function my_exception_handler($e) {
		try {
			$data = array(
				'error' => ''.$e->getMessage(),
				'code' => $e->getCode(),
				'file' => ''.$e->getFile(),
				'line' => $e->getLine()
			);

			$stack = $e->getTrace();
			if(count($stack) !== 0) {
				$data['trace'] = array();
				foreach($stack as $tmp) { $data['trace'][] = $tmp; }
			}

			output_error_object($data);
		} catch(Exception $e2) {
			output_error_object(array('error'=>"Got another exception '" . $e2 . "' while handling exception '" . $e . "'"));
		}
		exit;
	}

	// Set handlers
	set_error_handler('my_error_handler');
	set_exception_handler('my_exception_handler');

	// Read config
	if(!file_exists(dirname(__FILE__) . '/config.php')) { throw new Exception('No configuration!'); }
	require_once(dirname(__FILE__) . '/config.php');

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
