<?php
try {

	

	throw new Exception('Unknown request');
} catch(Exception $e) {
	header('Content-Type: text/plain');
	echo 'Exception: ' . $e->getMessage() . "\n"
	   . '@code: ' . $e->getCode() . "\n"
	   . '@file: ' . $e->getFile() . ':' . $e->getLine() . "\n"
	   . '@stack/['."\n"
	   . $e->getTrace(). "\n"
	   . ']/@stack'."\n";
}
return;
?>
