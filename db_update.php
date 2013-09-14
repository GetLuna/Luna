<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// The ModernBB version this script updates to
define('UPDATE_TO', '2.0.0');

define('UPDATE_TO_DB_REVISION', 32);
define('UPDATE_TO_SI_REVISION', 2);
define('UPDATE_TO_PARSER_REVISION', 5);

define('MIN_PHP_VERSION', '5.0.0');
define('MIN_MYSQL_VERSION', '5.0.1');
define('MIN_MARIADB_VERSION', '5.3.4');
define('MIN_PGSQL_VERSION', '7.0.0');
define('FORUM_SEARCH_MIN_WORD', 3);
define('FORUM_SEARCH_MAX_WORD', 20);

// The number of items to process per page view
define('PER_PAGE', 300);

// Don't set to UTF-8 until after we've found out what the default character set is
define('FORUM_NO_SET_NAMES', 1);

// Make sure we are running at least MIN_PHP_VERSION
if (!function_exists('version_compare') || version_compare(PHP_VERSION, MIN_PHP_VERSION, '<'))
	exit('You are running PHP version '.PHP_VERSION.'. ModernBB '.UPDATE_TO.' requires at least PHP '.MIN_PHP_VERSION.' to run properly. You must upgrade your PHP installation before you can continue.');

define('FORUM_ROOT', dirname(__FILE__).'/');

// Attempt to load the configuration file config.php
if (file_exists(FORUM_ROOT.'config.php'))
	include FORUM_ROOT.'config.php';

// This fixes incorrect defined PUN, from FluxBB 1.4 and 1.5 and ModernBB 1.6
if (defined('PUN'))
	define('FORUM', PUN);

// If FORUM isn't defined, config.php is missing or corrupt
if (!defined('FORUM'))
{
	header('Location: install.php');
	exit;
}

// Enable debug mode
if (!defined('FORUM_DEBUG'))
	define('FORUM_DEBUG', 1);

// Load the functions script
require FORUM_ROOT.'include/functions.php';

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
if (get_magic_quotes_gpc())
{
	function stripslashes_array($array)
	{
		return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
	}

	$_GET = stripslashes_array($_GET);
	$_POST = stripslashes_array($_POST);
	$_COOKIE = stripslashes_array($_COOKIE);
	$_REQUEST = stripslashes_array($_REQUEST);
}

// If a cookie name is not specified in config.php, we use the default (forum_cookie)
if (empty($cookie_name))
	$cookie_name = 'pun_cookie';

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
	$pun_config[$cur_config_item[0]] = $cur_config_item[1];

// Load language file
$default_lang = $pun_config['o_default_lang'];

if (!file_exists(FORUM_ROOT.'lang/'.$default_lang.'/update.php'))
	$default_lang = 'English';

require FORUM_ROOT.'lang/'.$default_lang.'/common.php';
require FORUM_ROOT.'lang/'.$default_lang.'/update.php';

// Check current version
$cur_version = $pun_config['o_cur_version'];

if (version_compare($cur_version, '1.4', '<'))
	error(sprintf($lang_update['Version mismatch error'], $db_name));

// Do some DB type specific checks
$mysql = false;
switch ($db_type)
{
	case 'mysql':
	case 'mysqli':
	case 'mysql_innodb':
	case 'mysqli_innodb':
		$mysql_info = $db->get_version();
		if (version_compare($mysql_info['version'], MIN_MYSQL_VERSION, '<'))
			error(sprintf($lang_update['You are running error'], 'MySQL', $mysql_info['version'], UPDATE_TO, MIN_MYSQL_VERSION));

		$mysql = true;
		break;
		
	case 'mariadb':
		$mariadb_info = $db->get_version();
		if (version_compare($mariadb_info['version'], MIN_MARIADB_VERSION, '<'))
			error(sprintf($lang_update['You are running error'], 'MardiaDB', $mariadb_info['version'], UPDATE_TO, MIN_MARIADB_VERSION));

		$mariadb = true;
		break;

	case 'pgsql':
		$pgsql_info = $db->get_version();
		if (version_compare($pgsql_info['version'], MIN_PGSQL_VERSION, '<'))
			error(sprintf($lang_update['You are running error'], 'PostgreSQL', $pgsql_info['version'], UPDATE_TO, MIN_PGSQL_VERSION));

		break;
}

// Check the database, search index and parser revision and the current version
if (isset($pun_config['o_database_revision']) && $pun_config['o_database_revision'] >= UPDATE_TO_DB_REVISION &&
		isset($pun_config['o_searchindex_revision']) && $pun_config['o_searchindex_revision'] >= UPDATE_TO_SI_REVISION &&
		isset($pun_config['o_parser_revision']) && $pun_config['o_parser_revision'] >= UPDATE_TO_PARSER_REVISION &&
		version_compare($pun_config['o_cur_version'], UPDATE_TO, '>='))
	error($lang_update['No update error']);

$default_style = $pun_config['o_default_style'];
if (!file_exists(FORUM_ROOT.'style/'.$default_style.'.css'))
	$default_style = 'Air';

//
// Determines whether $str is UTF-8 encoded or not
//
function seems_utf8($str)
{
	$str_len = strlen($str);
	for ($i = 0; $i < $str_len; ++$i)
	{
		if (ord($str[$i]) < 0x80) continue; # 0bbbbbbb
		else if ((ord($str[$i]) & 0xE0) == 0xC0) $n=1; # 110bbbbb
		else if ((ord($str[$i]) & 0xF0) == 0xE0) $n=2; # 1110bbbb
		else if ((ord($str[$i]) & 0xF8) == 0xF0) $n=3; # 11110bbb
		else if ((ord($str[$i]) & 0xFC) == 0xF8) $n=4; # 111110bb
		else if ((ord($str[$i]) & 0xFE) == 0xFC) $n=5; # 1111110b
		else return false; # Does not match any model

		for ($j = 0; $j < $n; ++$j) # n bytes matching 10bbbbbb follow ?
		{
			if ((++$i == strlen($str)) || ((ord($str[$i]) & 0xC0) != 0x80))
				return false;
		}
	}

	return true;
}


//
// Translates the number from a HTML numeric entity into an UTF-8 character
//
function dcr2utf8($src)
{
	$dest = '';
	if ($src < 0)
		return false;
	else if ($src <= 0x007f)
		$dest .= chr($src);
	else if ($src <= 0x07ff)
	{
		$dest .= chr(0xc0 | ($src >> 6));
		$dest .= chr(0x80 | ($src & 0x003f));
	}
	else if ($src == 0xFEFF)
	{
		// nop -- zap the BOM
	}
	else if ($src >= 0xD800 && $src <= 0xDFFF)
	{
		// found a surrogate
		return false;
	}
	else if ($src <= 0xffff)
	{
		$dest .= chr(0xe0 | ($src >> 12));
		$dest .= chr(0x80 | (($src >> 6) & 0x003f));
		$dest .= chr(0x80 | ($src & 0x003f));
	}
	else if ($src <= 0x10ffff)
	{
		$dest .= chr(0xf0 | ($src >> 18));
		$dest .= chr(0x80 | (($src >> 12) & 0x3f));
		$dest .= chr(0x80 | (($src >> 6) & 0x3f));
		$dest .= chr(0x80 | ($src & 0x3f));
	}
	else
	{
		// out of range
		return false;
	}

	return $dest;
}

function utf8_callback_1($matches)
{
	return dcr2utf8($matches[1]);
}


function utf8_callback_2($matches)
{
	return dcr2utf8(hexdec($matches[1]));
}

// Empty all output buffers and stop buffering
while (@ob_end_clean());


$stage = isset($_REQUEST['stage']) ? $_REQUEST['stage'] : '';
$old_charset = isset($_REQUEST['req_old_charset']) ? str_replace('ISO8859', 'ISO-8859', strtoupper($_REQUEST['req_old_charset'])) : 'ISO-8859-1';
$start_at = isset($_REQUEST['start_at']) ? intval($_REQUEST['start_at']) : 0;
$query_str = '';

// Show form
if (empty($stage))
{
	if (file_exists(FORUM_CACHE_DIR.'db_update.lock'))
	{
		// Deal with newlines, tabs and multiple spaces
		$pattern = array("\t", '  ', '  ');
		$replace = array('&#160; &#160; ', '&#160; ', ' &#160;');
		$message = str_replace($pattern, $replace, $lang_update['Down']);

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang_common['lang_identifier'] ?>" lang="<?php echo $lang_common['lang_identifier'] ?>" dir="<?php echo $lang_common['lang_direction'] ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $lang_update['Maintenance'] ?></title>
<link rel="stylesheet" type="text/css" href="style/<?php echo $default_style ?>.css" />
</head>
<body>

<div id="punmaint" class="pun">
<div class="top-box"><div><!-- Top Corners --></div></div>
<div class="punwrap">

<div id="brdmain">
<div class="block">
	<h2><?php echo $lang_update['Maintenance'] ?></h2>
	<div class="box">
		<div class="inbox">
			<p><?php echo $lang_update['Down'] ?></p>
		</div>
	</div>
</div>
</div>

</div>
<div class="end-box"><div><!-- Bottom Corners --></div></div>
</div>

</body>
</html>
<?php

	}
	else
	{

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>ModernBB &middot; <?php echo $lang_update['Update'] ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex, nofollow">
        <link href="include/bootstrap/bootstrap.css" type="text/css" rel="stylesheet">
        <link href="style/Randomness.css" type="text/css" rel="stylesheet">
        <style type="text/css">
		body {
            padding-bottom: 40px;
			padding-top: 60px;
            background-color: #f2f2f2;
        }
        .form-signin {
            max-width: 350px;
            padding: 19px 29px 9px 29px;
            margin: 0 auto 20px;
            background-color: #fff;
			border-left: #2ca0e9 5px solid;
			box-shadow: 0 7px 7px 0px rgba(221,221,221,0.37);
        }
		h1 {
			font-weight: 100;
		}
        .form-pass {
            padding: 19px 29px 4px;
        }
        .form-signin .form-signin-heading {
            margin-bottom: 10px;
        }
        .form-signin input[type="password"] {
            font-size: 16px;
            height: auto;
            margin-bottom: 5px;
            padding: 7px 9px;
        }
		.form-signin-heading {
			color: #2ca0e9;
			text-transform: lowercase;
			margin-top: 0;
		}
		.btn {
			margin-bottom: 20px;
		}
        </style>
	</head>
	<body onload="document.getElementById('install').req_db_pass.focus();document.getElementById('install').start.disabled=false;">
		<!-- Content start -->
        <form class="form-signin" id="install" method="post" action="db_update.php">
            <h1 class="form-signin-heading"><?php echo $lang_update['Update'] ?></h1>
            <fieldset>
                <input type="hidden" name="stage" value="start" />
                <p><?php echo $lang_update['Database password info'] ?></p>
                <input class="form-control full-form" type="password" id="req_db_pass" name="req_db_pass" placeholder="Database password" />
            </fieldset>
			<div><input class="btn btn-primary btn-block" type="submit" name="start" value="<?php echo $lang_update['Start update'] ?>" /></div>
		</form>
	</body>
</html>
<?php

	}
	$db->end_transaction();
	$db->close();
	exit;

}

// Read the lock file
$lock = file_exists(FORUM_CACHE_DIR.'db_update.lock') ? trim(file_get_contents(FORUM_CACHE_DIR.'db_update.lock')) : false;
$lock_error = false;

// Generate or fetch the UID - this confirms we have a valid admin
if (isset($_POST['req_db_pass']))
{
	$req_db_pass = strtolower(pun_trim($_POST['req_db_pass']));

	switch ($db_type)
	{
		// For SQLite we compare against the database file name, since the password is left blank
		case 'sqlite':
			if ($req_db_pass != strtolower($db_name))
				error(sprintf($lang_update['Invalid file error'], 'config.php'));

			break;
		// For everything else, check the password matches
		default:
			if ($req_db_pass != strtolower($db_password))
				error(sprintf($lang_update['Invalid password error'], 'config.php'));

			break;
	}

	// Generate a unique id to identify this session, only if this is a valid session
	$uid = pun_hash($req_db_pass.'|'.uniqid(rand(), true));
	if ($lock) // We already have a lock file
		$lock_error = true;
	else // Create the lock file
	{
		$fh = @fopen(FORUM_CACHE_DIR.'db_update.lock', 'wb');
		if (!$fh)
			error(sprintf($lang_update['Unable to lock error'], 'cache'));

		fwrite($fh, $uid);
		fclose($fh);

		// Regenerate the config cache
		if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
			require FORUM_ROOT.'include/cache.php';

		generate_config_cache();
	}
}
else if (isset($_GET['uid']))
{
	$uid = pun_trim($_GET['uid']);
	if (!$lock || $lock != $uid) // The lock doesn't exist or doesn't match the given UID
		$lock_error = true;
}
else
	error($lang_update['No password error']);

// If there is an error with the lock file
if ($lock_error)
	error(sprintf($lang_update['Script runs error'], FORUM_CACHE_DIR.'db_update.lock'));

switch ($stage)
{
	// Start by updating the database structure
	case 'start':
		$query_str = '?stage=preparse_posts';

		// If we don't need to update the database, skip this stage
		if (isset($pun_config['o_database_revision']) && $pun_config['o_database_revision'] >= UPDATE_TO_DB_REVISION)
			break;

		// Make the message field MEDIUMTEXT to allow proper conversion of 65535 character posts to UTF-8
		$db->alter_field('posts', 'message', 'MEDIUMTEXT', true) or error('Unable to alter message field', __FILE__, __LINE__, $db->error());

		// Add the DST option to the users table
		$db->add_field('users', 'dst', 'TINYINT(1)', false, 0, 'timezone') or error('Unable to add dst field', __FILE__, __LINE__, $db->error());
		
		// Since 2.0-beta.1: Add the marked column to the posts table
		$db->add_field('posts', 'marked', 'TINYINT(1)', false, 0, null) or error('Unable to add marked field', __FILE__, __LINE__, $db->error());

		// Since 2.0-rc.1: Add the parent_forum_id column to the forums table
		$db->drop_field('forums', 'parent_forum_id', 'INT', true, 0) or error('Unable to drio parent_forum_id field', __FILE__, __LINE__, $db->error());
		
		// Since 2.0-rc.1: Change style from anything to Randomness when updating from ModernBB 2.0-beta.3 or lower
		if (FORUM_VERSION < '2.0-rc.1') {
			$db->query('UPDATE '.$db->prefix.'users SET style = Randomness WHERE style != Randomness') or error('Unable to update group ID', __FILE__, __LINE__, $db->error());
			$db->query('UPDATE '.$db->prefix.'config SET o_default_style = Randomness WHERE o_default_style != Randomness') or error('Unable to update group ID', __FILE__, __LINE__, $db->error());
		}

		// Since 1.4-beta.1: Add search index revision number
		if (!array_key_exists('o_searchindex_revision', $pun_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_searchindex_revision\', \'0\')') or error('Unable to insert config value \'o_searchindex_revision\'', __FILE__, __LINE__, $db->error());

		// Since 1.4-beta.1: Add parser revision number
		if (!array_key_exists('o_parser_revision', $pun_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_parser_revision\', \'0\')') or error('Unable to insert config value \'o_parser_revision\'', __FILE__, __LINE__, $db->error());

		// Since 1.4-beta.1: Insert new config option o_quote_depth
		if (!array_key_exists('o_quote_depth', $pun_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_quote_depth\', \'3\')') or error('Unable to insert config value \'o_quote_depth\'', __FILE__, __LINE__, $db->error());

		// Since 1.4-beta.1: Insert new config option o_feed_type
		if (!array_key_exists('o_feed_type', $pun_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_feed_type\', \'2\')') or error('Unable to insert config value \'o_feed_type\'', __FILE__, __LINE__, $db->error());

		// Since 1.4-beta.1: Insert new config option o_feed_ttl
		if (!array_key_exists('o_feed_ttl', $pun_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_feed_ttl\', \'0\')') or error('Unable to insert config value \'o_feed_ttl\'', __FILE__, __LINE__, $db->error());

		// Since 2.0-beta.2: Insert new config option o_antispam_api
		if (!array_key_exists('o_antispam_api', $pun_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_antispam_api\', NULL)') or error('Unable to insert config value \'o_antispam_api\'', __FILE__, __LINE__, $db->error());
			
		// Since 2.0-beta.3: Remove obsolete o_quickjump permission from config table
		if (array_key_exists('o_quickjump', $pun_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_quickjump\'') or error('Unable to remove config value \'o_quickjump\'', __FILE__, __LINE__, $db->error());
			
		// Since 2.0-rc.1: Remove obsolete o_show_dot permission from config table
		if (array_key_exists('o_show_dot', $pun_config))
			$db->query('DELETE FROM '.$db->prefix.'config WHERE conf_name = \'o_show_dot\'') or error('Unable to remove config value \'o_show_dot\'', __FILE__, __LINE__, $db->error());
		
		// Since 1.4-beta.1: Insert config option o_base_url which was removed in 1.3
		if (!array_key_exists('o_base_url', $pun_config))
		{
			// If it isn't in $pun_config['o_base_url'] it should be in $base_url, but just in-case it isn't we can make a guess at it
			if (!isset($base_url))
			{
				// Make an educated guess regarding base_url
				$base_url  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';	// protocol
				$base_url .= preg_replace('%:(80|443)$%', '', $_SERVER['HTTP_HOST']);							// host[:port]
				$base_url .= str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));							// path
			}

			if (substr($base_url, -1) == '/')
				$base_url = substr($base_url, 0, -1);

			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_base_url\', \''.$db->escape($base_url).'\')') or error('Unable to insert config value \'o_base_url\'', __FILE__, __LINE__, $db->error());
		}
		
		// Add an index to username on the bans table
		if ($mysql || $mariadb)
			$db->add_index('bans', 'username_idx', array('username(25)')) or error('Unable to add username_idx index', __FILE__, __LINE__, $db->error());
		else
			$db->add_index('bans', 'username_idx', array('username')) or error('Unable to add username_idx index', __FILE__, __LINE__, $db->error());

		// Change the username_idx on users to a unique index of max size 25
		$db->drop_index('users', 'username_idx') or error('Unable to drop old username_idx index', __FILE__, __LINE__, $db->error());
		$field = $mysql || $mariadb ? 'username(25)' : 'username';

		// Attempt to add a unique index. If the user doesn't use a transactional database this can fail due to multiple matching usernames in the
		// users table. This is bad, but just giving up if it happens is even worse! If it fails just add a regular non-unique index.
		if (!$db->add_index('users', 'username_idx', array($field), true))
			$db->add_index('users', 'username_idx', array($field)) or error('Unable to add username_idx field', __FILE__, __LINE__, $db->error());

		// Add the last_report_sent column to the users table and the g_report_flood
		// column to the groups table
		$db->add_field('users', 'last_report_sent', 'INT(10) UNSIGNED', true, null, 'last_email_sent') or error('Unable to add last_report_sent field', __FILE__, __LINE__, $db->error());
		$db->add_field('groups', 'g_report_flood', 'SMALLINT(6)', false, 60, 'g_email_flood') or error('Unable to add g_report_flood field', __FILE__, __LINE__, $db->error());

		// Change the search_data column to mediumtext
		$db->alter_field('search_cache', 'search_data', 'MEDIUMTEXT', true) or error('Unable to alter search_data field', __FILE__, __LINE__, $db->error());

		// Rename the subscription table
		$db->rename_table('subscriptions', 'topic_subscriptions');

		// If we don't have the forum_subscriptions table, create it
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
		
		// Since ModernBB 2.0-beta.3 - If we don't have the toolbar_conf table, create it
		if (!$db->table_exists('toolbar_conf'))
		{
			$schema = array(
				'FIELDS' => array(
					'conf_name' => array(
						'datatype' => 'VARCHAR(40)',
						'allow_null' => false,
						'default' => '\'\''
					),
					'conf_value' => array(
						'datatype' => 'VARCHAR(40)',
						'allow_null' => false,
						'default' => '\'\''
					)
				),
				'PRIMARY KEY' => array('conf_name')
			);
			$db->create_table('toolbar_conf', $schema) or error('Unable to create toolbar_conf table', __FILE__, __LINE__, $db->error());
			
			$config = array(
				'enable_form'		=>	'1',
				'enable_quickform'	=>	'0',
				'img_pack'			=>	'Default',
				'nb_smilies'		=>	'16',
			);
			
			while (list($conf_name, $conf_value) = @each($config))
				$db->query('INSERT INTO '.$db->prefix.'toolbar_conf (conf_name, conf_value) VALUES(\''.$db->escape($conf_name).'\', \''.$db->escape($conf_value).'\')') or error('Unable to insert in toolbar_conf table', __FILE__, __LINE__, $db->error());
		}
		
		// Since ModernBB 2.0-beta.3 - If we don't have the toolbar_tags table, create it
		if (!$db->table_exists('toolbar_tags'))
		{
			$schema = array(
				'FIELDS' => array(
					'name' => array(
						'datatype' => 'VARCHAR(20)',
						'allow_null' => false,
						'default' => '\'\''
					),
					'code' => array(
						'datatype' => 'VARCHAR(20)',
						'allow_null' => false,
						'default' => '\'\''
					),
					'enable_form' => array(
						'datatype' => 'TINYINT(1)',
						'allow_null' => false,
						'default' => '0'
					),
					'enable_quick' => array(
						'datatype' => 'TINYINT(1)',
						'allow_null' => false,
						'default' => '0'
					),
					'image' => array(
						'datatype' => 'VARCHAR(40)',
						'allow_null' => false,
						'default' => '\'\''
					),
					'func' => array(
						'datatype' => 'TINYINT(1)',
						'allow_null' => false,
						'default' => '0'
					),
					'position' => array(
						'datatype' => 'TINYINT(2) UNSIGNED',
						'allow_null' => false,
						'default' => '1'
					)
				),
				'PRIMARY KEY' => array('name')
			);
			$db->create_table('toolbar_tags', $schema) or error('Unable to create toolbar_tags table', __FILE__, __LINE__, $db->error());
			
			$tags = array(
				"'smilies', '', '1', '1', 'smilies.png', '0', '0'",
				"'bold', 'b', '1', '1', 'bold.png', '0', '1'",
				"'italic', 'i', '1', '1', 'italic.png', '0', '2'",
				"'underline', 'u', '1', '1', 'underline.png', '0', '3'",
				"'strike', 's', '1', '1', 'strike.png', '0', '4'",
				"'sup', 'sup', '1', '0', 'sup.png', '0', '5'",
				"'sub', 'sub', '1', '0', 'sub.png', '0', '6'",
				"'heading', 'h', '1', '1', 'size_plus.png', '0', '7'",
				"'left', 'left', '1', '0', 'align_left.png', '0', '8'",
				"'right', 'right', '1', '0', 'align_right.png', '0', '9'",
				"'center', 'center', '1', '0', 'align_center.png', '0', '10'",
				"'justify', 'justify', '1', '0', 'align_justify.png', '0', '11'",
				"'color', 'color', '1', '1', 'color.png', '0', '12'",
				"'q', 'q', '1', '0', 'quote.png', '0', '13'",
				"'acronym', 'acronym', '1', '0', 'acronym.png', '1', '14'",
				"'img', 'img', '1', '1', 'img.png', '2', '15'",
				"'code', 'code', '1', '1', 'pre.png', '0', '16'",
				"'quote', 'quote', '1', '1', 'bquote.png', '1', '17'",
				"'link', 'url', '1', '1', 'link.png', '2', '18'",
				"'email', 'email', '1', '1', 'email.png', '2', '19'",
				"'video', 'video', '1', '0', 'video.png', '3', '20'",
				"'li', '*', '1', '1', 'li.png', '0', '21'",
				"'list', 'list', '1', '1', 'ul.png', '1', '22'"
			);
			
			foreach ($tags as $tag)
				$db->query('INSERT INTO '.$db->prefix.'toolbar_tags (name, code, enable_form, enable_quick, image, func, position) VALUES ('.$tag.')') or error('Unable to insert in toolbar_tags table', __FILE__, __LINE__, $db->error());
		}

		// Insert new config option o_forum_subscriptions
		if (!array_key_exists('o_forum_subscriptions', $pun_config))
			$db->query('INSERT INTO '.$db->prefix.'config (conf_name, conf_value) VALUES (\'o_forum_subscriptions\', \'1\')') or error('Unable to insert config value \'o_forum_subscriptions\'', __FILE__, __LINE__, $db->error());

		// Rename config option o_subscriptions to o_topic_subscriptions
		if (!array_key_exists('o_topic_subscriptions', $pun_config))
			$db->query('UPDATE '.$db->prefix.'config SET conf_name=\'o_topic_subscriptions\' WHERE conf_name=\'o_subscriptions\'') or error('Unable to rename config value \'o_subscriptions\'', __FILE__, __LINE__, $db->error());

		// Change the default style if the old doesn't exist anymore
		if ($pun_config['o_default_style'] != $default_style)
			$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.$db->escape($default_style).'\' WHERE conf_name = \'o_default_style\'') or error('Unable to update default style config', __FILE__, __LINE__, $db->error());

		// For MySQL(i) without InnoDB, change the engine of the online table (for performance reasons)
		if ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mardiadb')
			$db->query('ALTER TABLE '.$db->prefix.'online ENGINE = MyISAM') or error('Unable to change engine type of online table to MyISAM', __FILE__, __LINE__, $db->error());

		break;

	// Handle any duplicate users which occured due to conversion
	case 'conv_users_dupe':
		$query_str = '?stage=preparse_posts';

		if (!$mysql || !$mariadb || empty($_SESSION['dupe_users']))
			break;

		if (isset($_POST['form_sent']))
		{
			$errors = array();

			require FORUM_ROOT.'include/email.php';

			foreach ($_SESSION['dupe_users'] as $id => $cur_user)
			{
				$errors[$id] = array();

				$username = pun_trim($_POST['dupe_users'][$id]);

				if (pun_strlen($username) < 2)
					$errors[$id][] = $lang_update['Username too short error'];
				else if (pun_strlen($username) > 25) // This usually doesn't happen since the form element only accepts 25 characters
					$errors[$id][] = $lang_update['Username too long error'];
				else if (!strcasecmp($username, 'Guest'))
					$errors[$id][] = $lang_update['Username Guest reserved error'];
				else if (preg_match('%[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}%', $username) || preg_match('%((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))%', $username))
					$errors[$id][] = $lang_update['Username IP format error'];
				else if ((strpos($username, '[') !== false || strpos($username, ']') !== false) && strpos($username, '\'') !== false && strpos($username, '"') !== false)
					$errors[$id][] = $lang_update['Username bad characters error'];
				else if (preg_match('%(?:\[/?(?:b|u|s|ins|del|em|i|h|colou?r|quote|code|img|url|email|list|\*)\]|\[(?:img|url|quote|list)=)%i', $username))
					$errors[$id][] = $lang_update['Username BBCode error'];

				$result = $db->query('SELECT username FROM '.$db->prefix.'users WHERE (UPPER(username)=UPPER(\''.$db->escape($username).'\') OR UPPER(username)=UPPER(\''.$db->escape(ucp_preg_replace('%[^\p{L}\p{N}]%u', '', $username)).'\')) AND id>1') or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());

				if ($db->num_rows($result))
				{
					$busy = $db->result($result);
					$errors[$id][] = sprintf($lang_update['Username duplicate error'], pun_htmlspecialchars($busy));
				}

				if (empty($errors[$id]))
				{
					$old_username = $cur_user['username'];
					$_SESSION['dupe_users'][$id]['username'] = $cur_user['username'] = $username;

					$temp = array();
					foreach ($cur_user as $idx => $value)
						$temp[$idx] = is_null($value) ? 'NULL' : '\''.$db->escape($value).'\'';

					// Insert the renamed user
					$db->query('INSERT INTO '.$db->prefix.'users('.implode(',', array_keys($temp)).') VALUES ('.implode(',', array_values($temp)).')') or error('Unable to insert data to new table', __FILE__, __LINE__, $db->error());

					// Renaming a user also affects a bunch of other stuff, lets fix that too...
					$db->query('UPDATE '.$db->prefix.'posts SET poster=\''.$db->escape($username).'\' WHERE poster_id='.$id) or error('Unable to update posts', __FILE__, __LINE__, $db->error());

					// TODO: The following must compare using collation utf8_bin otherwise we will accidently update posts/topics/etc belonging to both of the duplicate users, not just the one we renamed!
					$db->query('UPDATE '.$db->prefix.'posts SET edited_by=\''.$db->escape($username).'\' WHERE edited_by=\''.$db->escape($old_username).'\' COLLATE utf8_bin') or error('Unable to update posts', __FILE__, __LINE__, $db->error());
					$db->query('UPDATE '.$db->prefix.'topics SET poster=\''.$db->escape($username).'\' WHERE poster=\''.$db->escape($old_username).'\' COLLATE utf8_bin') or error('Unable to update topics', __FILE__, __LINE__, $db->error());
					$db->query('UPDATE '.$db->prefix.'topics SET last_poster=\''.$db->escape($username).'\' WHERE last_poster=\''.$db->escape($old_username).'\' COLLATE utf8_bin') or error('Unable to update topics', __FILE__, __LINE__, $db->error());
					$db->query('UPDATE '.$db->prefix.'forums SET last_poster=\''.$db->escape($username).'\' WHERE last_poster=\''.$db->escape($old_username).'\' COLLATE utf8_bin') or error('Unable to update forums', __FILE__, __LINE__, $db->error());
					$db->query('UPDATE '.$db->prefix.'online SET ident=\''.$db->escape($username).'\' WHERE ident=\''.$db->escape($old_username).'\' COLLATE utf8_bin') or error('Unable to update online list', __FILE__, __LINE__, $db->error());

					// If the user is a moderator or an administrator we have to update the moderator lists
					$result = $db->query('SELECT g_moderator FROM '.$db->prefix.'groups WHERE g_id='.$cur_user['group_id']) or error('Unable to fetch group', __FILE__, __LINE__, $db->error());
					$group_mod = $db->result($result);

					if ($cur_user['group_id'] == FORUM_ADMIN || $group_mod == '1')
					{
						$result = $db->query('SELECT id, moderators FROM '.$db->prefix.'forums') or error('Unable to fetch forum list', __FILE__, __LINE__, $db->error());

						while ($cur_forum = $db->fetch_assoc($result))
						{
							$cur_moderators = ($cur_forum['moderators'] != '') ? unserialize($cur_forum['moderators']) : array();

							if (in_array($id, $cur_moderators))
							{
								unset($cur_moderators[$old_username]);
								$cur_moderators[$username] = $id;
								uksort($cur_moderators, 'utf8_strcasecmp');

								$db->query('UPDATE '.$db->prefix.'forums SET moderators=\''.$db->escape(serialize($cur_moderators)).'\' WHERE id='.$cur_forum['id']) or error('Unable to update forum', __FILE__, __LINE__, $db->error());
							}
						}
					}

					// Email the user alerting them of the change
					if (file_exists(FORUM_ROOT.'lang/'.$cur_user['language'].'/mail_templates/rename.tpl'))
						$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/'.$cur_user['language'].'/mail_templates/rename.tpl'));
					else if (file_exists(FORUM_ROOT.'lang/'.$pun_config['o_default_lang'].'/mail_templates/rename.tpl'))
						$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/'.$pun_config['o_default_lang'].'/mail_templates/rename.tpl'));
					else
						$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/English/mail_templates/rename.tpl'));

					// The first row contains the subject
					$first_crlf = strpos($mail_tpl, "\n");
					$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
					$mail_message = trim(substr($mail_tpl, $first_crlf));

					$mail_subject = str_replace('<board_title>', $pun_config['o_board_title'], $mail_subject);
					$mail_message = str_replace('<base_url>', get_base_url().'/', $mail_message);
					$mail_message = str_replace('<old_username>', $old_username, $mail_message);
					$mail_message = str_replace('<new_username>', $username, $mail_message);
					$mail_message = str_replace('<board_mailer>', $pun_config['o_board_title'], $mail_message);

					pun_mail($cur_user['email'], $mail_subject, $mail_message);

					unset($_SESSION['dupe_users'][$id]);
				}
			}
		}

		if (!empty($_SESSION['dupe_users']))
		{
			$query_str = '';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang_common['lang_identifier'] ?>" lang="<?php echo $lang_common['lang_identifier'] ?>" dir="<?php echo $lang_common['lang_direction'] ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $lang_update['Update'] ?></title>
<link rel="stylesheet" type="text/css" href="style/<?php echo $default_style ?>.css" />
</head>
<body>

<div id="pundb_update" class="pun">
<div class="top-box"><div><!-- Top Corners --></div></div>
<div class="punwrap">

<div class="blockform">
	<h2><span><?php echo $lang_update['Error converting users'] ?></span></h2>
	<div class="box">
		<form method="post" action="db_update.php?stage=conv_users_dupe&amp;uid=<?php echo $uid ?>">
			<input type="hidden" name="form_sent" value="1" />
			<div class="inform">
				<div class="forminfo">
						<p style="font-size: 1.1em"><?php echo $lang_update['Error info 1'] ?></p>
						<p style="font-size: 1.1em"><?php echo $lang_update['Error info 2'] ?></p>
				</div>
			</div>
<?php

			foreach ($_SESSION['dupe_users'] as $id => $cur_user)
			{

?>
			<div class="inform">
				<fieldset>
					<legend><?php echo pun_htmlspecialchars($cur_user['username']); ?></legend>
					<div class="infldset">
						<label class="required"><strong><?php echo $lang_update['New username'] ?> <span><?php echo $lang_update['Required'] ?></span></strong><br /><input type="text" name="<?php echo 'dupe_users['.$id.']'; ?>" value="<?php if (isset($_POST['dupe_users'][$id])) echo pun_htmlspecialchars($_POST['dupe_users'][$id]); ?>" size="25" maxlength="25" /><br /></label>
					</div>
				</fieldset>
<?php if (!empty($errors[$id])): ?>				<div class="forminfo error-info">
					<h3><?php echo $lang_update['Correct errors'] ?></h3>
					<ul class="error-list">
<?php

foreach ($errors[$id] as $cur_error)
	echo "\t\t\t\t\t\t".'<li><strong>'.$cur_error.'</strong></li>'."\n";
?>
					</ul>
				</div>
<?php endif; ?>			</div>
<?php

			}

?>
			<p class="buttons"><input type="submit" name="rename" value="<?php echo $lang_update['Rename users'] ?>" /></p>
		</form>
	</div>
</div>

</div>
<div class="end-box"><div><!-- Bottom Corners --></div></div>
</div>

</body>
</html>
<?php

		}

		break;


	// Preparse posts
	case 'preparse_posts':
		$query_str = '?stage=preparse_sigs';

		// If we don't need to parse the posts, skip this stage
		if (isset($pun_config['o_parser_revision']) && $pun_config['o_parser_revision'] >= UPDATE_TO_PARSER_REVISION)
			break;

		require FORUM_ROOT.'include/parser.php';

		// Fetch posts to process this cycle
		$result = $db->query('SELECT id, message FROM '.$db->prefix.'posts WHERE id > '.$start_at.' ORDER BY id ASC LIMIT '.PER_PAGE) or error('Unable to fetch posts', __FILE__, __LINE__, $db->error());

		$temp = array();
		$end_at = 0;
		while ($cur_item = $db->fetch_assoc($result))
		{
			echo sprintf($lang_update['Preparsing item'], $lang_update['post'], $cur_item['id']).'<br />'."\n";
			$db->query('UPDATE '.$db->prefix.'posts SET message = \''.$db->escape(preparse_bbcode($cur_item['message'], $temp)).'\' WHERE id = '.$cur_item['id']) or error('Unable to update post', __FILE__, __LINE__, $db->error());

			$end_at = $cur_item['id'];
		}

		// Check if there is more work to do
		if ($end_at > 0)
		{
			$result = $db->query('SELECT 1 FROM '.$db->prefix.'posts WHERE id > '.$end_at.' ORDER BY id ASC LIMIT 1') or error('Unable to fetch next ID', __FILE__, __LINE__, $db->error());

			if ($db->num_rows($result) > 0)
				$query_str = '?stage=preparse_posts&start_at='.$end_at;
		}

		break;


	// Preparse signatures
	case 'preparse_sigs':
		$query_str = '?stage=rebuild_idx';

		// If we don't need to parse the sigs, skip this stage
		if (isset($pun_config['o_parser_revision']) && $pun_config['o_parser_revision'] >= UPDATE_TO_PARSER_REVISION)
			break;

		require FORUM_ROOT.'include/parser.php';

		// Fetch users to process this cycle
		$result = $db->query('SELECT id, signature FROM '.$db->prefix.'users WHERE id > '.$start_at.' ORDER BY id ASC LIMIT '.PER_PAGE) or error('Unable to fetch users', __FILE__, __LINE__, $db->error());

		$temp = array();
		$end_at = 0;
		while ($cur_item = $db->fetch_assoc($result))
		{
			echo sprintf($lang_update['Preparsing item'], $lang_update['signature'], $cur_item['id']).'<br />'."\n";
			$db->query('UPDATE '.$db->prefix.'users SET signature = \''.$db->escape(preparse_bbcode($cur_item['signature'], $temp, true)).'\' WHERE id = '.$cur_item['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());

			$end_at = $cur_item['id'];
		}

		// Check if there is more work to do
		if ($end_at > 0)
		{
			$result = $db->query('SELECT 1 FROM '.$db->prefix.'users WHERE id > '.$end_at.' ORDER BY id ASC LIMIT 1') or error('Unable to fetch next ID', __FILE__, __LINE__, $db->error());
			if ($db->num_rows($result) > 0)
				$query_str = '?stage=preparse_sigs&start_at='.$end_at;
		}

		break;


	// Rebuild the search index
	case 'rebuild_idx':
		$query_str = '?stage=finish';

		// If we don't need to update the search index, skip this stage
		if (isset($pun_config['o_searchindex_revision']) && $pun_config['o_searchindex_revision'] >= UPDATE_TO_SI_REVISION)
			break;

		if ($start_at == 0)
		{
			// Truncate the tables just in-case we didn't already (if we are coming directly here without converting the tables)
			$db->truncate_table('search_cache') or error('Unable to empty search cache table', __FILE__, __LINE__, $db->error());
			$db->truncate_table('search_matches') or error('Unable to empty search index match table', __FILE__, __LINE__, $db->error());
			$db->truncate_table('search_words') or error('Unable to empty search index words table', __FILE__, __LINE__, $db->error());

			// Reset the sequence for the search words (not needed for SQLite)
			switch ($db_type)
			{
				case 'mysql':
				case 'mysqli':
				case 'mysql_innodb':
				case 'mysqli_innodb':
				case 'mariadb':
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
		while ($cur_item = $db->fetch_assoc($result))
		{
			echo sprintf($lang_update['Rebuilding index item'], $lang_update['post'], $cur_item['id']).'<br />'."\n";

			if ($cur_item['id'] == $cur_item['first_post_id'])
				update_search_index('post', $cur_item['id'], $cur_item['message'], $cur_item['subject']);
			else
				update_search_index('post', $cur_item['id'], $cur_item['message']);

			$end_at = $cur_item['id'];
		}

		// Check if there is more work to do
		if ($end_at > 0)
		{
			$result = $db->query('SELECT 1 FROM '.$db->prefix.'posts WHERE id > '.$end_at.' ORDER BY id ASC LIMIT 1') or error('Unable to fetch next ID', __FILE__, __LINE__, $db->error());

			if ($db->num_rows($result) > 0)
				$query_str = '?stage=rebuild_idx&start_at='.$end_at;
		}

		break;


	// Show results page
	case 'finish':
		// We update the version number
		$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.UPDATE_TO.'\' WHERE conf_name = \'o_cur_version\'') or error('Unable to update version', __FILE__, __LINE__, $db->error());

		// And the database revision number
		$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.UPDATE_TO_DB_REVISION.'\' WHERE conf_name = \'o_database_revision\'') or error('Unable to update database revision number', __FILE__, __LINE__, $db->error());

		// And the search index revision number
		$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.UPDATE_TO_SI_REVISION.'\' WHERE conf_name = \'o_searchindex_revision\'') or error('Unable to update search index revision number', __FILE__, __LINE__, $db->error());

		// And the parser revision number
		$db->query('UPDATE '.$db->prefix.'config SET conf_value = \''.UPDATE_TO_PARSER_REVISION.'\' WHERE conf_name = \'o_parser_revision\'') or error('Unable to update parser revision number', __FILE__, __LINE__, $db->error());

		// Check the default language still exists!
		if (!file_exists(FORUM_ROOT.'lang/'.$pun_config['o_default_lang'].'/common.php'))
			$db->query('UPDATE '.$db->prefix.'config SET conf_value = \'English\' WHERE conf_name = \'o_default_lang\'') or error('Unable to update default language', __FILE__, __LINE__, $db->error());

		// Check the default style still exists!
		if (!file_exists(FORUM_ROOT.'style/'.$pun_config['o_default_style'].'.css'))
			$db->query('UPDATE '.$db->prefix.'config SET conf_value = \'Air\' WHERE conf_name = \'o_default_style\'') or error('Unable to update default style', __FILE__, __LINE__, $db->error());

		// This feels like a good time to synchronize the forums
		$result = $db->query('SELECT id FROM '.$db->prefix.'forums') or error('Unable to fetch forum IDs', __FILE__, __LINE__, $db->error());

		while ($row = $db->fetch_row($result))
			update_forum($row[0]);

		// Empty the PHP cache
		forum_clear_cache();

		// Delete the update lock file
		@unlink(FORUM_CACHE_DIR.'db_update.lock');

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title><?php echo $lang_update['Update'] ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex, nofollow">
        <link href="include/bootstrap/bootstrap.css" type="text/css" rel="stylesheet">
        <link href="style/Randomness.css" type="text/css" rel="stylesheet">
        <style type="text/css">
		body {
            padding-bottom: 40px;
			padding-top: 60px;
            background-color: #f2f2f2;
        }
        .form-signin {
            max-width: 350px;
            padding: 19px 29px 9px 29px;
            margin: 0 auto 20px;
            background-color: #fff;
			border-left: #2ca0e9 5px solid;
			box-shadow: 0 7px 7px 0px rgba(221,221,221,0.37);
        }
		h1 {
			font-weight: 100;
		}
        .form-pass {
            padding: 19px 29px 4px;
        }
        .form-signin .form-signin-heading {
            margin-bottom: 10px;
        }
        .form-signin input[type="password"] {
            font-size: 16px;
            height: auto;
            margin-bottom: 5px;
            padding: 7px 9px;
        }
		.form-signin-heading {
			color: #2ca0e9;
			text-transform: lowercase;
			margin-top: 0;
		}
		.btn {
			margin-bottom: 20px;
		}
        </style>
	</head>
	<body>
        <div class="form-signin">
            <h1 class="form-signin-heading"><?php echo $lang_update['Update'] ?></h1>
            <p><?php printf($lang_update['Successfully updated'], sprintf('<a href="index.php">%s</a>', $lang_update['go to index'])) ?></p>
		</div>
	</body>
</html>
<?php

		break;
}

$db->end_transaction();
$db->close();

if ($query_str != '')
	exit('<script type="text/javascript">window.location="db_update.php'.$query_str.'&uid='.$uid.'"</script><noscript><meta http-equiv="refresh" content="0;url=db_update.php'.$query_str.'&uid='.$uid.'" /></noscript>');
