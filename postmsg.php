<?php
try {

	

	throw new Exception('Unknown request');
} catch(Exception $e) {
	header('Content-Type: text/plain');
	echo 'FAIL: ' . $e->getMessage() . "\n"
	   . ' at file: ' . $e->getFile() . ':' . $e->getLine() . "\n"
	   . ' with stack:'."\n"
	   . $e->getTrace();
}
return;
?>
