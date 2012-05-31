<?php
try {
	require_once(dirname(__FILE__) . '/main.php');


	header("Content-Type: application/rss+xml; charset=UTF-8");

	$rssfeed = '<?xml version="1.0" encoding="ISO-8859-1"?>';
	$rssfeed .= '<rss version="2.0">';
	$rssfeed .= '<channel>';
	$rssfeed .= '<title>'. htmlspecialchars($_SERVER['SERVER_NAME']) .'</title>';
	$rssfeed .= '<link>http://'.htmlspecialchars($_SERVER['SERVER_NAME']).'</link>';
	$rssfeed .= '<description>Latest notes from '.htmlspecialchars($_SERVER['SERVER_NAME']).'</description>';
	$rssfeed .= '<language>en-us</language>';
	$rssfeed .= '<copyright>Copyright (C) 2012 Jaakko-Heikki Heusala</copyright>';

	$q = '';
	if(isset($_GET['q'])) $q = $_GET['q'];
	if(isset($_POST['q'])) $q = $_POST['q'];
	$sql = SQL::init();
	$query = 'SELECT * FROM `' . SQL_PREFIX . 'log`';
	$query .= ' WHERE domain=\'' . $sql->escape_string($_SERVER['SERVER_NAME']) . '\'';
	if($q !== '') {
		$query .= " AND (LOCATE('". $sql->escape_string($q) ."', msg) != 0)";
	}
	$query .= ' ORDER BY updated, created';
	if( $result = $sql->query($query) ) {
		$list = array();
		while ($row = $result->fetch_object()){
			if(strlen($row['msg']) <= 160) {
				$title = $row['msg'];
			} else {
				$title = substr($row['msg'], 0, 160) . '...';
			}
	        $rssfeed .= '<item>';
	        $rssfeed .= '<title>' . htmlspecialchars($title) . '</title>';
	        $rssfeed .= '<description>' . htmlspecialchars($row['msg']) . '</description>';
	        $rssfeed .= '<link>http://' . htmlspecialchars($_SERVER['SERVER_NAME']) . '</link>';
	        $rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O", strtotime($row['updated'])) . '</pubDate>';
	        $rssfeed .= '</item>';
		}
	} else {
		throw new Exception('SQL error: ' . $sql->error);
	}

    $rssfeed .= '</channel>';
    $rssfeed .= '</rss>';

    echo $rssfeed;

} catch(Exception $e) {
	my_exception_handler($e);
}
return;
?>
