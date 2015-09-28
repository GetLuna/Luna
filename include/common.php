<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

if (!defined('FORUM_ROOT'))
	exit('The constant FORUM_ROOT must be defined and point to a valid Luna installation root directory.');

// Load the version class
require FORUM_ROOT.'include/version.php';

// Block prefetch requests
if (isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] == 'prefetch') {
	header('HTTP/1.1 403 Prefetching Forbidden');

	// Send no-cache headers
	header('Expires: Thu, 21 Jul 1977 07:30:00 GMT'); // When yours truly first set eyes on this world! :)
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache'); // For HTTP/1.0 compatibility

	exit;
}

// Attempt to load the configuration file config.php
if (file_exists(FORUM_ROOT.'config.php'))
	require FORUM_ROOT.'config.php';

// This fixes incorrect defined PUN in PunBB/FluxBB 1.2, 1.4 and 1.5 and ModernBB 1.6
if (defined('PUN'))
	define('FORUM', PUN);

// Load the functions script
require FORUM_ROOT.'include/functions.php';

// Load the security functions
require FORUM_ROOT.'include/class/luna_nonces.php';

// Load UTF-8 functions
require FORUM_ROOT.'include/utf8/utf8.php';

// Strip out "bad" UTF-8 characters
forum_remove_bad_characters();

// Reverse the effect of register_globals
forum_unregister_globals();

// If FORUM isn't defined, config.php is missing or corrupt
if (!defined('FORUM')) {
	header('Location: install.php');
	exit;
}

// Record the start time (will be used to calculate the generation time for the page)
$luna_start = get_microtime();

// Make sure PHP reports all errors except E_NOTICE. Luna supports E_ALL, but a lot of scripts it may interact with, do not
error_reporting(E_ALL ^ E_NOTICE);

// Force POSIX locale (to prevent functions such as strtolower() from messing up UTF-8 strings)
setlocale(LC_CTYPE, 'C');

// Turn off magic_quotes_runtime
if (get_magic_quotes_runtime())
	set_magic_quotes_runtime(0);

// Strip slashes from GET/POST/COOKIE/REQUEST/FILES (if magic_quotes_gpc is enabled)
if (!defined('FORUM_DISABLE_STRIPSLASHES') && get_magic_quotes_gpc()) {
	function stripslashes_array($array) {
		return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
	}

	$_GET = stripslashes_array($_GET);
	$_POST = stripslashes_array($_POST);
	$_COOKIE = stripslashes_array($_COOKIE);
	$_REQUEST = stripslashes_array($_REQUEST);
	if (is_array($_FILES)) {
		// Don't strip valid slashes from tmp_name path on Windows
		foreach ($_FILES AS $key => $value)
			$_FILES[$key]['tmp_name'] = str_replace('\\', '\\\\', $value['tmp_name']);
		$_FILES = stripslashes_array($_FILES);
	}
}

// If a cookie name is not specified in config.php, we use the default (luna_cookie)
if (empty($cookie_name))
	$cookie_name = 'luna_cookie';

// If the cache directory is not specified, we use the default setting
if (!defined('FORUM_CACHE_DIR'))
	define('FORUM_CACHE_DIR', FORUM_ROOT.'cache/');

// Define a few commonly used constants
define('FORUM_UNVERIFIED', 0);
define('FORUM_ADMIN', 1);
define('FORUM_MOD', 2);
define('FORUM_GUEST', 3);
define('FORUM_MEMBER', 4);

// Load DB abstraction layer and connect
require FORUM_ROOT.'include/dblayer/common_db.php';

// Start a transaction
$db->start_transaction();

// Load cached config
if (file_exists(FORUM_CACHE_DIR.'cache_config.php'))
	include FORUM_CACHE_DIR.'cache_config.php';

if (!defined('FORUM_CONFIG_LOADED')) {
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();
	require FORUM_CACHE_DIR.'cache_config.php';
}

// Let's guess a base_url
if (!isset($base_url)) {
	$base_url_take = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://').preg_replace('/:80$/', '', $_SERVER['HTTP_HOST']).str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
	if (substr($base_url_take, -1) == '/')
		$base_url_take = substr($base_url_take, 0, -1);
	if (substr($base_url_take, -10) == '/backstage')
		$base_url_take = substr($base_url_take, 0, -10);

	$base_url = $base_url_take;
}

// Verify that we are running the proper database schema revision
if (!array_key_exists('o_core_version', $luna_config) || version_compare($luna_config['o_core_version'], Version::FORUM_CORE_VERSION, '<')) {
	header('Location: '.$base_url.'/db_update.php');

	exit;
}

// Enable output buffering
if (!defined('FORUM_DISABLE_BUFFERING')) {
	// Should we use gzip output compression?
	if ($luna_config['o_gzip'] && extension_loaded('zlib'))
		ob_start('ob_gzhandler');
	else
		ob_start();
}

// Define standard date/time formats
$forum_time_formats = array($luna_config['o_time_format'], 'H:i:s', 'H:i', 'g:i:s a', 'g:i a');
$forum_date_formats = array($luna_config['o_date_format'], 'Y-m-d', 'Y-d-m', 'd-m-Y', 'm-d-Y', 'M j Y', 'jS M Y');

// Check/update/set cookie and fetch user info
$luna_user = array();
check_cookie($luna_user);

// Load l10n
require_once FORUM_ROOT.'include/pomo/MO.php';
require_once FORUM_ROOT.'include/l10n.php';

// Attempt to load the language file
if (file_exists(FORUM_ROOT.'lang/'.$luna_user['language'].'/luna.mo'))
	load_textdomain('luna', FORUM_ROOT.'lang/'.$luna_user['language'].'/luna.mo');
elseif (file_exists(FORUM_ROOT.'lang/English/luna.mo'))
	load_textdomain('luna', FORUM_ROOT.'lang/English/luna.mo');
else
	error('There is no valid language pack \''.luna_htmlspecialchars($luna_user['language']).'\' installed. Please reinstall a language of that name');

// Check if we are to display a maintenance message
if ($luna_config['o_maintenance'] && $luna_user['g_id'] > FORUM_ADMIN && !defined('FORUM_TURN_OFF_MAINT'))
	maintenance_message();

// Load cached bans
if (file_exists(FORUM_CACHE_DIR.'cache_bans.php'))
	include FORUM_CACHE_DIR.'cache_bans.php';

if (!defined('FORUM_BANS_LOADED')) {
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_bans_cache();
	require FORUM_CACHE_DIR.'cache_bans.php';
}

// Check if current user is banned
check_bans();

// Update online list
update_users_online();

// Check to see if we logged in without a cookie being set
if ($luna_user['is_guest'] && isset($_GET['login']))
	message(__('You appear to have logged in successfully, however a cookie has not been set. Please check your settings and if applicable, enable cookies for this website.', 'luna'));

// The maximum size of a comment, in bytes, since the field is now MEDIUMTEXT this allows ~16MB but lets cap at 1MB...
if (!defined('FORUM_MAX_POSTSIZE'))
	define('FORUM_MAX_POSTSIZE', 1048576);

if (!defined('FORUM_SEARCH_MIN_WORD'))
	define('FORUM_SEARCH_MIN_WORD', 3);
if (!defined('FORUM_SEARCH_MAX_WORD'))
	define('FORUM_SEARCH_MAX_WORD', 20);

if (!defined('FORUM_MAX_COOKIE_SIZE'))
	define('FORUM_MAX_COOKIE_SIZE', 4048);

// Are we admins?
require_once FORUM_ROOT.'include/backstage_functions.php';
$is_admin = check_is_admin();

require FORUM_ROOT.'include/general_functions.php';
require FORUM_ROOT.'include/draw_functions.php';
require FORUM_ROOT.'include/statistic_functions.php';

require FORUM_ROOT.'include/notifications.php';
