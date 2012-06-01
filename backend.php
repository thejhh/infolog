<?php
try {
	require_once(dirname(__FILE__) . '/main.php');

	/* Test server environment */
	if(isset($_POST['test']) || isset($_GET['test'])) {
		if(!extension_loaded('intl')) {
			throw new Exception('PHP extension is not loaded: intl');
		}
		return;
	}

	/* send_msg */
	if(isset($_POST['send_msg']) && isset($_POST['msg'])) {
		if(!extension_loaded('intl')) {
			throw new Exception('PHP extension is not loaded: intl');
		}

		$user_tag = Cookie::getUserTag();

		$msg = Normalizer::normalize($_POST['msg']);
		$msg_len = strlen($msg);
		if($msg_len >= MAX_MSG_LENGTH) {
			throw new Exception(sprintf('Message is too long (%d bytes).', $msg_len));
		}
		$sql = SQL::init();
		if( $sql->query('INSERT INTO `' . SQL_PREFIX . 'log` (created,updated,domain,remote_addr,user_tag,msg)'
				.' VALUES (NOW(), NOW()'
				.', \'' . $sql->escape_string($_SERVER['SERVER_NAME']) . '\''
				.', \'' . $sql->escape_string($_SERVER['REMOTE_ADDR']) . '\''
				.', \'' . $sql->escape_string($user_tag) . '\''
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
		if( $result = $sql->query($query) ) {
			$list = array();
			while ($row = $result->fetch_object()){
				$data = array(
					'log_id' => $row->log_id,
					'updated' => $row->updated,
					'created' => $row->created,
					'domain' => Normalizer::normalize($row->domain),
					'msg' => Normalizer::normalize($row->msg)
				);
				if(defined('REMOTE_ADDR_SALT')) {
					$data['remote_addr'] = sha1(REMOTE_ADDR_SALT . $row->remote_addr);
				}
				if(defined('USER_TAG_SALT')) {
					$data['user_tag'] = sha1(USER_TAG_SALT . $row->user_tag);
				}
				$list[] = $data;
			}
			echo json_encode($list);
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
