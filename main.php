<?php
	function __autoload($class_name) {
		require_once( dirname(__FILE__) . '/classes/' . $class_name . '.php');
	}

	// Set PHP settings
	error_reporting(E_ALL|E_STRICT);

	/* Write to stderr */
	function writeln_stderr($msg) {
		file_put_contents("php://stderr", $msg . "\n");
	}

	/* Write to stderr */
	function output_error_object(array $data) {
		$ident = ErrorIdentifier::get($data);
		$json = json_encode($data);
		writeln_stderr($ident . ' = ' . $json);
		header("Content-Type: application/json");
		header("Status: 400 Bad Request");
		echo json_encode(array('error' => 'Internal error with identifier ' . $ident)) . "\n";
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
	require_once(dirname(__FILE__) . '/main-config.php');

return;
?>
