<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_SEARCH_MIN_WORD', 3);
define('FORUM_SEARCH_MAX_WORD', 20);

define('FORUM_ROOT', dirname(__FILE__).'/');

// Load the version class
require FORUM_ROOT.'include/version.php';

// The number of items to process per page view
define('PER_PAGE', 300);

// Don't set to UTF-8 until after we've found out what the default character set is
define('FORUM_NO_SET_NAMES', 1);

// Send the Content-type header in case the web server is setup to send something else
header('Content-type: text/html; charset=utf-8');

// Make sure we are running at least Version::MIN_PHP_VERSION
if (!function_exists('version_compare') || version_compare(PHP_VERSION, Version::MIN_PHP_VERSION, '<'))
	exit('You are running PHP version '.PHP_VERSION.'. Luna '.Version::FORUM_VERSION.' requires at least PHP '.Version::MIN_PHP_VERSION.' to run properly. You must upgrade your PHP installation before you can continue.');

// Attempt to load the configuration file config.php
if (file_exists(FORUM_ROOT.'config.php'))
	include FORUM_ROOT.'config.php';

// This fixes incorrect defined PUN, from FluxBB 1.5 and ModernBB 1.6
if (defined('PUN'))
	define('FORUM', PUN);

// If FORUM isn't defined, config.php is missing or corrupt
if (!defined('FORUM')) {
	header('Location: install.php');
	exit;
}

// Enable debug mode
if (!defined('FORUM_DEBUG'))
	define('FORUM_DEBUG', 1);

// Load the functions script
require FORUM_ROOT.'include/functions.php';
require FORUM_ROOT.'include/notifications.php';
require FORUM_ROOT.'include/draw_functions.php';
require FORUM_ROOT.'include/general_functions.php';

// Load UTF-8 functions
require FORUM_ROOT.'include/utf8/utf8.php';

// Strip out "bad" UTF-8 characters
forum_remove_bad_characters();

// Reverse the effect of register_globals
forum_unregister_globals();

// Turn on full PHP error reporting
error_reporting(E_ALL);

// Force POSIX locale (to prevent functions such as strtolower() from messing up UTF-8 strings)
setlocale(LC_CTYPE, 'C');

// Turn off magic_quotes_runtime
if (get_magic_quotes_runtime())
	set_magic_quotes_runtime(0);

// Strip slashes from GET/POST/COOKIE (if magic_quotes_gpc is enabled)
if (get_magic_quotes_gpc()) {
	function stripslashes_array($array) {
		return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
	}

	$_GET = stripslashes_array($_GET);
	$_POST = stripslashes_array($_POST);
	$_COOKIE = stripslashes_array($_COOKIE);
	$_REQUEST = stripslashes_array($_REQUEST);
}

// If a cookie name is not specified in config.php, we use the default (forum_cookie)
if (empty($cookie_name))
	$cookie_name = 'luna_cookie';

// If the cache directory is not specified, we use the default setting
if (!defined('FORUM_CACHE_DIR'))
	define('FORUM_CACHE_DIR', FORUM_ROOT.'cache/');

// Turn off PHP time limit
@set_time_limit(0);

// Define a few commonly used constants
define('FORUM_UNVERIFIED', 0);
define('FORUM_ADMIN', 1);
define('FORUM_MOD', 2);
define('FORUM_GUEST', 3);
define('FORUM_MEMBER', 4);

// Load DB abstraction layer and try to connect
require FORUM_ROOT.'include/dblayer/common_db.php';

// Check what the default character set is - since 1.2 didn't specify any we will use whatever the default was (usually latin1)
$old_connection_charset = defined('FORUM_DEFAULT_CHARSET') ? FORUM_DEFAULT_CHARSET : $db->get_names();

// Set the connection to UTF-8 now
$db->set_names('utf8');

// Get the forum config
$result = $db->query('SELECT * FROM '.$db->prefix.'config') or error('Unable to fetch config.', __FILE__, __LINE__, $db->error());
while ($cur_config_item = $db->fetch_row($result))
	$luna_config[$cur_config_item[0]] = $cur_config_item[1];

// Load l10n
require_once FORUM_ROOT.'include/pomo/MO.php';
require_once FORUM_ROOT.'include/l10n.php';

// Load language file
$default_lang = $luna_config['o_default_lang'];
if (!file_exists(FORUM_ROOT.'lang/'.$default_lang.'/luna.mo'))
	$default_lang = 'English';

load_textdomain('luna', FORUM_ROOT.'lang/'.$default_lang.'/luna.mo');

// Do some DB type specific checks
$mysql = false;
switch ($db_type) {
	case 'mysql':
	case 'mysqli':
	case 'mysql_innodb':
	case 'mysqli_innodb':
		$mysql_info = $db->get_version();
		if (version_compare($mysql_info['version'], Version::MIN_MYSQL_VERSION, '<'))
			error(sprintf(__('You are running %1$s version %2$s. Luna %3$s requires at least %1$s %4$s to run properly. You must upgrade your %1$s installation before you can continue.', 'luna'), 'MySQL', $mysql_info['version'], Version::FORUM_VERSION, Version::MIN_MYSQL_VERSION));

		$mysql = true;
		break;

	case 'pgsql':
		$pgsql_info = $db->get_version();
		if (version_compare($pgsql_info['version'], Version::MIN_PGSQL_VERSION, '<'))
			error(sprintf(__('You are running %1$s version %2$s. Luna %3$s requires at least %1$s %4$s to run properly. You must upgrade your %1$s installation before you can continue.', 'luna'), 'PostgreSQL', $pgsql_info['version'], Version::FORUM_VERSION, Version::MIN_PGSQL_VERSION));

		break;
}

// Check the database, search index and parser revision and the current version
if (isset($luna_config['o_database_revision']) && $luna_config['o_database_revision'] >= Version::FORUM_DB_VERSION &&
		isset($luna_config['o_searchindex_revision']) && $luna_config['o_searchindex_revision'] >= Version::FORUM_SI_VERSION &&
		isset($luna_config['o_parser_revision']) && $luna_config['o_parser_revision'] >= Version::FORUM_PARSER_VERSION &&
		array_key_exists('o_core_version', $luna_config) && version_compare($luna_config['o_core_version'], Version::FORUM_CORE_VERSION, '>=')) {
	draw_wall_error(__('Your forum is already as up-to-date as this script can make it.', 'luna'), '<a class="btn btn-default" href="index.php">Continue</a>', __('Let\'s get started', 'luna'));
	exit;
}

// Check style
$default_style = $luna_config['o_default_style'];
if (!file_exists(FORUM_ROOT.'themes/'.$default_style.'/style.css'))
	$default_style = 'Fifteen';

// Empty all output buffers and stop buffering
while (@ob_end_clean());

$stage = isset($_REQUEST['stage']) ? $_REQUEST['stage'] : '';
$old_charset = isset($_REQUEST['req_old_charset']) ? str_replace('ISO8859', 'ISO-8859', strtoupper($_REQUEST['req_old_charset'])) : 'ISO-8859-1';
$start_at = isset($_REQUEST['start_at']) ? intval($_REQUEST['start_at']) : 0;
$query_str = '';

// Show form
if (empty($stage)) {
	if (file_exists(FORUM_CACHE_DIR.'db_update.lock')) {
		// Deal with newlines, tabs and multiple spaces
		$pattern = array("\t", '  ', '  ');
		$replace = array('&#160; &#160; ', '&#160; ', ' &#160;');
		$message = str_replace($pattern, $replace, __('The forums are temporarily down for maintenance. Please try again in a few minutes.', 'luna'));

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php _e('Maintenance', 'luna') ?></title>
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link href="backstage/css/style.css" type="text/css" rel="stylesheet">
	</head>
	<body>
		<div class="alert alert-info">
			<h3><?php _e('Maintenance', 'luna') ?></h3>
		</div>
	</body>
</html>
<?php

	} else {

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Luna &middot; <?php _e('Update', 'luna') ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="robots" content="noindex, nofollow">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link href="backstage/css/style.css" type="text/css" rel="stylesheet">
	</head>
	<body onload="document.getElementById('install').start.disabled=false;">
		<div class="well form-box">
			<h3 class="form-title">Luna</h3>
			<form id="install" method="post" action="db_update.php">
				<input type="hidden" name="stage" value="start" />
				<div class="form-group">
					<input class="btn btn-primary btn-block btn-update" type="submit" name="start" value="<?php _e('Start update', 'luna') ?>" />
				</div>
			</form>
		</div>
	</body>
</html>
<?php

	}
	$db->end_transaction();
	$db->close();
	exit;

}

switch ($stage) {
	// Start by updating the database structure
	case 'start':
		$query_str = '?stage=preparse_posts';

		// If we don't need to update the database, skip this stage
		if (isset($luna_config['o_database_revision']) && $luna_config['o_database_revision'] >= Version::FORUM_DB_VERSION)
			break;

		// Change the default style if the old doesn't exist anymore
		if (!file_exists(FORUM_ROOT.'themes/'.$luna_config['o_default_style'].'/style.css'))
			$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.$db->escape($default_style).'\' WHERE conf_name = \'o_default_style\'') or error('Unable to update default style config', __FILE__, __LINE__, $db->error());
			
		// Legacy support: FluxBB 1.4
		// Make the message field MEDIUMTEXT to allow proper conversion of 65535 character posts to UTF-8
		$db->alter_field('posts', 'message', 'MEDIUMTEXT', true) or error('Unable to alter message field', __FILE__, __LINE__, $db->error());

		// Insert new config option o_feed_ttl
		if (!array_key_exists('o_feed_ttl', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_feed_ttl\', \'0\')') or error('Unable to insert config value \'o_feed_ttl\'', __FILE__, __LINE__, $db->error());

		// Add the last_report_sent column to the users table and the g_report_flood column to the groups table
		$db->add_field('users', 'last_report_sent', 'INT(10) UNSIGNED', true, null, 'last_email_sent') or error('Unable to add last_report_sent field', __FILE__, __LINE__, $db->error());
		$db->add_field('groups', 'g_report_flood', 'SMALLINT(6)', false, 60, 'g_email_flood') or error('Unable to add g_report_flood field', __FILE__, __LINE__, $db->error());

		// Set non-default g_send_email, g_flood_email and g_flood_report values properly
		$db->query('UPDATE '.$db->prefix.'groups SET g_send_email = 0 WHERE g_id = 3') or error('Unable to update group email permissions', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'groups SET g_email_flood = 0 WHERE g_id IN (1,2,3)') or error('Unable to update group email permissions', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'groups SET g_email_flood = 0, g_report_flood = 0 WHERE g_id IN (1,2,3)') or error('Unable to update group email permissions', __FILE__, __LINE__, $db->error());

		// Rename the subscription table
		$db->rename_table('subscriptions', 'topic_subscriptions');

		// if we don't have the forum_subscriptions table, create it
		if (!$db->table_exists('forum_subscriptions'))
		{
			$schema = array(
				'FIELDS'		=> array(
					'user_id'		=> array(
						'datatype'		=> 'INT(10) UNSIGNED',
						'allow_null'	=> false,
						'default'		=> '0'
					),
					'forum_id'		=> array(
						'datatype'		=> 'INT(10) UNSIGNED',
						'allow_null'	=> false,
						'default'		=> '0'
					)
				),
				'PRIMARY KEY'	=> array('user_id', 'forum_id')
			);

			$db->create_table('forum_subscriptions', $schema) or error('Unable to create forum subscriptions table', __FILE__, __LINE__, $db->error());
		}

		// Insert new config option o_forum_subscriptions
		if (!array_key_exists('o_forum_subscriptions', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_forum_subscriptions\', \'1\')') or error('Unable to insert config value \'o_forum_subscriptions\'', __FILE__, __LINE__, $db->error());

		// Rename config option o_subscriptions to o_topic_subscriptions
		if (!array_key_exists('o_topic_subscriptions', $luna_config))
			$db->query('UPDATE '.$db->prefix.'config SET conf_name=\'o_topic_subscriptions\' WHERE conf_name=\'o_subscriptions\'') or error('Unable to rename config value \'o_subscriptions\'', __FILE__, __LINE__, $db->error());

		// For MySQL(i) without InnoDB, change the engine of the online table (for performance reasons)
		if ($db_type == 'mysql' || $db_type == 'mysqli')
			$db->query('ALTER TABLE '.$db->prefix.'online ENGINE = MyISAM') or error('Unable to change engine type of online table to MyISAM', __FILE__, __LINE__, $db->error());
			
		// Legacy support: FluxBB 1.5
		$db->drop_field($db->prefix.'groups', 'g_promote_min_posts', 'INT(10) UNSIGNED', false, 0, 'g_user_title') or error('Unable to drop g_promote_min_posts field', __FILE__, __LINE__, $db->error());
		$db->drop_field($db->prefix.'groups', 'g_promote_next_group', 'INT(10) UNSIGNED', false, 0, 'g_promote_min_posts') or error('Unable to drop g_promote_next_group field', __FILE__, __LINE__, $db->error());
		$db->drop_field($db->prefix.'groups', 'g_post_links', 'TINYINT(1)', false, 0, 'g_delete_topics') or error('Unable to drop g_post_links field', __FILE__, __LINE__, $db->error());
		$db->drop_field($db->prefix.'groups', 'g_mod_promote_users', 'TINYINT(1)', false, 0, 'g_mod_ban_users') or error('Unable to drop g_mod_ban_users field', __FILE__, __LINE__, $db->error());
		if (!$db->table_exists('search_cache')) {
			$schema = array(
				'FIELDS'		=> array(
					'id'			=> array(
						'datatype'		=> 'SERIAL',
						'allow_null'	=> false
					),
					'rank'			=> array(
						'datatype'		=> 'VARCHAR(50)',
						'allow_null'	=> false,
						'default'		=> '\'\''
					),
					'min_posts'		=> array(
						'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
						'allow_null'	=> false,
						'default'		=> '0'
					)
				),
				'PRIMARY KEY'	=> array('id')
			);
		
			$db->create_table('ranks', $schema) or error('Unable to create ranks table', __FILE__, __LINE__, $db->error());
		}
		if (!array_key_exists('o_ranks', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_ranks\', \'1\')') or error('Unable to insert config value \'o_ranks\'', __FILE__, __LINE__, $db->error());

		// Legacy support: ModernBB 1.6, 1.7, 2.0, 2.1, 2.2, 2.3, 3.0, 3.1, 3.2, 3.3, 3.4, 3.5, 3.6, 3.7 and 3.8
		// Since 2.0-beta.1: Add the marked column to the posts table
		$db->add_field('posts', 'marked', 'TINYINT(1)', false, 0, null) or error('Unable to add marked field', __FILE__, __LINE__, $db->error());

		// Since 2.0-beta.3: Remove obsolete o_quickjump permission from config table
		if (array_key_exists('o_quickjump', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_quickjump\'') or error('Unable to remove config value \'o_quickjump\'', __FILE__, __LINE__, $db->error());

		// Since 2.0-rc.1: Remove obsolete o_show_dot permission from config table
		if (array_key_exists('o_show_dot', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_show_dot\'') or error('Unable to remove config value \'o_show_dot\'', __FILE__, __LINE__, $db->error());

		// Since 3.2-alpha: Add the first_run column to the users table
		$db->add_field('users', 'first_run', 'TINYINT(1)', false, 0) or error('Unable to add first_run field', __FILE__, __LINE__, $db->error());

		// Since 3.2-alpha: Insert new config option o_show_first_run
		if (!array_key_exists('o_show_first_run', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_show_first_run\', \'1\')') or error('Unable to insert config value \'o_show_first_run\'', __FILE__, __LINE__, $db->error());

		// Since 3.2-alpha: Insert new config option o_first_run_guests
		if (!array_key_exists('o_first_run_guests', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_first_run_guests\', \'1\')') or error('Unable to insert config value \'o_first_run_guests\'', __FILE__, __LINE__, $db->error());

		// Since 3.2-alpha: Insert new config option o_first_run_message
		if (!array_key_exists('o_first_run_message', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_first_run_message\', \'\')') or error('Unable to insert config value \'o_first_run_message\'', __FILE__, __LINE__, $db->error());

		// Since 3.2-alpha: Remove obsolete o_redirect_delay permission from config table
		if (array_key_exists('o_redirect_delay', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_redirect_delay\'') or error('Unable to remove config value \'o_redirect_delay\'', __FILE__, __LINE__, $db->error());

		// Since 3.2-beta: Add o_has_posted
		if (!array_key_exists('o_has_posted', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_has_posted\', \'1\')') or error('Unable to insert config value \'o_has_posted\'', __FILE__, __LINE__, $db->error());

		// Since 3.3-alpha: Add o_enable_advanced_search
		if (!array_key_exists('o_enable_advanced_search', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_enable_advanced_search\', \'1\')') or error('Unable to insert config value \'o_enable_advanced_search\'', __FILE__, __LINE__, $db->error());

		// Since 3.3-beta: Drop the backstage_style column from the forums table
		$db->drop_field('users', 'backstage_style', 'INT', true, 0) or error('Unable to drop backstage_style field', __FILE__, __LINE__, $db->error());

		// Since 3.4-rc: Insert new config option o_cookie_bar
		if (!array_key_exists('o_cookie_bar', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_cookie_bar\', \'0\')') or error('Unable to insert config value \'o_cookie_bar\'', __FILE__, __LINE__, $db->error());

		// Since 3.4-rc: Insert new config option o_moderated_by
		if (!array_key_exists('o_moderated_by', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_moderated_by\', \'1\')') or error('Unable to insert config value \'o_moderated_by\'', __FILE__, __LINE__, $db->error());

		// Since 3.4-rc: Make password field VARCHAR(256)
		$db->alter_field('users', 'password', 'VARCHAR(256)', true) or error('Unable to alter password field', __FILE__, __LINE__, $db->error());

		// Since 3.4-rc: Insert new config option video_width
		if (!array_key_exists('o_video_width', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_video_width\', \'640\')') or error('Unable to insert config value \'o_video_width\'', __FILE__, __LINE__, $db->error());

		// Since 3.4-rc: Insert new config option video_height
		if (!array_key_exists('o_video_height', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_video_height\', \'360\')') or error('Unable to insert config value \'o_video_height\'', __FILE__, __LINE__, $db->error());

		// Since 3.4.1: Drop the jabber column from the forums table
		$db->drop_field('users', 'jabber') or error('Unable to drop jabber field', __FILE__, __LINE__, $db->error());

		// Since 3.4.1: Drop the icq column from the forums table
		$db->drop_field('users', 'icq') or error('Unable to drop icq field from user table', __FILE__, __LINE__, $db->error());

		// Since 3.4.1: Drop the yahoo column from the forums table
		$db->drop_field('users', 'yahoo') or error('Unable to drop yahoo field from user table', __FILE__, __LINE__, $db->error());

		// Since 3.4.1: Drop the aim column from the forums table
		$db->drop_field('users', 'aim') or error('Unable to drop aim field from user table', __FILE__, __LINE__, $db->error());

		// Since 3.4.1: Add the facebook column to the users table
		$db->add_field('users', 'facebook', 'VARCHAR(30)', true, null) or error('Unable to add facebook field to user table', __FILE__, __LINE__, $db->error());

		// Since 3.4.1: Add the twitter column to the users table
		$db->add_field('users', 'twitter', 'VARCHAR(30)', true, null) or error('Unable to add twitter field to user table', __FILE__, __LINE__, $db->error());

		// Since 3.4.1: Add the google column to the users table
		$db->add_field('users', 'google', 'VARCHAR(30)', true, null) or error('Unable to add google field to user table', __FILE__, __LINE__, $db->error());

		// Since 3.5-beta: Remove obsolete o_antispam_api permission from config table
		if (array_key_exists('o_antispam_api', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_antispam_api\'') or error('Unable to remove config value \'o_antispam_api\'', __FILE__, __LINE__, $db->error());

		// Since 3.5-beta: Add o_core_version
		if (!array_key_exists('o_core_version', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_core_version\', \''.Version::FORUM_CORE_VERSION.'\')') or error('Unable to insert config value \'o_core_version\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.35.2488: Remove obsolete o_index_update_check permission from config table
		if (array_key_exists('o_index_update_check', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_index_update_check\'') or error('Unable to remove config value \'o_index_update_check\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.37.2564: Add the parent_id column to the forums table
		$db->add_field('forums', 'parent_id', 'INT', true, 0) or error('Unable to add parent_id field', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.2944: Drop the redirect_url column to the forums table
		$db->drop_field($db->prefix.'forums', 'redirect_url', 'VARCHAR(100)', true, 0) or error('Unable to drop redirect_url field', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.2946: Drop the backstage_color column to the forums table
		$db->drop_field($db->prefix.'users', 'backstage_color', 'VARCHAR(25)', false, 0) or error('Unable to drop backstage_color field', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.2975: Remove obsolete o_header_title permission from config table
		if (array_key_exists('o_header_title', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_header_title\'') or error('Unable to remove config value \'o_header_title\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.2975: Remove obsolete o_header_desc permission from config table
		if (array_key_exists('o_header_desc', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_header_desc\'') or error('Unable to remove config value \'o_header_desc\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.2975: Remove obsolete o_menu_title permission from config table
		if (array_key_exists('o_menu_title', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_menu_title\'') or error('Unable to remove config value \'o_menu_title\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.2975: Remove obsolete o_show_version permission from config table
		if (array_key_exists('o_show_version', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_show_version\'') or error('Unable to remove config value \'o_show_version\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.2975: Remove obsolete o_show_index_stats permission from config table
		if (array_key_exists('o_show_index_stats', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_show_index_stats\'') or error('Unable to remove config value \'o_show_index_stats\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.2981: Add the menu table
		if (!$db->table_exists('menu')) {
			$schema = array(
				'FIELDS'		=> array(
					'id'			=> array(
						'datatype'		=> 'SERIAL',
						'allow_null'	=> false
					),
					'url'			=> array(
						'datatype'		=> 'VARCHAR(200)',
						'allow_null'	=> false,
						'default'		=> '\'\''
					),
					'name'			=> array(
						'datatype'		=> 'VARCHAR(200)',
						'allow_null'	=> false,
						'default'		=> '\'\''
					),
					'disp_position'	=> array(
						'datatype'		=> 'INT(10)',
						'allow_null'	=> false,
						'default'		=> '0'
					),
					'visible'			=> array(
						'datatype'		=> 'INT(10)',
						'allow_null'	=> false,
						'default'		=> '1'
					),
					'sys_entry'		=> array(
						'datatype'		=> 'INT(10)',
						'allow_null'	=> false,
						'default'		=> 0
					)
				),
				'PRIMARY KEY'	=> array('id')
			);
		
			$db->create_table('menu', $schema) or error('Unable to create menu table', __FILE__, __LINE__, $db->error());

			$db->query('INSERT INTO '.$db_prefix.'menu (url, name, disp_position, visible, sys_entry) VALUES(\'index.php\', \'Index\', 1, \'1\', 1)')
				or error('Unable to add Index menu item. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
		
			$db->query('INSERT INTO '.$db_prefix.'menu (url, name, disp_position, visible, sys_entry) VALUES(\'userlist.php\', \'Users\', 2, \'1\', 1)')
				or error('Unable to add Users menu item. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
		
			$db->query('INSERT INTO '.$db_prefix.'menu (url, name, disp_position, visible, sys_entry) VALUES(\'search.php\', \'Search\', 3, \'1\', 1)')
				or error('Unable to add Search menu item. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
		}

		// Since 0.0.40.2985: Remove obsolete o_show_index permission from config table
		if (array_key_exists('o_show_index', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_show_index\'') or error('Unable to remove config value \'o_show_index\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.2985: Remove obsolete o_show_userlist permission from config table
		if (array_key_exists('o_show_userlist', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_show_userlist\'') or error('Unable to remove config value \'o_show_userlist\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.2985: Remove obsolete o_show_search permission from config table
		if (array_key_exists('o_show_index', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_show_search\'') or error('Unable to remove config value \'o_show_search\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.2985: Remove obsolete o_show_rules permission from config table
		if (array_key_exists('o_show_rules', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_show_rules\'') or error('Unable to remove config value \'o_show_rules\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.2989: Add o_admin_note
		if (!array_key_exists('o_admin_note', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_admin_note\', NULL)') or error('Unable to insert config value \'o_admin_note\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.2985: Remove obsolete p_message_bbcode permission from config table
		if (array_key_exists('p_message_bbcode', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'p_message_bbcode\'') or error('Unable to remove config value \'p_message_bbcode\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.40.3048: Remove obsolete o_additional_navlinks permission from config table
		if (array_key_exists('o_additional_navlinks', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_additional_navlinks\'') or error('Unable to remove config value \'o_additional_navlinks\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.3221: Drop the last_poster column to the forums table
		$db->drop_field($db->prefix.'forums', 'last_poster', 'VARCHAR(200)', true) or error('Unable to drop last_poster field', __FILE__, __LINE__, $db->error());

		// Since 0.0.3221: Drop the last_topic column to the forums table
		$db->drop_field($db->prefix.'forums', 'last_topic', 'VARCHAR(255)', false, 0) or error('Unable to drop last_topic field', __FILE__, __LINE__, $db->error());

		// Since 0.0.3247: Remove obsolete o_quickpost permission from config table
		if (array_key_exists('o_quickpost', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_quickpost\'') or error('Unable to remove config value \'o_quickpost\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.3250: Add the messages table
		if (!$db->table_exists('messages')) {
			$schema = array(
				'FIELDS'			=> array(
					'id'				=> array(
						'datatype'		=> 'SERIAL',
						'allow_null'	=> false
					),
					'shared_id'		=> array(
						'datatype'		=> 'INT(10)',
						'allow_null'	=> false,
						'default'		=> '0'
					),
					'last_shared_id'	=> array(
						'datatype'			=> 'INT(10)',
						'allow_null'		=> false,
						'default'			=> '0'
					),
					'last_post'			=> array(
						'datatype'			=> 'INT(10)',
						'allow_null'		=> true,
						'default'			=> '0'
					),
					'last_post_id'		=> array(
						'datatype'			=> 'INT(10)',
						'allow_null'		=> true,
						'default'			=> '0'
					),
					'last_poster'		=> array(
						'datatype'			=> 'VARCHAR(255)',
						'allow_null'		=> false,
						'default'			=> '0'
					),
					'owner'				=> array(
						'datatype'			=> 'INTEGER',
						'allow_null'		=> false,
						'default'			=> '0'
					),
					'subject'			=> array(
						'datatype'			=> 'VARCHAR(255)',
						'allow_null'		=> false
					),
					'message'			=> array(
						'datatype'			=> 'MEDIUMTEXT',
						'allow_null'		=> false
					),
					'hide_smilies'	=> array(
						'datatype'		=> 'TINYINT(1)',
						'allow_null'	=> false,
						'default'		=> '0'
					),
					'show_message'	=> array(
						'datatype'		=> 'TINYINT(1)',
						'allow_null'	=> false,
						'default'		=> '0'
					),
					'sender'	=> array(
						'datatype'		=> 'VARCHAR(200)',
						'allow_null'	=> false
					),
					'receiver'	=> array(
						'datatype'		=> 'VARCHAR(200)',
						'allow_null'	=> true
					),
					'sender_id'	=> array(
						'datatype'			=> 'INT(10)',
						'allow_null'		=> false,
						'default'			=> '0'
					),
					'receiver_id'	=> array(
						'datatype'			=> 'VARCHAR(255)',
						'allow_null'		=> true,
						'default'			=> '0'
					),
					'sender_ip'	=> array(
						'datatype'			=> 'VARCHAR(39)',
						'allow_null'		=> true
					),
					'posted'	=> array(
						'datatype'			=> 'INT(10)',
						'allow_null'		=> false,
					),
					'showed'	=> array(
						'datatype'			=> 'TINYINT(1)',
						'allow_null'		=> false,
						'default'			=> '0'
					)
				),
				'PRIMARY KEY'		=> array('id'),
			);
			
			$db->create_table('messages', $schema) or error('Unable to create messages table', __FILE__, __LINE__, $db->error());
		}

		// Since 0.0.3263: Add the g_pm column to the groups table
		$db->add_field('groups', 'g_pm', 'TINYINT(1)', false, '1', 'g_email_flood') or error('Unable to add column "g_pm" to table "groups"', __FILE__, __LINE__, $db->error());

		// Since 0.0.3263: Add the g_pm_limit column to the groups table
		$db->add_field('groups', 'g_pm_limit', 'INT', false, '20', 'g_pm') or error('Unable to add column "g_pm_limit" to table "groups"', __FILE__, __LINE__, $db->error());

		// Since 0.0.3263: Add the use_pm column to the users table
		$db->add_field('users', 'use_pm', 'TINYINT(1)', false, '1', 'activate_key') or error('Unable to add column "use_pm" to table "users"', __FILE__, __LINE__, $db->error());

		// Since 0.0.3263: Add the notify_pm column to the users table
		$db->add_field('users', 'notify_pm', 'TINYINT(1)', false, '1', 'use_pm') or error('Unable to add column "notify_pm" to table "users"', __FILE__, __LINE__, $db->error());

		// Since 0.0.3263: Add the notify_pm_full column to the users table
		$db->add_field('users', 'notify_pm_full', 'TINYINT(1)', false, '0', 'notify_with_post') or error('Unable to add column "num_pms" to table "users"', __FILE__, __LINE__, $db->error());

		// Since 0.0.3263: Add the num_pms column to the users table
		$db->add_field('users', 'num_pms', 'INT(10) UNSIGNED', false, '0', 'num_posts') or error('Unable to add column "num_pms" to table "users"', __FILE__, __LINE__, $db->error());

		// Since 0.0.3265: Add o_pms_enabled feature
		if (!array_key_exists('o_pms_enabled', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_pms_enabled\', \'1\')') or error('Unable to insert config value \'o_pms_enabled\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.3265: Add o_pms_mess_per_page feature
		if (!array_key_exists('o_pms_mess_per_page', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_pms_mess_per_page\', \'10\')') or error('Unable to insert config value \'o_pms_mess_per_page\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.3265: Add o_pms_max_receiver feature
		if (!array_key_exists('o_pms_max_receiver', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_pms_max_receiver\', \'5\')') or error('Unable to insert config value \'o_pms_max_receiver\'', __FILE__, __LINE__, $db->error());

		// Since 0.0.3265: Add o_pms_notification feature
		if (!array_key_exists('o_pms_notification', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_pms_notification\', \'1\')') or error('Unable to insert config value \'o_pms_notification\'', __FILE__, __LINE__, $db->error());

		// Since 0.1.3283: Remove obsolete o_private_message permission from config table
		if (array_key_exists('o_private_message', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_private_message\'') or error('Unable to remove config value \'o_private_message\'', __FILE__, __LINE__, $db->error());

		// Since 0.1.3300: Remove obsolete o_user_menu_sidebar permission from config table
		if (array_key_exists('o_user_menu_sidebar', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_user_menu_sidebar\'') or error('Unable to remove config value \'o_user_menu_sidebar\'', __FILE__, __LINE__, $db->error());

		// Since 0.1.3301: Set sys_entry to 0 for Backstage
		$db->query('UPDATE '.$db->prefix.'menu SET sys_entry = \'0\' WHERE id = \'4\'') or error('Unable to reset Backstage menu item', __FILE__, __LINE__, $db->error());

		// Since 0.1.3320: Remove obsolete o_backstage_dark permission from config table
		if (array_key_exists('o_backstage_dark', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_backstage_dark\'') or error('Unable to remove config value \'o_backstage_dark\'', __FILE__, __LINE__, $db->error());

		// Since 0.2.3414: Remove obsolete o_forum_new_style permission from config table
		if (array_key_exists('o_forum_new_style', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_forum_new_style\'') or error('Unable to remove config value \'o_forum_new_style\'', __FILE__, __LINE__, $db->error());

		// Since 0.2.3415: Remove obsolete o_notifications permission from config table
		if (array_key_exists('o_notifications', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_notifications\'') or error('Unable to remove config value \'o_notifications\'', __FILE__, __LINE__, $db->error());

		// Since 0.2.3423: Add the messages table
		if (!$db->table_exists('notifications')) {
			$schema = array(
				'FIELDS'			=> array(
					'id'				=> array(
						'datatype'			=> 'SERIAL',
						'allow_null'		=> false
					),
					'user_id'			=> array(
						'datatype'			=> 'INT(10)',
						'allow_null'		=> false,
						'default'			=> '0'
					),
					'message'			=> array(
						'datatype'			=> 'VARCHAR(255)',
						'allow_null'		=> false,
						'default'			=> '0'
					),
					'icon'				=> array(
						'datatype'			=> 'VARCHAR(255)',
						'allow_null'		=> false,
						'default'			=> '0'
					),
					'link'			=> array(
						'datatype'			=> 'VARCHAR(255)',
						'allow_null'		=> false,
						'default'			=> '0'
					),
					'time'				=> array(
						'datatype'			=> 'INT(11)',
						'allow_null'		=> false,
						'default'			=> '0'
					),
					'viewed'			=> array(
						'datatype'		=> 'TINYINT(1)',
						'allow_null'		=> false,
						'default'			=> '0'
					),
				),
				'PRIMARY KEY'		=> array('id'),
			);
			
			$db->create_table('notifications', $schema) or error('Unable to create notifications table', __FILE__, __LINE__, $db->error());
		}

		// Since 0.2.3425: Drop the color column from the notifications table
		$db->drop_field($db->prefix.'notifications', 'color', 'VARCHAR(255)', false, 0) or error('Unable to drop color field', __FILE__, __LINE__, $db->error());

		// Since 0.2.3459: Add o_first_run_backstage feature
		if (!array_key_exists('o_first_run_backstage', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_first_run_backstage\', \'0\')') or error('Unable to insert config value \'o_first_run_backstage\'', __FILE__, __LINE__, $db->error());

		// Since 0.2.3495: Add o_emoji_size feature
		if (!array_key_exists('o_emoji_size', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_emoji_size\', \'16\')') or error('Unable to insert config value \'o_emoji_size\'', __FILE__, __LINE__, $db->error());

		// Since 0.2.3495: Add o_back_to_top feature
		if (!array_key_exists('o_back_to_top', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_back_to_top\', \'1\')') or error('Unable to insert config value \'o_back_to_top\'', __FILE__, __LINE__, $db->error());

		// Since 0.2.3495: Add o_show_copyright feature
		if (!array_key_exists('o_show_copyright', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_show_copyright\', \'1\')') or error('Unable to insert config value \'o_show_copyright\'', __FILE__, __LINE__, $db->error());

		// Since 0.2.3495: Add o_copyright_type feature
		if (!array_key_exists('o_copyright_type', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_copyright_type\', \'0\')') or error('Unable to insert config value \'o_copyright_type\'', __FILE__, __LINE__, $db->error());

		// Since 0.2.3495: Add o_copyright_type feature
		if (!array_key_exists('o_custom_copyright', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_custom_copyright\', NULL)') or error('Unable to insert config value \'o_custom_copyright\'', __FILE__, __LINE__, $db->error());

		// Since 0.2.3558: Remove obsolete o_reading_list permission from config table
		if (array_key_exists('o_reading_list', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_reading_list\'') or error('Unable to remove config value \'o_reading_list\'', __FILE__, __LINE__, $db->error());

		// Since 0.2.3561: Add o_header_search feature
		if (!array_key_exists('o_header_search', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_header_search\', \'1\')') or error('Unable to insert config value \'o_header_search\'', __FILE__, __LINE__, $db->error());

		// Since 0.2.3562: Add o_board_statistics feature
		if (!array_key_exists('o_board_statistics', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_board_statistics\', \'1\')') or error('Unable to insert config value \'o_board_statistics\'', __FILE__, __LINE__, $db->error());

		// Since 0.2.3563: Add o_notification_flyout feature
		if (!array_key_exists('o_notification_flyout', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_notification_flyout\', \'1\')') or error('Unable to insert config value \'o_notification_flyout\'', __FILE__, __LINE__, $db->error());

		// Since 0.3.3721: Remove reading_list table
		if ($db->table_exists('reading_list'))
			$db->drop_table('reading_list') or error('Unable to drop reading_list table', __FILE__, __LINE__, $db->error());

		// Since 0.3.3724: Remove sending_lists table
		if ($db->table_exists('sending_lists'))
			$db->drop_table('sending_lists') or error('Unable to drop sending_lists table', __FILE__, __LINE__, $db->error());

		// Since 0.3.3734: Remove contacts table
		if ($db->table_exists('contacts'))
			$db->drop_table('contacts') or error('Unable to drop contacts table', __FILE__, __LINE__, $db->error());

		// Since 0.3.3752: Add the soft column to the posts table
		$db->add_field('posts', 'soft', 'TINYINT(1)', false, 0, null) or error('Unable to add soft field', __FILE__, __LINE__, $db->error());

		// Since 0.3.3752: Add the soft column to the topics table
		$db->add_field('topics', 'soft', 'TINYINT(1)', false, 0, null) or error('Unable to add soft field', __FILE__, __LINE__, $db->error());
		
		// Since 0.3.3765: Add new g_soft_delete_view field to the groups table
		$db->add_field('groups', 'g_soft_delete_view', 'TINYINT(1)', false, 0, 'g_user_title') or error('Unable to add g_soft_delete_view field', __FILE__, __LINE__, $db->error());
		
		// Since 0.3.3765: Add new g_soft_delete_posts field to the groups table
		$db->add_field('groups', 'g_soft_delete_posts', 'TINYINT(1)', false, 0, 'g_user_title') or error('Unable to add g_soft_delete_posts field', __FILE__, __LINE__, $db->error());
		
		// Since 0.3.3765: Add new g_soft_delete_topics field to the groups table
		$db->add_field('groups', 'g_soft_delete_topics', 'TINYINT(1)', false, 0, 'g_user_title') or error('Unable to add g_soft_delete_topics field', __FILE__, __LINE__, $db->error());

		// Since 0.3.3800: Remove obsolete o_post_responsive permission from config table
		if (array_key_exists('o_post_responsive', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_post_responsive\'') or error('Unable to remove config value \'o_post_responsive\'', __FILE__, __LINE__, $db->error());

		// Since 0.3.3814: Add o_emoji feature
		if (!array_key_exists('o_emoji', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_emoji\', \'0\')') or error('Unable to insert config value \'o_emoji\'', __FILE__, __LINE__, $db->error());

		// Since 0.4.3861: Add the color_scheme column to the users table
		$db->add_field('users', 'color_scheme', 'INT(25)', false, '2') or error('Unable to add column "color_scheme" to table "users"', __FILE__, __LINE__, $db->error());

		// Since 0.4.3861: Drop the color column to the users table
		$db->drop_field($db->prefix.'users', 'color', 'VARCHAR(25)', true, 0) or error('Unable to drop color field', __FILE__, __LINE__, $db->error());

		// Since 0.4.3902: Add o_code_name feature
		if (!array_key_exists('o_code_name', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_code_name\', \''.Version::FORUM_CODE_NAME.'\')') or error('Unable to insert config value \'o_code_name\'', __FILE__, __LINE__, $db->error());

		// Since 0.4.3903: Add o_update_ring feature
		if (!array_key_exists('o_update_ring', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_update_ring\', \'1\')') or error('Unable to insert config value \'o_update_ring\'', __FILE__, __LINE__, $db->error());

		// Since 0.9.4156: Add the color column to the forums table
		$db->add_field('forums', 'color', 'VARCHAR(25)', false, '\'#2788cb\'') or error('Unable to add column "color" to table "forums"', __FILE__, __LINE__, $db->error());

		// Since 0.9.4191: Remove obsolete o_quickpost permission from config table
		if (array_key_exists('o_admin_notes', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_admin_notes\'') or error('Unable to remove config value \'o_admin_notes\'', __FILE__, __LINE__, $db->error());

		// Since 0.9.4229: Remove obsolete o_smilies permission from config table
		if (array_key_exists('o_smilies', $luna_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_smilies\'') or error('Unable to remove config value \'o_smilies\'', __FILE__, __LINE__, $db->error());

		// Since 1.1.4286: Add the accent column to the users table
		$db->add_field('users', 'accent', 'INT(10)', false, '2') or error('Unable to add column "accent" to table "users"', __FILE__, __LINE__, $db->error());

		// Since 1.1.4289: Add the adapt_time column to the users table
		$db->add_field('users', 'adapt_time', 'TINYINT(1)', false, '0') or error('Unable to add column "adapt_time" to table "users"', __FILE__, __LINE__, $db->error());

		// Since 1.1.4381: Add o_default_accent feature
		if (!array_key_exists('o_default_accent', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_default_accent\', \'2\')') or error('Unable to insert config value \'o_default_accent\'', __FILE__, __LINE__, $db->error());

		// Since 1.1.4504: Add o_announcement_title feature
		if (!array_key_exists('o_announcement_title', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_announcement_title\', \'\')') or error('Unable to insert config value \'o_announcement_title\'', __FILE__, __LINE__, $db->error());

		// Since 1.1.4504: Add o_announcement_type feature
		if (!array_key_exists('o_announcement_type', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_announcement_type\', \'info\')') or error('Unable to insert config value \'o_announcement_type\'', __FILE__, __LINE__, $db->error());

		// Since 1.1.4682: Add the solved column to the topics table
		$db->add_field('topics', 'solved', 'INT(10) UNSIGNED', true) or error('Unable to add solved field', __FILE__, __LINE__, $db->error());

		// Since 1.1.4688: Add o_board_tags feature
		if (!array_key_exists('o_board_tags', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_board_tags\', \'\')') or error('Unable to insert config value \'o_board_tags\'', __FILE__, __LINE__, $db->error());

		// Since 1.1.4704: Add o_cookie_bar_url feature
		if (!array_key_exists('o_cookie_bar_url', $luna_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_cookie_bar_url\', \'http://getluna.org/docs/cookies.php\')') or error('Unable to insert config value \'o_cookie_bar_url\'', __FILE__, __LINE__, $db->error());

		break;

	// Preparse posts
	case 'preparse_posts':
		$query_str = '?stage=preparse_sigs';

		// If we don't need to parse the posts, skip this stage
		if (isset($luna_config['o_parser_revision']) && $luna_config['o_parser_revision'] >= Version::FORUM_PARSER_VERSION)
			break;

		require FORUM_ROOT.'include/parser.php';

		// Fetch posts to process this cycle
		$result = $db->query('SELECT id, message FROM '.$db->prefix.'posts WHERE id > '.$start_at.' ORDER BY id ASC LIMIT '.PER_PAGE) or error('Unable to fetch posts', __FILE__, __LINE__, $db->error());

		$temp = array();
		$end_at = 0;
		while ($cur_item = $db->fetch_assoc($result)) {
			echo sprintf(__('Preparsing %1$s %2$s …', 'luna'), __('post', 'luna'), $cur_item['id']).'<br />'."\n";
			$db->query('UPDATE '.$db->prefix.'posts SET message = \''.$db->escape(preparse_bbcode($cur_item['message'], $temp)).'\' WHERE id = '.$cur_item['id']) or error('Unable to update post', __FILE__, __LINE__, $db->error());

			$end_at = $cur_item['id'];
		}

		// Check if there is more work to do
		if ($end_at > 0) {
			$result = $db->query('SELECT 1 FROM '.$db->prefix.'posts WHERE id > '.$end_at.' ORDER BY id ASC LIMIT 1') or error('Unable to fetch next ID', __FILE__, __LINE__, $db->error());

			if ($db->num_rows($result) > 0)
				$query_str = '?stage=preparse_posts&start_at='.$end_at;
		}

		break;


	// Preparse signatures
	case 'preparse_sigs':
		$query_str = '?stage=rebuild_idx';

		// If we don't need to parse the sigs, skip this stage
		if (isset($luna_config['o_parser_revision']) && $luna_config['o_parser_revision'] >= Version::FORUM_PARSER_VERSION)
			break;

		require FORUM_ROOT.'include/parser.php';

		// Fetch users to process this cycle
		$result = $db->query('SELECT id, signature FROM '.$db->prefix.'users WHERE id > '.$start_at.' ORDER BY id ASC LIMIT '.PER_PAGE) or error('Unable to fetch users', __FILE__, __LINE__, $db->error());

		$temp = array();
		$end_at = 0;
		while ($cur_item = $db->fetch_assoc($result)) {
			echo sprintf(__('Preparsing %1$s %2$s …', 'luna'), __('signature', 'luna'), $cur_item['id']).'<br />'."\n";
			$db->query('UPDATE '.$db->prefix.'users SET signature = \''.$db->escape(preparse_bbcode($cur_item['signature'], $temp, true)).'\' WHERE id = '.$cur_item['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());

			$end_at = $cur_item['id'];
		}

		// Check if there is more work to do
		if ($end_at > 0) {
			$result = $db->query('SELECT 1 FROM '.$db->prefix.'users WHERE id > '.$end_at.' ORDER BY id ASC LIMIT 1') or error('Unable to fetch next ID', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result) > 0)
				$query_str = '?stage=preparse_sigs&start_at='.$end_at;
		}

		break;


	// Rebuild the search index
	case 'rebuild_idx':
		$query_str = '?stage=finish';

		// If we don't need to update the search index, skip this stage
		if (isset($luna_config['o_searchindex_revision']) && $luna_config['o_searchindex_revision'] >= Version::FORUM_SI_VERSION)
			break;

		if ($start_at == 0) {
			// Truncate the tables just in-case we didn't already (if we are coming directly here without converting the tables)
			$db->truncate_table('search_cache') or error('Unable to empty search cache table', __FILE__, __LINE__, $db->error());
			$db->truncate_table('search_matches') or error('Unable to empty search index match table', __FILE__, __LINE__, $db->error());
			$db->truncate_table('search_words') or error('Unable to empty search index words table', __FILE__, __LINE__, $db->error());

			// Reset the sequence for the search words (not needed for SQLite)
			switch ($db_type) {
				case 'mysql':
				case 'mysqli':
				case 'mysql_innodb':
				case 'mysqli_innodb':
					$db->query('ALTER TABLE '.$db->prefix.'search_words auto_increment=1') or error('Unable to update table auto_increment', __FILE__, __LINE__, $db->error());
					break;

				case 'pgsql';
					$db->query('SELECT setval(\''.$db->prefix.'search_words_id_seq\', 1, false)') or error('Unable to update sequence', __FILE__, __LINE__, $db->error());
					break;
			}
		}

		require FORUM_ROOT.'include/search_idx.php';

		// Fetch posts to process this cycle
		$result = $db->query('SELECT p.id, p.message, t.subject, t.first_post_id FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id WHERE p.id > '.$start_at.' ORDER BY p.id ASC LIMIT '.PER_PAGE) or error('Unable to fetch posts', __FILE__, __LINE__, $db->error());

		$end_at = 0;
		while ($cur_item = $db->fetch_assoc($result)) {
			echo sprintf(__('Rebuilding index for %1$s %2$s', 'luna'), __('post', 'luna'), $cur_item['id']).'<br />'."\n";

			if ($cur_item['id'] == $cur_item['first_post_id'])
				update_search_index('post', $cur_item['id'], $cur_item['message'], $cur_item['subject']);
			else
				update_search_index('post', $cur_item['id'], $cur_item['message']);

			$end_at = $cur_item['id'];
		}

		// Check if there is more work to do
		if ($end_at > 0) {
			$result = $db->query('SELECT 1 FROM '.$db->prefix.'posts WHERE id > '.$end_at.' ORDER BY id ASC LIMIT 1') or error('Unable to fetch next ID', __FILE__, __LINE__, $db->error());

			if ($db->num_rows($result) > 0)
				$query_str = '?stage=rebuild_idx&start_at='.$end_at;
		}

		break;


	// Show results page
	case 'finish':
		
		// Give a "Success" notifcation
		if ($luna_config['o_cur_version'] != Version::FORUM_VERSION)
			new_notification('2', 'backstage/index.php', 'Luna has been updated to '.Version::FORUM_VERSION, 'fa-cloud-upload');

		// We update the version numbers
		$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.Version::FORUM_VERSION.'\' WHERE conf_name = \'o_cur_version\'') or error('Unable to update version', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.Version::FORUM_CORE_VERSION.'\' WHERE conf_name = \'o_core_version\'') or error('Unable to update core version', __FILE__, __LINE__, $db->error());
		$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.Version::FORUM_CODE_NAME.'\' WHERE conf_name = \'o_code_name\'') or error('Unable to update code name', __FILE__, __LINE__, $db->error());

		// And the database revision number
		$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.Version::FORUM_DB_VERSION.'\' WHERE conf_name = \'o_database_revision\'') or error('Unable to update database revision number', __FILE__, __LINE__, $db->error());

		// And the search index revision number
		$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.Version::FORUM_SI_VERSION.'\' WHERE conf_name = \'o_searchindex_revision\'') or error('Unable to update search index revision number', __FILE__, __LINE__, $db->error());

		// And the parser revision number
		$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.Version::FORUM_PARSER_VERSION.'\' WHERE conf_name = \'o_parser_revision\'') or error('Unable to update parser revision number', __FILE__, __LINE__, $db->error());

		// Check the default language still exists!
		if (!file_exists(FORUM_ROOT.'lang/'.$luna_config['o_default_lang'].'/common.php'))
			$db->query('UPDATE '.$db->prefix.'config SET conf_value = \'English\' WHERE conf_name = \'o_default_lang\'') or error('Unable to update default language', __FILE__, __LINE__, $db->error());

		// Check the default style still exists!
		if (!file_exists(FORUM_ROOT.'themes/'.$luna_config['o_default_style'].'/style.css'))
			$db->query('UPDATE '.$db->prefix.'config SET conf_value = \'Fifteen\' WHERE conf_name = \'o_default_style\'') or error('Unable to update default style', __FILE__, __LINE__, $db->error());

		// This feels like a good time to synchronize the forums
		$result = $db->query('SELECT id FROM '.$db->prefix.'forums') or error('Unable to fetch forum IDs', __FILE__, __LINE__, $db->error());

		while ($row = $db->fetch_row($result))
			update_forum($row[0]);

		// Empty the PHP cache
		forum_clear_cache();

		// Delete the update lock file
		@unlink(FORUM_CACHE_DIR.'db_update.lock');

		header('Location: index.php');
		break;
}

$db->end_transaction();
$db->close();

if ($query_str != '')
	exit('<script type="text/javascript">window.location="db_update.php'.$query_str.'"</script><noscript><meta http-equiv="refresh" content="0;url=db_update.php'.$query_str.'" /></noscript>');

