<?php

	// Read config
	if(!file_exists(dirname(__FILE__) . '/config.php')) { throw new Exception('No configuration!'); }
	require_once(dirname(__FILE__) . '/config.php');

	if(!defined('MAX_MSG_LENGTH')) { define('MAX_MSG_LENGTH', 1024); }
	if(!defined('USER_COOKIE_NAME')) { define('USER_COOKIE_NAME', 'InInfologCookie'); }

	if(!defined('TOP_DOMAIN')) {
		if(isset($_SERVER['SERVER_NAME'])) {
			$matches = array();
			if(preg_match('/\.([a-z0-9\-_]+\.[a-z0-9\-_]+)$/i', $_SERVER['SERVER_NAME'], $matches) === 1) {
				define('TOP_DOMAIN', $matches[1]);
			} else {
				define('TOP_DOMAIN', 'infolog.in');
			}
		} else {
			define('TOP_DOMAIN', 'infolog.in');
		}
	}

	if(!defined('CURRENT_DOMAIN')) {
		define('CURRENT_DOMAIN', isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'default.' . TOP_DOMAIN);
	}

	if(!defined('CURRENT_DOMAIN_TAG')) {
		define('CURRENT_DOMAIN_TAG', preg_replace('/\.'.preg_quote(TOP_DOMAIN).'$/i', '', CURRENT_DOMAIN));
	}

	if(!defined('COOKIE_DOMAIN')) {
		define('COOKIE_DOMAIN', TOP_DOMAIN);
	}

return;
?>
