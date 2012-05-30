<?php
try {

	

	throw new Exception('Unknown request');
} catch(Exception $e) {
	echo 'FAIL: ' . $e;
}
return;
?>
