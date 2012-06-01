<?php
try {
	require_once(dirname(__FILE__) . '/main.php');
	header("Content-Type: application/rss+xml; charset=UTF-8");

	$domain = CURRENT_DOMAIN;

	$rssfeed = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
	$rssfeed .= '<rss version="2.0">'."\n";
	$rssfeed .= '<channel>'."\n";
	$rssfeed .= '<title>'. htmlspecialchars($domain) .'</title>'."\n";
	$rssfeed .= '<link>http://'.htmlspecialchars($domain).'</link>'."\n";
	$rssfeed .= '<description>Latest notes from '.htmlspecialchars($domain).'</description>'."\n";
	$rssfeed .= '<language>en-us</language>'."\n";
	$rssfeed .= '<copyright>Copyright (C) 2012 Jaakko-Heikki Heusala</copyright>'."\n";

	$q = '';
	if(isset($_GET['q'])) $q = $_GET['q'];
	if(isset($_POST['q'])) $q = $_POST['q'];
	$sql = SQL::init();
	$query = 'SELECT * FROM `' . SQL_PREFIX . 'log`';
	$query .= ' WHERE domain=\'' . $sql->escape_string($domain) . '\'';
	if($q !== '') {
		$query .= " AND (LOCATE('". $sql->escape_string($q) ."', msg) != 0)";
	}
	$query .= ' AND UNIX_TIMESTAMP(SUBDATE(NOW(), INTERVAL 24 HOUR)) <= UNIX_TIMESTAMP(created)';
	$query .= ' ORDER BY updated DESC';
	if( $result = $sql->query($query) ) {
		$list = array();
		while ($row = $result->fetch_object()){
			$msg = (string)$row->msg;
			if(strlen($msg) <= 160) {
				$title = $msg;
			} else {
				$title = substr($msg, 0, 160) . '...';
			}
	        $rssfeed .= '<item>'."\n";
	        $rssfeed .= '<title>' . htmlspecialchars($title) . '</title>'."\n";
	        $rssfeed .= '<description>' . htmlspecialchars($msg) . '</description>'."\n";
	        //$rssfeed .= '<link>http://' . htmlspecialchars($domain) . '</link>'."\n";
	        $rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O", strtotime($row->updated)) . '</pubDate>'."\n";
	        $rssfeed .= '</item>'."\n";
		}
	} else {
		throw new Exception('SQL error: ' . $sql->error);
	}

    $rssfeed .= '</channel>'."\n";
    $rssfeed .= '</rss>'."\n";

    echo $rssfeed . "\n";

} catch(Exception $e) {
	my_exception_handler($e);
}
return;
?>
