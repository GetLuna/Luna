<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

class Installer {
	
	const DEFAULT_LANG = 'English';
	const DEFAULT_STYLE = 'Fifteen';
	
	public static function is_supported_php_version() {
		return function_exists('version_compare') && version_compare(PHP_VERSION, Version::MIN_PHP_VERSION, '>=');
	}
	
	public static function guess_base_url() {
		// Make an educated guess regarding base_url
		$base_url  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';	// protocol
		$base_url .= preg_replace('%:(80|443)$%', '', $_SERVER['HTTP_HOST']);							// host[:port]
		$base_url .= str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));							// path
		
		return $base_url;
	}
	
	public static function determine_database_extensions() {
		// Determine available database extensions
		$dual_mysql = false;
		$db_extensions = array();
		$mysql_innodb = false;

		if (function_exists('mysqli_connect')) {
			$db_extensions[] = array('mysqli', 'MySQL Improved');
			$db_extensions[] = array('mysqli_innodb', 'MySQL Improved (InnoDB)');
			$mysql_innodb = true;
		}

		if (function_exists('mysql_connect')) {
			$db_extensions[] = array('mysql', 'MySQL Standard');
			$db_extensions[] = array('mysql_innodb', 'MySQL Standard (InnoDB)');
			$mysql_innodb = true;
	
			if (count($db_extensions) > 2)
				$dual_mysql = true;
		}

		if (function_exists('sqlite_open'))
			$db_extensions[] = array('sqlite', 'SQLite 2');

		if (class_exists('SQLite3'))
			$db_extensions[] = array('sqlite3', 'SQLite 3');

		if (function_exists('pg_connect'))
			$db_extensions[] = array('pgsql', 'PostgreSQL');
			
		return $db_extensions;
	}
	
	public static function generate_config_file($db_type, $db_host, $db_name, $db_username, $db_password, $db_prefix = '', $cookie_name = false, $cookie_seed = false) {
		if ($cookie_name === false)
		$cookie_name = 'luna_cookie_'.random_key(6, false, true);
		
		if ($cookie_seed === false)
		$cookie_seed = random_key(16, false, true);
		
		return '<?php'."\n\n".'$db_type = \''.$db_type."';\n".'$db_host = \''.$db_host."';\n".'$db_name = \''.addslashes($db_name)."';\n".'$db_username = \''.addslashes($db_username)."';\n".'$db_password = \''.addslashes($db_password)."';\n".'$db_prefix = \''.addslashes($db_prefix)."';\n".'$p_connect = false;'."\n\n".'$cookie_name = '."'".$cookie_name."';\n".'$cookie_domain = '."'';\n".'$cookie_path = '."'/';\n".'$cookie_secure = 0;'."\n".'$cookie_seed = \''.$cookie_seed."';\n\ndefine('PUN', 1);\n";
	}
	
	public static function validate_config($username, $password1, $password2, $email, $title, $default_lang, $default_style) {

		$alerts = array();

		// Validate username and passwords
		if (luna_strlen($username) < 2)
			$alerts[] = __('Usernames must be at least 2 characters long.', 'luna');
		elseif (luna_strlen($username) > 25) // This usually doesn't happen since the form element only accepts 25 characters
			$alerts[] = __('Usernames must not be more than 25 characters long.', 'luna');
		elseif (!strcasecmp($username, 'Guest'))
			$alerts[] = __('The username guest is reserved.', 'luna');
		elseif (preg_match('%[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}%', $username) || preg_match('%((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))%', $username))
			$alerts[] = __('Usernames may not be in the form of an IP address.', 'luna');
		elseif ((strpos($username, '[') !== false || strpos($username, ']') !== false) && strpos($username, '\'') !== false && strpos($username, '"') !== false)
			$alerts[] = __('Usernames may not contain all the characters \', " and [ or ] at once.', 'luna');
		elseif (preg_match('%(?:\[/?(?:b|u|i|h|colou?r|quote|code|img|url|email|list)\]|\[(?:code|quote|list)=)%i', $username))
			$alerts[] = __('Usernames may not contain any of the text formatting tags (BBCode) that the forum uses.', 'luna');
	
		if (luna_strlen($password1) < 4)
			$alerts[] = __('Passwords must be at least 6 characters long.', 'luna');
		elseif ($password1 != $password2)
			$alerts[] = __('Passwords do not match.', 'luna');
	
		// Validate email
		require LUNA_ROOT.'include/email.php';
	
		if (!is_valid_email($email))
			$alerts[] = __('The administrator email address you entered is invalid.', 'luna');
	
		if ($title == '')
			$alerts[] = __('You must enter a board title.', 'luna');
	
		$languages = forum_list_langs();
		if (!in_array($default_lang, $languages))
			$alerts[] = __('The default language chosen doesn\'t seem to exist.', 'luna');
	
		$styles = forum_list_styles();
		if (!in_array($default_style, $styles))
			$alerts[] = __('The default style chosen doesn\'t seem to exist.', 'luna');
			
		return $alerts;
	}
	
	private static function load_database_driver($db_type) {
		
		// Load the appropriate DB layer class
		switch ($db_type) {
			case 'mysql':
				require LUNA_ROOT.'include/dblayer/mysql.php';
				break;
	
			case 'mysql_innodb':
				require LUNA_ROOT.'include/dblayer/mysql_innodb.php';
				break;
	
			case 'mysqli':
				require LUNA_ROOT.'include/dblayer/mysqli.php';
				break;
	
			case 'mysqli_innodb':
				require LUNA_ROOT.'include/dblayer/mysqli_innodb.php';
				break;
	
			case 'pgsql':
				require FORUM_ROOT.'include/dblayer/pgsql.php';
				break;
	
			case 'sqlite':
				require LUNA_ROOT.'include/dblayer/sqlite.php';
				break;

			case 'sqlite3':
				require LUNA_ROOT.'include/dblayer/sqlite3.php';
				break;
	
			default:
				error(sprintf(__('"%s" is not a valid database type', 'luna'), luna_htmlspecialchars($db_type)));
		}
	}
	
	private static function validate_database_version($db_type, $db) {
		global $db_prefix;
		
		// Do some DB type specific checks
		switch ($db_type) {
			case 'mysql':
			case 'mysqli':
			case 'mysql_innodb':
			case 'mysqli_innodb':
				$mysql_info = $db->get_version();
				if (version_compare($mysql_info['version'], Version::MIN_MYSQL_VERSION, '<'))
					error(sprintf(__('You are running %1$s version %2$s. Luna %3$s requires at least %1$s %4$s to run properly. You must upgrade your %1$s installation before you can continue.', 'luna'), 'MySQL', $mysql_info['version'], Version::LUNA_VERSION, Version::MIN_MYSQL_VERSION));
				break;
	
			case 'pgsql':
				$pgsql_info = $db->get_version();
				if (version_compare($pgsql_info['version'], Version::MIN_PGSQL_VERSION, '<'))
					error(sprintf(__('You are running %1$s version %2$s. Luna %3$s requires at least %1$s %4$s to run properly. You must upgrade your %1$s installation before you can continue.', 'luna'), 'PostgreSQL', $pgsql_info['version'], Version::LUNA_VERSION, Version::MIN_PGSQL_VERSION));
				break;
	
			case 'sqlite':
			case 'sqlite3':
				if (strtolower($db_prefix) == 'sqlite_')
					error(__('The table prefix \'sqlite_\' is reserved for use by the SQLite engine. Please choose a different prefix', 'luna'));
				break;
		}

		// Check if InnoDB is available
		if ($db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb') {
			$result = $db->query('SHOW VARIABLES LIKE \'have_innodb\'');
			list (, $result) = $db->fetch_row($result);
			if ((strtoupper($result) != 'YES'))
				error(__('InnoDB does not seem to be enabled. Please choose a database layer that does not have InnoDB support, or enable InnoDB on your MySQL server', 'luna'));
		}
	}
	
	public static function create_database($db_type, $db_host, $db_name, $db_username, $db_password, $db_prefix, $title, $description, $default_lang, $default_style, $email, $avatars, $base_url) {

		// Validate prefix
		if (strlen($db_prefix) > 0 && (!preg_match('%^[a-zA-Z_][a-zA-Z0-9_]*$%', $db_prefix) || strlen($db_prefix) > 40))
		error(sprintf(__('The table prefix \'%s\' contains illegal characters or is too long. The prefix may contain the letters a to z, any numbers and the underscore character. They must however not start with a number. The maximum length is 40 characters. Please choose a different prefix', 'luna'), $db->prefix));
		
		// Load the appropriate DB layer class
		Installer::load_database_driver($db_type);
		
		// Create the database object (and connect/select db)
		$db = new DBLayer($db_host, $db_username, $db_password, $db_name, $db_prefix, false);
		
		// Do some DB type specific checks
		Installer::validate_database_version($db_type, $db);
		
		// Make sure FluxBB isn't already installed
		$result = $db->query('SELECT 1 FROM '.$db->prefix.'users WHERE id=1');
		if ($db->num_rows($result))
		error(sprintf(__('A table called "%susers" is already present in the database "%s". This could mean that Luna is already installed or that another piece of software is installed and is occupying one or more of the table names Luna requires. If you want to install multiple copies of Luna in the same database, you must choose a different table prefix', 'luna'), $db->prefix, $db_name));
		
		// Start a transaction
		$db->start_transaction();
		
		// Create all tables
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'username'		=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> true
				),
				'ip'			=> array(
					'datatype'		=> 'VARCHAR(255)',
					'allow_null'	=> true
				),
				'email'			=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> true
				),
				'message'		=> array(
					'datatype'		=> 'VARCHAR(255)',
					'allow_null'	=> true
				),
				'expire'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'ban_creator'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('id'),
			'INDEXES'		=> array(
				'username_idx'	=> array('username')
			)
		);
	
		if ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb')
			$schema['INDEXES']['username_idx'] = array('username(25)');
	
		$db->create_table('bans', $schema) or error('Unable to create bans table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'cat_name'		=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> false,
					'default'		=> '\'New Category\''
				),
				'disp_position'	=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('id')
		);
	
		$db->create_table('categories', $schema) or error('Unable to create categories table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'search_for'	=> array(
					'datatype'		=> 'VARCHAR(60)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'replace_with'	=> array(
					'datatype'		=> 'VARCHAR(60)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				)
			),
			'PRIMARY KEY'	=> array('id')
		);
	
		$db->create_table('censoring', $schema) or error('Unable to create censoring table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'conf_name'		=> array(
					'datatype'		=> 'VARCHAR(255)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'conf_value'	=> array(
					'datatype'		=> 'TEXT',
					'allow_null'	=> true
				)
			),
			'PRIMARY KEY'	=> array('conf_name')
		);
	
		$db->create_table('config', $schema) or error('Unable to create config table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'group_id'		=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'forum_id'		=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'read_forum'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'comment'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'create_threads'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				)
			),
			'PRIMARY KEY'	=> array('group_id', 'forum_id')
		);
	
		$db->create_table('forum_perms', $schema) or error('Unable to create forum_perms table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'forum_name'	=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> false,
					'default'		=> '\'New forum\''
				),
				'forum_desc'	=> array(
					'datatype'		=> 'TEXT',
					'allow_null'	=> true
				),
				'moderators'	=> array(
					'datatype'		=> 'TEXT',
					'allow_null'	=> true
				),
				'num_threads'	=> array(
					'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'num_comments'		=> array(
					'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_comment'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'last_comment_id'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'last_commenter_id'=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> true,
					'default'		=> NULL,
				),
				'sort_by'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'disp_position'	=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'cat_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'color'			=> array(
					'datatype'		=> 'VARCHAR(25)',
					'allow_null'	=> false,
					'default'		=> '\'#2788cb\''
				),
				'parent_id'		=> array(
					'datatype'		=> 'INT',
					'allow_null'	=> true,
					'default'		=> 0
				),
				'solved'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'icon'		=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> true,
					'default'		=> NULL
				)
			),
			'PRIMARY KEY'	=> array('id')
		);
	
		$db->create_table('forums', $schema) or error('Unable to create forums table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'g_id'						=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'g_title'					=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'g_user_title'				=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> true
				),
				'g_moderator'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'g_mod_edit_users'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'g_mod_rename_users'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'g_mod_change_passwords'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'g_mod_ban_users'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'g_read_board'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_view_users'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_comment'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_create_threads'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_edit_comments'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_delete_comments'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_delete_threads'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_set_title'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_search'					=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_search_users'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_send_email'				=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_comment_flood'				=> array(
					'datatype'		=> 'SMALLINT(6)',
					'allow_null'	=> false,
					'default'		=> '30'
				),
				'g_search_flood'			=> array(
					'datatype'		=> 'SMALLINT(6)',
					'allow_null'	=> false,
					'default'		=> '30'
				),
				'g_email_flood'				=> array(
					'datatype'		=> 'SMALLINT(6)',
					'allow_null'	=> false,
					'default'		=> '60'
				),
				'g_inbox'						=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_inbox_limit'				=> array(
					'datatype'		=> 'INT',
					'allow_null'	=> false,
					'default'		=> '20'
				),
				'g_report_flood'			=> array(
					'datatype'		=> 'SMALLINT(6)',
					'allow_null'	=> false,
					'default'		=> '60'
				),
				'g_soft_delete_view'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_soft_delete_comments'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'g_soft_delete_threads'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				)
			),
			'PRIMARY KEY'	=> array('g_id')
		);
		
		$db->create_table('groups', $schema) or error('Unable to create groups table', __FILE__, __LINE__, $db->error());
	
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
				'visible'	=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'sys_entry'		=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> true,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('id')
		);
	
		$db->create_table('menu', $schema) or error('Unable to create menu table', __FILE__, __LINE__, $db->error());
	
	
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
	
	
		$schema = array(
			'FIELDS'		=> array(
				'user_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'ident'			=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'logged'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'idle'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_comment'			=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'last_search'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
			),
	
			'UNIQUE KEYS'	=> array(
				'user_id_ident_idx'	=> array('user_id', 'ident')
			),
			'INDEXES'		=> array(
				'ident_idx'		=> array('ident'),
				'logged_idx'	=> array('logged')
			)
		);
	
		if ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb') {
			$schema['UNIQUE KEYS']['user_id_ident_idx'] = array('user_id', 'ident(25)');
			$schema['INDEXES']['ident_idx'] = array('ident(25)');
		}
	
		if ($db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb')
			$schema['ENGINE'] = 'InnoDB';
	
		$db->create_table('online', $schema) or error('Unable to create online table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'commenter'		=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'commenter_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'commenter_ip'		=> array(
					'datatype'		=> 'VARCHAR(39)',
					'allow_null'	=> true
				),
				'commenter_email'	=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> true
				),
				'message'		=> array(
					'datatype'		=> 'MEDIUMTEXT',
					'allow_null'	=> true
				),
				'hide_smilies'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'commented'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'edited'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'edited_by'		=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> true
				),
				'thread_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'marked'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'soft'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('id'),
			'INDEXES'		=> array(
				'thread_id_idx'	=> array('thread_id'),
				'multi_idx'		=> array('commenter_id', 'thread_id')
			)
		);
	
		$db->create_table('comments', $schema) or error('Unable to create comments table', __FILE__, __LINE__, $db->error());
	
	
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
				'min_comments'		=> array(
					'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('id')
		);
	
		$db->create_table('ranks', $schema) or error('Unable to create ranks table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'comment_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'thread_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'forum_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'reported_by'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'created'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'message'		=> array(
					'datatype'		=> 'TEXT',
					'allow_null'	=> true
				),
				'zapped'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'zapped_by'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				)
			),
			'PRIMARY KEY'	=> array('id'),
			'INDEXES'		=> array(
				'zapped_idx'	=> array('zapped')
			)
		);
	
		$db->create_table('reports', $schema) or error('Unable to create reports table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'ident'			=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'search_data'	=> array(
					'datatype'		=> 'MEDIUMTEXT',
					'allow_null'	=> true
				)
			),
			'PRIMARY KEY'	=> array('id'),
			'INDEXES'		=> array(
				'ident_idx'	=> array('ident')
			)
		);
	
		if ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb')
			$schema['INDEXES']['ident_idx'] = array('ident(8)');
	
		$db->create_table('search_cache', $schema) or error('Unable to create search_cache table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'comment_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'word_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'subject_match'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'INDEXES'		=> array(
				'word_id_idx'	=> array('word_id'),
				'comment_id_idx'	=> array('comment_id')
			)
		);
	
		$db->create_table('search_matches', $schema) or error('Unable to create search_matches table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'word'			=> array(
					'datatype'		=> 'VARCHAR(20)',
					'allow_null'	=> false,
					'default'		=> '\'\'',
					'collation'		=> 'bin'
				)
			),
			'PRIMARY KEY'	=> array('word'),
			'INDEXES'		=> array(
				'id_idx'	=> array('id')
			)
		);
	
		if ($db_type == 'sqlite' || $db_type == 'sqlite3') {
			$schema['PRIMARY KEY'] = array('id');
			$schema['UNIQUE KEYS'] = array('word_idx'	=> array('word'));
		}
	
		$db->create_table('search_words', $schema) or error('Unable to create search_words table', __FILE__, __LINE__, $db->error());
	
	
		$schema = array(
			'FIELDS'		=> array(
				'user_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'thread_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('user_id', 'thread_id')
		);
	
		$db->create_table('thread_subscriptions', $schema) or error('Unable to create thread subscriptions table', __FILE__, __LINE__, $db->error());
	
	
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
	
	
		$schema = array(
			'FIELDS'		=> array(
				'id'			=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'commenter'		=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'subject'		=> array(
					'datatype'		=> 'VARCHAR(255)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'commented'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'first_comment_id'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_comment'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_comment_id'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_commenter'	=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> true
				),
				'num_views'		=> array(
					'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'num_replies'	=> array(
					'datatype'		=> 'MEDIUMINT(8) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_commenter_id'=> array(
					'datatype'		=> 'INT(10)',
					'allow_null'	=> true,
					'default'		=> NULL,
				),
				'closed'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'pinned'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'important'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'moved_to'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'forum_id'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'soft'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'solved'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true,
					'default'		=> NULL
				)
			),
			'PRIMARY KEY'	=> array('id'),
			'INDEXES'		=> array(
				'forum_id_idx'		=> array('forum_id'),
				'moved_to_idx'		=> array('moved_to'),
				'last_comment_idx'		=> array('last_comment'),
				'last_commenter_id'	=> array('last_commenter'),
				'first_comment_id_idx'	=> array('first_comment_id')
			)
		);
	
		$db->create_table('threads', $schema) or error('Unable to create threads table', __FILE__, __LINE__, $db->error());
	
		$schema = array(
			'FIELDS'		=> array(
				'id'				=> array(
					'datatype'		=> 'SERIAL',
					'allow_null'	=> false
				),
				'group_id'			=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '3'
				),
				'username'			=> array(
					'datatype'		=> 'VARCHAR(200)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'password'			=> array(
					'datatype'		=> 'VARCHAR(256)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'email'				=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> false,
					'default'		=> '\'\''
				),
				'title'				=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> true
				),
				'realname'			=> array(
					'datatype'		=> 'VARCHAR(40)',
					'allow_null'	=> true
				),
				'url'				=> array(
					'datatype'		=> 'VARCHAR(100)',
					'allow_null'	=> true
				),
				'facebook'			=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> true
				),
				'msn'				=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> true
				),
				'twitter'			=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> true
				),
				'google'			=> array(
					'datatype'		=> 'VARCHAR(50)',
					'allow_null'	=> true
				),
				'location'			=> array(
					'datatype'		=> 'VARCHAR(30)',
					'allow_null'	=> true
				),
				'signature'			=> array(
					'datatype'		=> 'TEXT',
					'allow_null'	=> true
				),
				'disp_threads'		=> array(
					'datatype'		=> 'TINYINT(3) UNSIGNED',
					'allow_null'	=> true
				),
				'disp_comments'		=> array(
					'datatype'		=> 'TINYINT(3) UNSIGNED',
					'allow_null'	=> true
				),
				'email_setting'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'notify_with_comment'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'auto_notify'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'show_smilies'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'show_img'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'show_img_sig'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'show_avatars'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'show_sig'			=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'php_timezone'		=> array(
					'datatype'		=> 'VARCHAR(100)',
					'allow_null'	=> false,
					'default'		=> '\'UTC\''
				),
				'time_format'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'date_format'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'language'			=> array(
					'datatype'		=> 'VARCHAR(25)',
					'allow_null'	=> false,
					'default'		=> '\''.$db->escape($default_lang).'\''
				),
				'style'				=> array(
					'datatype'		=> 'VARCHAR(25)',
					'allow_null'	=> false,
					'default'		=> '\''.$db->escape($default_style).'\''
				),
				'num_comments'			=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'last_comment'			=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'last_search'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'last_email_sent'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'last_report_sent'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> true
				),
				'registered'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'registration_ip'	=> array(
					'datatype'		=> 'VARCHAR(39)',
					'allow_null'	=> false,
					'default'		=> '\'0.0.0.0\''
				),
				'last_visit'		=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'admin_note'		=> array(
					'datatype'		=> 'VARCHAR(30)',
					'allow_null'	=> true
				),
				'activate_string'	=> array(
					'datatype'		=> 'VARCHAR(80)',
					'allow_null'	=> true
				),
				'activate_key'		=> array(
					'datatype'		=> 'VARCHAR(8)',
					'allow_null'	=> true
				),
				'use_inbox'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'notify_inbox'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '1'
				),
				'notify_inbox_full'=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'num_inbox'	=> array(
					'datatype'		=> 'INT(10) UNSIGNED',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'first_run'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'color_scheme'	=> array(
					'datatype'		=> 'INT(25)',
					'allow_null'	=> false,
					'default'		=> '2'
				),
				'adapt_time'		=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				),
				'accent'	=> array(
					'datatype'		=> 'INT(25)',
					'allow_null'	=> false,
					'default'		=> '2'
				),
				'enforce_accent'	=> array(
					'datatype'		=> 'TINYINT(1)',
					'allow_null'	=> false,
					'default'		=> '0'
				)
			),
			'PRIMARY KEY'	=> array('id'),
			'UNIQUE KEYS'	=> array(
				'username_idx'		=> array('username')
			),
			'INDEXES'		=> array(
				'registered_idx'	=> array('registered')
			)
		);
	
		if ($db_type == 'mysql' || $db_type == 'mysqli' || $db_type == 'mysql_innodb' || $db_type == 'mysqli_innodb')
			$schema['UNIQUE KEYS']['username_idx'] = array('username(25)');
	
		$db->create_table('users', $schema) or error('Unable to create users table', __FILE__, __LINE__, $db->error());
		
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
				'last_comment'			=> array(
					'datatype'			=> 'INT(10)',
					'allow_null'		=> true,
					'default'			=> '0'
				),
				'last_comment_id'		=> array(
					'datatype'			=> 'INT(10)',
					'allow_null'		=> true,
					'default'			=> '0'
				),
				'last_commenter'		=> array(
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
				'commented'	=> array(
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

		// Insert config data
		$luna_config = array(
			'o_cur_version'				=> Version::LUNA_VERSION,
			'o_core_version'			=> Version::LUNA_CORE_VERSION,
			'o_code_name'				=> Version::LUNA_CODE_NAME,
			'o_database_revision'		=> Version::LUNA_DB_VERSION,
			'o_searchindex_revision'	=> Version::LUNA_SI_VERSION,
			'o_parser_revision'			=> Version::LUNA_PARSER_VERSION,
			'o_board_title'				=> $title,
			'o_board_desc'				=> $description,
			'o_board_tags'				=> NULL,
			'o_timezone'				=> 'UTC',
			'o_time_format'				=> __('H:i', 'luna'),
			'o_date_format'				=> __('j M Y', 'luna'),
			'o_timeout_visit'			=> 1800,
			'o_timeout_online'			=> 300,
			'o_show_user_info'			=> 1,
			'o_show_comment_count'			=> 1,
			'o_signatures'				=> 1,
			'o_smilies_sig'				=> 1,
			'o_make_links'				=> 1,
			'o_default_lang'			=> $default_lang,
			'o_default_style'			=> $default_style,
			'o_default_accent'			=> 2,
			'o_allow_accent_color'		=> 1,
			'o_allow_night_mode'		=> 1,
			'o_default_user_group'		=> 4,
			'o_disp_threads'			=> 30,
			'o_disp_comments'			=> 25,
			'o_indent_num_spaces'		=> 4,
			'o_quote_depth'				=> 3,
			'o_users_online'			=> 1,
			'o_censoring'				=> 0,
			'o_ranks'					=> 1,
			'o_has_commented'			=> 1,
			'o_thread_views'			=> 1,
			'o_gzip'					=> 0,
			'o_report_method'			=> 0,
			'o_regs_report'				=> 0,
			'o_default_email_setting'	=> 1,
			'o_mailing_list'			=> $email,
			'o_avatars'					=> $avatars,
			'o_avatars_dir'				=> 'img/avatars',
			'o_avatars_width'			=> 128,
			'o_avatars_height'			=> 128,
			'o_avatars_size'			=> 30720,
			'o_search_all_forums'		=> 1,
			'o_base_url'				=> $base_url,
			'o_admin_email'				=> $email,
			'o_webmaster_email'			=> $email,
			'o_forum_subscriptions'		=> 1,
			'o_thread_subscriptions'	=> 1,
			'o_first_run_message'		=> __('Wow, it\'s great to have you here, welcome and thanks for joining us. We\'ve set up your account and you\'re ready to go. Though we like to point out some actions you might want to do first.', 'luna'),
			'o_show_first_run'			=> 1,
			'o_first_run_guests'		=> 1,
			'o_first_run_backstage'		=> 0,
			'o_smtp_host'				=> NULL,
			'o_smtp_user'				=> NULL,
			'o_smtp_pass'				=> NULL,
			'o_smtp_ssl'				=> 0,
			'o_regs_allow'				=> 1,
			'o_regs_verify'				=> 0,
			'o_video_width'				=> 640,
			'o_video_height'			=> 360,
			'o_enable_advanced_search'	=> 1,
			'o_announcement'			=> 0,
			'o_announcement_message'	=> __('Announcement', 'luna'),
			'o_announcement_title'		=> NULL,
			'o_announcement_type'		=> 'ifno',
			'o_rules'					=> 0,
			'o_rules_message'			=> __('Rules', 'luna'),
			'o_maintenance'				=> 0,
			'o_maintenance_message'		=> __('The forums are temporarily down for maintenance. Please try again in a few minutes.', 'luna'),
			'o_feed_type'				=> 2,
			'o_feed_ttl'				=> 0,
			'o_cookie_bar'				=> 0,
			'o_cookie_bar_url'			=> 'http://getluna.org/docs/cookies.php',
			'o_moderated_by'			=> 1,
			'o_admin_note'				=> '',
			'o_enable_inbox'				=> 1,
			'o_message_per_page'		=> 10,
			'o_max_receivers'		=> 5,
			'o_inbox_notification'		=> 1,
			'o_emoji'					=> 0,
			'o_emoji_size'				=> 16,
			'o_back_to_top'				=> 1,
			'o_show_copyright'			=> 1,
			'o_copyright_type'			=> 0,
			'o_custom_copyright'		=> NULL,
			'o_header_search'			=> 1,
			'o_board_statistics'		=> 1,
			'o_notification_flyout'		=> 1,
			'o_update_ring'				=> 1,
			'p_message_img_tag'			=> 1,
			'p_message_all_caps'		=> 1,
			'p_subject_all_caps'		=> 1,
			'p_sig_all_caps'			=> 1,
			'p_sig_img_tag'				=> 0,
			'p_sig_length'				=> 400,
			'p_sig_lines'				=> 4,
			'p_allow_banned_email'		=> 1,
			'p_allow_dupe_email'		=> 0,
			'p_force_guest_email'		=> 1
		);
	
		foreach ($luna_config as $conf_name => $conf_value) {
			$db->query('INSERT INTO '.$db_prefix.'config (conf_name, conf_value) VALUES(\''.$conf_name.'\', '.(is_null($conf_value) ? 'NULL' : '\''.$db->escape($conf_value).'\'').')')
				or error('Unable to insert into table '.$db_prefix.'config. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
		}
		
		$db->end_transaction();

		return $db;
	}
	
	public static function insert_default_users($username, $password, $email, $language, $style) {
		global $db, $db_type;
		
		$now = time();
		
		$db->start_transaction();

		// Insert guest and first admin user
		$db->query('INSERT INTO '.$db->prefix.'users (group_id, username, password, email) VALUES(3, \''.$db->escape(__('Guest', 'luna')).'\', \''.$db->escape(__('Guest', 'luna')).'\', \''.$db->escape(__('Guest', 'luna')).'\')')
			or error('Unable to add guest user. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'users (group_id, username, password, email, language, style, num_comments, last_comment, registered, registration_ip, last_visit) VALUES(1, \''.$db->escape($username).'\', \''.luna_hash($password).'\', \''.$email.'\', \''.$db->escape($language).'\', \''.$db->escape($style).'\', 1, '.$now.', '.$now.', \''.$db->escape(get_remote_address()).'\', '.$now.')')
			or error('Unable to add administrator user. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
		
		$db->end_transaction();
	}
	
	public static function insert_default_groups() {
		global $db, $db_type;
		
		$now = time();
		
		$db->start_transaction();

		// Insert the first 4 groups
		$db->query('INSERT INTO '.$db->prefix.'groups ('.($db_type != 'pgsql' ? 'g_id, ' : '').'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_comment, g_create_threads, g_edit_comments, g_delete_comments, g_delete_threads, g_set_title, g_search, g_search_users, g_send_email, g_comment_flood, g_search_flood, g_email_flood, g_report_flood, g_soft_delete_view, g_soft_delete_comments, g_soft_delete_threads) VALUES('.($db_type != 'pgsql' ? '1, ' : '').'\''.$db->escape(__('Administrators', 'luna')).'\', \''.$db->escape(__('Administrator', 'luna')).'\', 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1)') or error('Unable to add group', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'groups ('.($db_type != 'pgsql' ? 'g_id, ' : '').'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_comment, g_create_threads, g_edit_comments, g_delete_comments, g_delete_threads, g_set_title, g_search, g_search_users, g_send_email, g_comment_flood, g_search_flood, g_email_flood, g_report_flood, g_soft_delete_view, g_soft_delete_comments, g_soft_delete_threads) VALUES('.($db_type != 'pgsql' ? '2, ' : '').'\''.$db->escape(__('Moderators', 'luna')).'\', \''.$db->escape(__('Moderator', 'luna')).'\', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 1, 1)') or error('Unable to add group', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'groups ('.($db_type != 'pgsql' ? 'g_id, ' : '').'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_comment, g_create_threads, g_edit_comments, g_delete_comments, g_delete_threads, g_set_title, g_search, g_search_users, g_send_email, g_comment_flood, g_search_flood, g_email_flood, g_report_flood, g_soft_delete_view, g_soft_delete_comments, g_soft_delete_threads) VALUES('.($db_type != 'pgsql' ? '3, ' : '').'\''.$db->escape(__('Guests', 'luna')).'\', NULL, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 1, 1, 0, 60, 30, 0, 0, 0, 0, 0)') or error('Unable to add group', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'groups ('.($db_type != 'pgsql' ? 'g_id, ' : '').'g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_comment, g_create_threads, g_edit_comments, g_delete_comments, g_delete_threads, g_set_title, g_search, g_search_users, g_send_email, g_comment_flood, g_search_flood, g_email_flood, g_report_flood, g_soft_delete_view, g_soft_delete_comments, g_soft_delete_threads) VALUES('.($db_type != 'pgsql' ? '4, ' : '').'\''.$db->escape(__('Members', 'luna')).'\', NULL, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 60, 30, 60, 60, 0, 0, 0)') or error('Unable to add group', __FILE__, __LINE__, $db->error());
		
		$db->end_transaction();
	}
	
	public static function instert_default_menu() {
		global $db;
		
		$db->start_transaction();

		$db->query('INSERT INTO '.$db->prefix.'menu (url, name, disp_position, visible, sys_entry) VALUES(\'index.php\', \'Index\', 1, \'1\', 1)')
			or error('Unable to add Index menu item. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'menu (url, name, disp_position, visible, sys_entry) VALUES(\'userlist.php\', \'Users\', 2, \'1\', 1)')
			or error('Unable to add Users menu item. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'menu (url, name, disp_position, visible, sys_entry) VALUES(\'search.php\', \'Search\', 3, \'1\', 1)')
			or error('Unable to add Search menu item. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
		
		$db->end_transaction();
	}
	
	public static function insert_default_data() {
		global $db, $db_type;
		
		$now = time();
		
		$db->start_transaction();

		$db->query('INSERT INTO '.$db->prefix.'ranks (rank, min_comments) VALUES(\''.$db->escape(__('New member', 'luna')).'\', 0)')
			or error('Unable to insert into table '.$db->prefix.'ranks. Please check your configuration and try again', __FILE__, __LINE__, $db->error());
	
		$db->query('INSERT INTO '.$db->prefix.'ranks (rank, min_comments) VALUES(\''.$db->escape(__('Member', 'luna')).'\', 10)')
			or error('Unable to insert into table '.$db->prefix.'ranks. Please check your configuration and try again', __FILE__, __LINE__, $db->error());

		require LUNA_ROOT.'include/notifications.php';		
		new_notification('2', 'backstage/about.php', 'Welcome to Luna, discover the possibilities!', 'fa-moon-o');
		
		$db->end_transaction();
	}
}