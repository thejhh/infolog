<?php
try {
	require_once(dirname(__FILE__) . '/main.php');
	header("Content-Type: application/rss+xml; charset=UTF-8");

	$domain = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'default.infolog.in';

	$rssfeed = '<?xml version="1.0" encoding="UTF-8"?>';
	$rssfeed .= '<rss version="2.0">';
	$rssfeed .= '<channel>';
	$rssfeed .= '<title>'. htmlspecialchars($domain) .'</title>';
	$rssfeed .= '<link>http://'.htmlspecialchars($domain).'</link>';
	$rssfeed .= '<description>Latest notes from '.htmlspecialchars($domain).'</description>';
	$rssfeed .= '<language>en-us</language>';
	$rssfeed .= '<copyright>Copyright (C) 2012 Jaakko-Heikki Heusala</copyright>';

	$q = '';
	if(isset($_GET['q'])) $q = $_GET['q'];
	if(isset($_POST['q'])) $q = $_POST['q'];
	$sql = SQL::init();
	$query = 'SELECT * FROM `' . SQL_PREFIX . 'log`';
	$query .= ' WHERE domain=\'' . $sql->escape_string($domain) . '\'';
	if($q !== '') {
		$query .= " AND (LOCATE('". $sql->escape_string($q) ."', msg) != 0)";
	}
	$query .= ' ORDER BY updated, created';
	if( $result = $sql->query($query) ) {
		$list = array();
		while ($row = $result->fetch_object()){
			$msg = (string)$row['msg'];
			if(strlen($msg) <= 160) {
				$title = $msg;
			} else {
				$title = substr($msg, 0, 160) . '...';
			}
	        $rssfeed .= '<item>';
	        $rssfeed .= '<title>' . htmlspecialchars($title) . '</title>';
	        $rssfeed .= '<description>' . htmlspecialchars($msg) . '</description>';
	        //$rssfeed .= '<link>http://' . htmlspecialchars($domain) . '</link>';
	        $rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O", strtotime($row['updated'])) . '</pubDate>';
	        $rssfeed .= '</item>';
		}
	} else {
		throw new Exception('SQL error: ' . $sql->error);
	}

    $rssfeed .= '</channel>';
    $rssfeed .= '</rss>';

    echo $rssfeed . "\n";

} catch(Exception $e) {
	my_exception_handler($e);
}
return;
?>
