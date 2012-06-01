<?php
	// Set PHP settings
	error_reporting(E_ALL|E_STRICT);

	/* */
	class ErrorIdentifier {
		static private $id_cache = array();
		static public function get($data) {
			$key = '';
			if(isset($data['error'])) {
				$key .= $data['error'];
				if(isset($data['file'])) $key .= '|' . $data['file'];
				if(isset($data['line'])) $key .= '|' . $data['line'];
			} else {
				$key = json_encode($data);
			}
			if(isset(self::$id_cache[$key])) {
				self::$id_cache[$key]++;
			} else {
				self::$id_cache[$key] = 1;
			}
			return sprintf('%s-%08d', sha1($key), self::$id_cache[$key]);
		}
	}

	/* Write to stderr */
	function writeln_stderr($msg) {
		file_put_contents("php://stderr", $msg . "\n");
	}

	/* Write to stderr */
	function output_error_object(array $data) {
		$ident = ErrorIdentifier::get($data);
		$json = json_encode($data);
		writeln_stderr($ident . ' = ' . $json);
		header("Content-Type: application/javascript");
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
	if(!file_exists(dirname(__FILE__) . '/config.php')) { throw new Exception('No configuration!'); }
	require_once(dirname(__FILE__) . '/config.php');

	if(!defined('MAX_MSG_LENGTH')) { define('MAX_MSG_LENGTH', 1024); }
	if(!defined('USER_COOKIE_NAME')) { define('USER_COOKIE_NAME', 'InInfologCookie'); }

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

	/* Setup cookies */
	class Cookie {
		static private $random_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-/%=';
		static private $user_ident_tag = null;

		/* */
		static public function isValid($tag) {
			if(strlen($tag) !== 128) return false;
			if(preg_match('/^[' .preg_quote(self::$random_chars) .']{128}$/', $tag) !== 1) return false;
			return true;
		}

		/* */
		static public function getUserIdentTag() {
			if(is_null(self::$user_ident_tag)) {
				if(isset($_COOKIE[USER_COOKIE_NAME]) && self::isValid($_COOKIE[USER_COOKIE_NAME]) ) {
					self::$user_ident_tag = $_COOKIE[USER_COOKIE_NAME];
				} else {
					self::$user_ident_tag = self::generateRandom(128);
					setcookie(USER_COOKIE_NAME,
						self::$user_ident_tag,
						time()+60*60*24*365*10,
						'/',
						$domain,
						(isset($_SERVER['HTTPS']) ? true : false)
					);
				}
			}
			return self::$user_ident_tag;
		}

		/* */
		static private function generateRandom($len) {
			$key = '';
			for($i = 0; $i<$len; $i++) {
				$key .= self::$random_chars[ rand(0, strlen(self::$random_chars) ) ];
			}
			return $key;
		}

	}

return;
?>
