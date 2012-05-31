<?php
try {
	require_once(dirname(__FILE__) . '/main.php');

	/* send_msg */
	if(isset($_POST['send_msg']) && isset($_POST['msg'])) {
		$msg = $_POST['msg'];
		$sql = SQL::init();
		if( $sql->query('INSERT INTO `' . SQL_PREFIX . 'log` (created,updated,domain,remote_addr,msg)'
				.' VALUES (NOW(), NOW()'
				.', \'' . $sql->escape_string($_SERVER['SERVER_NAME']) . '\''
				.', \'' . $sql->escape_string($_SERVER['REMOTE_ADDR']) . '\''
				.', \'' . $sql->escape_string($msg) . '\''
				.')') === FALSE) {
			throw new Exception('SQL error: ' . $sql->error);
		}
		echo 'OK';
		return;
	}

	/* get_msgs */
	if(isset($_POST['msgs']) || isset($_GET['msgs']) ) {
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
				$list[] = $row;
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
