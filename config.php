<?php
	define('BASE_DIR', __DIR__ ."/");
	define('VENDOR_DIR', BASE_DIR . "vendor/");
	
	define('TPLINK_API_HOST', "https://wap.tplinkcloud.com/");
	
	// composer
	require_once(VENDOR_DIR ."autoload.php");
	
	// config: sensitive passwords
	require_once(BASE_DIR ."config.sensitive_passwords.php");
	
	// utilities
	require_once(BASE_DIR ."includes/utilities.php");
	
	// tplink functions
	require_once(BASE_DIR ."includes/tplink.php");