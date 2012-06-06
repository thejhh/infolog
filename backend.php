<?php
try {
	require_once(dirname(__FILE__) . '/main.php');

	/* Get server configurations */
	if(isset($_GET['config'])) {
		$server_time = time();
		// Please note: Export only public information!
		header("Content-Type: application/json");
		echo json_encode(array(
			'time'           => $server_time,
			'MAX_MSG_LENGTH' => MAX_MSG_LENGTH,
			'TOP_DOMAIN' => TOP_DOMAIN,
			'USER_COOKIE_NAME' => USER_COOKIE_NAME,
			'COOKIE_DOMAIN' => COOKIE_DOMAIN,
			'CURRENT_DOMAIN' => CURRENT_DOMAIN,
			'CURRENT_DOMAIN_TAG' => CURRENT_DOMAIN_TAG,
			'GLOBAL_AUTH_COOKIE_NAME' => 'InfoLogAuthKey',
			'require_global_auth_cookie' => (defined('GLOBAL_AUTH_KEY') ? true : false)
		));
		return;
	}

	/* Check optional authentication */
	if(defined('GLOBAL_AUTH_KEY')) {
		$pw = isset($_COOKIES['InfoLogAuthKey']) ? (string)$_COOKIES['InfoLogAuthKey'] : '';
		if(crypt($pw, GLOBAL_AUTH_KEY) !== GLOBAL_AUTH_KEY) {
			setcookie($_COOKIES['InfoLogAuthKey'], "", time() - 3600);
			throw new Exception("Authentication failed.");
		}
	}

	/* Test server environment */
	if(isset($_POST['test']) || isset($_GET['test'])) {
		if(!extension_loaded('intl')) {
			throw new Exception('PHP extension is not loaded: intl');
		}
		return;
	}

	/* remove_msg */
	if(isset($_POST['remove_msg'])) {
		$log_id = $_POST['remove_msg'];
		$user_id = Cookie::getUserId();
		$sql = SQL::init();
		$res = $sql->query(sprintf('DELETE FROM `' . SQL_PREFIX . 'log` WHERE (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(created) <= 5*60 ) AND log_id=\'%s\' AND user_id=\'%s\' LIMIT 1', $log_id, $user_id));
		if($res === FALSE) { throw new Exception('SQL error: ' . $sql->error); }
		if($sql->affected_rows !== 1) { throw new Exception(sprintf('Could not find specified message with log_id=%s and user_id=%s', $log_id, $user_id)); }
		echo 'OK';
		return;
	}

	/* send_msg */
	if(isset($_POST['send_msg']) && isset($_POST['msg'])) {
		if(!extension_loaded('intl')) {
			throw new Exception('PHP extension is not loaded: intl');
		}

		$user_id = Cookie::getUserId();

		$msg = Normalizer::normalize($_POST['msg']);
		$msg_len = strlen($msg);
		if($msg_len >= MAX_MSG_LENGTH) {
			throw new Exception(sprintf('Message is too long (%d bytes).', $msg_len));
		}
		$sql = SQL::init();
		if( $sql->query('INSERT INTO `' . SQL_PREFIX . 'log` (created,updated,domain,remote_addr,user_id,msg)'
				.' VALUES (NOW(), NOW()'
				.', \'' . $sql->escape_string($_SERVER['SERVER_NAME']) . '\''
				.', \'' . $sql->escape_string($_SERVER['REMOTE_ADDR']) . '\''
				.', \'' . $sql->escape_string($user_id) . '\''
				.', \'' . $sql->escape_string($msg) . '\''
				.')') === FALSE) {
			throw new Exception('SQL error: ' . $sql->error);
		}
		echo 'OK';
		return;
	}

	/* get_msgs */
	if(isset($_POST['msgs']) || isset($_GET['msgs']) ) {

		if(!extension_loaded('intl')) {
			throw new Exception('PHP extension is not loaded: intl');
		}

		/*
		if( phpversion() < '5.3' ) {
			throw new Exception('Invalid PHP version: ' . phpversion());
		}
		*/

		$start_from = 0;
		$q = '';
		if(isset($_GET['start'])) $start_from = (int) ($_GET['start']);
		if(isset($_POST['start'])) $start_from = (int) ($_POST['start']);
		if(isset($_GET['q'])) $q = $_GET['q'];
		if(isset($_POST['q'])) $q = $_POST['q'];
		$sql = SQL::init();
		$query = 'SELECT * FROM `' . SQL_PREFIX . 'log`';
		$query .= ' WHERE log_id >= ' . $start_from;
		$query .= ' AND domain=\'' . $sql->escape_string($_SERVER['SERVER_NAME']) . '\'';
		if($q !== '') {
			$query .= " AND (LOCATE('". $sql->escape_string($q) ."', msg) != 0)";
		}
		$query .= ' ORDER BY updated, created';
		$server_time = time();
		$user_id = Cookie::getUserId();
		//writeln_stderr("server_time = " . $server_time);
		if( $result = $sql->query($query) ) {
			$list = array();
			while ($row = $result->fetch_object()){
				//writeln_stderr("event_id = " . $row->log_id);
				//writeln_stderr("event_updated_str = " . $row->updated);
				//writeln_stderr("event_updated = " . strtotime($row->updated));
				$data = array(
					'log_id' => $row->log_id,
					'updated' => strtotime($row->updated),
					'created' => strtotime($row->created),
					'domain' => Normalizer::normalize($row->domain),
					'msg' => Normalizer::normalize($row->msg)
				);
				if(defined('REMOTE_ADDR_SALT')) {
					$data['remote_addr'] = sha1(REMOTE_ADDR_SALT . $row->remote_addr);
				}
				if(defined('USER_ID_SALT')) {
					$data['user_id'] = sha1(USER_ID_SALT . $row->user_id);
				}
				$list[] = $data;
			}
			echo json_encode(array('time'=>$server_time, 'user_id'=>sha1(USER_ID_SALT . $user_id), 'events'=>$list));
		} else {
			throw new Exception('SQL error: ' . $sql->error);
		}
		return;
	}

	throw new Exception('Unknown request - _GET has ' . implode(', ', array_keys($_GET)) . ', _POST has ' . implode(', ', array_keys($_POST)) );
} catch(Exception $e) {
	my_exception_handler($e);
}
return;
?>
