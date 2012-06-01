<?php
/* */
class ErrorIdentifier {
	static private $id_cache = array();
	static public function get($data) {
		$key = '';
		if(isset($data['error'])) {
			$key .= $data['error'];
			if(isset($data['file'])) $key .= '|' . $data['file'];
			if(isset($data['line'])) $key .= '|' . $data['line'];
		} else {
			$key = json_encode($data);
		}
		if(isset(self::$id_cache[$key])) {
			self::$id_cache[$key]++;
		} else {
			self::$id_cache[$key] = 1;
		}
		return sprintf('%s-%08d', sha1($key), self::$id_cache[$key]);
	}
}
return;
?>
