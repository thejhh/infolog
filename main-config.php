<?php

	// Read config
	if(!file_exists(dirname(__FILE__) . '/config.php')) { throw new Exception('No configuration!'); }
	require_once(dirname(__FILE__) . '/config.php');

	if(!defined('MAX_MSG_LENGTH')) { define('MAX_MSG_LENGTH', 1024); }
	if(!defined('USER_COOKIE_NAME')) { define('USER_COOKIE_NAME', 'InInfologCookie'); }

	if(!defined('CURRENT_DOMAIN')) {
		define('CURRENT_DOMAIN', isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'default.infolog.in');
	}
	if(!defined('CURRENT_DOMAIN_TAG')) {
		define('CURRENT_DOMAIN_TAG', preg_replace('/\.infolog\.in$/i', '', CURRENT_DOMAIN));
	}

	if(!defined('TOP_DOMAIN')) {
		$matches = array();
		if(preg_match('/\.([a-z0-9\-_]+\.[a-z0-9\-_]+)$/i', '', CURRENT_DOMAIN, $matches)) {
			define('TOP_DOMAIN', $matches[1]);
		} else {
			define('TOP_DOMAIN', 'infolog.in');
		}
	}

	if(!defined('COOKIE_DOMAIN')) {
		define('COOKIE_DOMAIN', TOP_DOMAIN);
	}

return;
?>
