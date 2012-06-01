<?php
	/* Setup cookie based users */
	class Cookie {
		static private $key_len = 64;
		static private $key_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		static private $user_id = null;
		static private $user_tag = null;

		/* */
		static public function isValid($tag) {
			if(is_null($tag)) return false;
			if(strlen($tag) !== self::$key_len) return false;
			if(preg_match('/^[' .preg_quote(self::$key_chars) .']+$/', $tag) !== 1) return false;
			return true;
		}

		/* */
		static public function exists($tag) {
			$sql = SQL::init();
			$res = $sql->query(sprintf('SELECT COUNT(*) AS count FROM `' . SQL_PREFIX . 'user` WHERE tag=\'%s\'', $sql->escape_string($tag) ));
			if($res === FALSE) throw new Exception('MySQL error: ' . $sql->error);
			$row = $res->fetch_array();
			if(is_null($row)) throw new Exception('Unespected missing row from MySQL!');
			if($row['count'] == 1) return true;
			return false;
		}

		/* */
		static public function getUser($tag) {
			$sql = SQL::init();
			$res = $sql->query(sprintf('SELECT * FROM `' . SQL_PREFIX . 'user` WHERE tag=\'%s\' LIMIT 1', $sql->escape_string($tag) ));
			if($res === FALSE) throw new Exception('MySQL error: ' . $sql->error);
			$row = $res->fetch_array();
			return $row;
		}

		/* */
		static public function insert($tag) {
			$sql = SQL::init();
			$res = $sql->query(sprintf('INSERT INTO `' . SQL_PREFIX . 'user` (tag) VALUES (\'%s\')', $sql->escape_string($tag) ));
			if($res === FALSE) throw new Exception('MySQL error: ' . $sql->error);
			return $sql->insert_id;
		}

		/* */
		static private function init() {
			if( is_null(self::$user_tag) || is_null(self::$user_id) ) {
				$cookie = isset($_COOKIE[USER_COOKIE_NAME]) ? $_COOKIE[USER_COOKIE_NAME] : null;
				if( self::isValid($cookie) && self::exists($cookie) ) {
					self::$user_tag = $cookie;
					$row = self::getUser($cookie);
					if(is_null($row)) throw new Exception('Unespected null row!');
					self::$user_id = $row['user_id'];
				} else {
					self::$user_tag = self::generateRandom(self::$key_len);
					setcookie(USER_COOKIE_NAME,
						self::$user_tag,
						time()+60*60*24*365*10,
						'/',
						CURRENT_DOMAIN,
						(isset($_SERVER['HTTPS']) ? true : false)
					);
					self::$user_id = self::insert(self::$user_tag);
				}
			}
		}

		/* */
		static public function getUserTag() {
			self::init();
			return self::$user_tag;
		}

		/* */
		static public function getUserId() {
			self::init();
			return self::$user_id;
		}

		/* */
		static private function generateRandom($len) {
			$key = '';
			for($i = 0; $i<$len; $i++) {
				$key .= self::$key_chars[ rand(0, strlen(self::$key_chars)-1 ) ];
			}
			return $key;
		}

	}

return;
?>
