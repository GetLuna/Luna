<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('LUNA_ROOT', '../');
require LUNA_ROOT.'include/common.php';

if (!$is_admin)
	header("Location: login.php");
//
// Increase maximum execution time, but don't complain about it if it isn't allowed.
//
@set_time_limit(0);

function gzip_PrintFourChars($Val) {
	for ($i = 0; $i < 4; $i ++) {
		$return = chr($Val % 256);
		$Val = floor($Val / 256);
	}
	return $return;
}

// db functions (not in Luna dblayer)
function field_name($offset, $query_id = 0) {
	global $db_type;

	if(!$query_id) {
		$query_id = $this->query_result;
	}
	if($query_id) {
		switch($db_type) {
			case 'mysql':
			case 'mysql_innodb':
				$result = @mysql_field_name($query_id, $offset);
			break;
			case 'mysqli':
			case 'mysqli_innodb':
				$finfo = @mysqli_fetch_field_direct($query_id, $offset);
				$result = $finfo->name;
		}
		return $result;
	} else
		return false;
}

function num_fields($query_id = 0) {
	global $db_type;
	if (!$query_id)
		$query_id = $this->query_result;
		switch($db_type) {
			case 'mysql':
			case 'mysql_innodb':
				return ($query_id) ? @mysql_num_fields($query_id) : false;
			break;
			case 'mysqli':
			case 'mysqli_innodb':
				return ($query_id) ? @mysqli_num_fields($query_id) : false;
		}
}

//
// This function returns the "CREATE TABLE" syntax for mysql dbms
//
function get_table_def_mysql($table, $crlf) {
	global $drop, $db;

	$schema_create = "";
	$field_query = "SHOW FIELDS FROM $table";
	$key_query = "SHOW KEYS FROM $table";
	$schema_create = "DROP TABLE IF EXISTS $table;$crlf";

	$schema_create .= "CREATE TABLE $table($crlf";

	//
	// Ok lets grab the fields
	//
	$result = $db->query($field_query);
	if(!$result)
		message_backstage('Failed to get field list');

	while ($row = $db->fetch_assoc($result)) {
		$schema_create .= '	' . $row['Field'] . ' ' . $row['Type'];

		if(!empty($row['Default'])) {
			$schema_create .= ' DEFAULT \'' . $row['Default'] . '\'';
		}

		if($row['Null'] != "YES") {
			$schema_create .= ' NOT NULL';
		}

		if($row['Extra'] != "") {
			$schema_create .= ' ' . $row['Extra'];
		}

		$schema_create .= ",$crlf";
	}
	//
	// Drop the last ',$crlf' off ;)
	//
	$schema_create = preg_replace('/,'.$crlf.'$/', "", $schema_create);

	//
	// Get any Indexed fields from the database
	//
	$result = $db->query($key_query);
	if(!$result)
		message_backstage('Failed to get Indexed Fields');

	while($row = $db->fetch_assoc($result)) {
		$kname = $row['Key_name'];

		if(($kname != 'PRIMARY') && ($row['Non_unique'] == 0)) {
			$kname = "UNIQUE|$kname";
		}
		if (!isset($index[$kname]))
			$index[$kname] = array();

		$index[$kname][] = $row['Column_name'];
	}

	while(list($x, $columns) = @each($index)) {
		$schema_create .= ", $crlf";

		if($x == 'PRIMARY') {
			$schema_create .= '	PRIMARY KEY (' . implode($columns, ', ') . ')';
		} elseif (substr($x,0,6) == 'UNIQUE') {
			$schema_create .= '	UNIQUE ' . substr($x,7) . ' (' . implode($columns, ', ') . ')';
		} else {
			$schema_create .= "	KEY $x (" . implode($columns, ', ') . ')';
		}
	}

	$schema_create .= "$crlf);";

	if(get_magic_quotes_runtime()) {
		return(stripslashes($schema_create));
	} else {
		return($schema_create);
	}

} // End get_table_def_mysql


//
// This function is for getting the data from a mysql table.
//


function get_table_content_mysql($table, $handler) {
	global $db;

	// Grab the data from the table.
	if (!($result = $db->query("SELECT * FROM $table")))
		message_backstage('Failed to get table content');

	// Loop through the resulting rows and build the sql statement.
	if ($row = $db->fetch_assoc($result)) {
		$handler("\n#\n# Table Data for $table\n#\n");
		$field_names = array();

		// Grab the list of field names.
		$num_fields = num_fields($result);
		$table_list = '(';
		for ($j = 0; $j < $num_fields; $j++) {
			$field_names[$j] = field_name($j, $result);
			$table_list .= (($j > 0) ? ', ' : '') . $field_names[$j];

		}
		$table_list .= ')';

		do {
			// Start building the SQL statement.
			$schema_insert = "INSERT INTO $table $table_list VALUES(";

			// Loop through the rows and fill in data for each column
			for ($j = 0; $j < $num_fields; $j++) {
				$schema_insert .= ($j > 0) ? ', ' : '';

				if(!isset($row[$field_names[$j]])) {
					//
					// If there is no data for the column set it to null.
					// There was a problem here with an extra space causing the
					// sql file not to reimport if the last column was null in
					// any table.  Should be fixed now :) JLH
					//
					$schema_insert .= 'NULL';
				} elseif ($row[$field_names[$j]] != '') {
					$schema_insert .= '\'' . addslashes($row[$field_names[$j]]) . '\'';
				} else {
					$schema_insert .= '\'\'';
				}
			}

			$schema_insert .= ');';

			// Go ahead and send the insert statement to the handler function.
			$handler(trim($schema_insert));

		}
		while ($row = $db->fetch_assoc($result));
	}

	return(true);
}

function output_table_content($content) {
	global $tempfile;

	// fwrite($tempfile, $content . "\n");
	// $backup_sql .= $content . "\n";
	echo $content ."\n";
	return;
}

function remove_remarks($sql) {
	$lines = explode("\n", $sql);

	// try to keep mem. use down
	$sql = "";

	$linecount = count($lines);
	$output = "";

	for ($i = 0; $i < $linecount; $i++) {
		if ((($i != ($linecount - 1)) || (strlen($lines[$i]) > 0)) and $lines[$i]) {
			if ($lines[$i][0] != "#") {
				$output .= $lines[$i] . "\n";
				// Trading a bit of speed for lower mem. use here.
				$lines[$i] = "";
			}
		}
	}
	return $output;
}

function split_sql_file($sql, $delimiter) {
	// Split up our string into "possible" SQL statements.
	$tokens = explode($delimiter, $sql);

	// try to save mem.
	$sql = "";
	$output = array();

	// we don't actually care about the matches preg gives us.
	$matches = array();

	// this is faster than calling count($oktens) every time thru the loop.
	$token_count = count($tokens);
	for ($i = 0; $i < $token_count; $i++) {
		// Don't wanna add an empty string as the last thing in the array.
		if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0))) {
			// This is the total number of single quotes in the token.
			$total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
			// Counts single quotes that are preceded by an odd number of backslashes,
			// which means they're escaped quotes.
			$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);

			$unescaped_quotes = $total_quotes - $escaped_quotes;

			// If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
			if (($unescaped_quotes % 2) == 0) {
				// It's a complete sql statement.
				$output[] = $tokens[$i];
				// save memory.
				$tokens[$i] = "";
			} else {
				// incomplete sql statement. keep adding tokens until we have a complete one.
				// $temp will hold what we have so far.
				$temp = $tokens[$i] . $delimiter;
				// save memory..
				$tokens[$i] = "";

				// Do we have a complete statement yet?
				$complete_stmt = false;

				for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++) {
					// This is the total number of single quotes in the token.
					$total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
					// Counts single quotes that are preceded by an odd number of backslashes,
					// which means they're escaped quotes.
					$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);

					$unescaped_quotes = $total_quotes - $escaped_quotes;

					if (($unescaped_quotes % 2) == 1) {
						// odd number of unescaped quotes. In combination with the previous incomplete
						// statement(s), we now have a complete statement. (2 odds always make an even)
						$output[] = $temp . $tokens[$j];

						// save memory.
						$tokens[$j] = "";
						$temp = "";

						// exit the loop.
						$complete_stmt = true;
						// make sure the outer loop continues at the right point.
						$i = $j;
					} else {
						// even number of unescaped quotes. We still don't have a complete statement.
						// (1 odd and 1 even always make an odd)
						$temp .= $tokens[$j] . $delimiter;
						// save memory.
						$tokens[$j] = "";
					}
				} // for..
			} // else
		}
	}
	return $output;
}
//
// End Functions
// -------------
//
// Begin program proper
//

// Check this is a mysql Luna setup
switch($db_type) {
	case 'mysql':
	case 'mysql_innodb':
	case 'mysqli':
	case 'mysqli_innodb':
		break;
	default:
		message_backstage('Sorry your database type is not supported');
}
// Start actual db stuff
if (isset($_POST['backupstart'])) {
	// Output sql dump
	$tables = array('bans', 'categories', 'censoring', 'config', 'forums', 'forum_perms', 'forum_subscriptions', 'groups', 'menu', 'messages', 'notifications', 'online', 'comments', 'ranks', 'reports', 'search_cache', 'search_matches', 'search_words', 'threads', 'thread_subscriptions', 'users');
	$backup_type = (isset($_POST['backup_type'])) ? $_POST['backup_type'] : ( (isset($HTTP_GET_VARS['backup_type'])) ? $HTTP_GET_VARS['backup_type'] : "" );
	$gzipcompress = (!empty($_POST['gzipcompress'])) ? $_POST['gzipcompress'] : ( (!empty($HTTP_GET_VARS['gzipcompress'])) ? $HTTP_GET_VARS['gzipcompress'] : 0 );
	$drop = (!empty($_POST['drop'])) ? intval($_POST['drop']) : ( (!empty($HTTP_GET_VARS['drop'])) ? intval($HTTP_GET_VARS['drop']) : 0 );

	header("Pragma: no-cache");
	$do_gzip_compress = FALSE;
	if( $gzipcompress ) {
		$phpver = phpversion();
		if($phpver >= "4.0") {
			if(extension_loaded("zlib")) {
				$do_gzip_compress = TRUE;
			}
		}
	}
	if($do_gzip_compress) {
		@ob_start();
		@ob_implicit_flush(0);
		header("Content-Type: application/x-gzip; name=\"luna_backup." . date("Y-m-d") . ".sql.gz\"");
		header("Content-disposition: attachment; filename=luna_backup." . date("Y-m-d") . ".sql.gz");
	} else {
		header("Content-Type: text/x-delimtext; name=\"luna_backup." . date("Y-m-d") . ".sql\"");
		header("Content-disposition: attachment; filename=luna_backup." . date("Y-m-d") . ".sql");
	}
	//
	// Build the sql script file
	//
	echo "#\n";
	echo "# Luna Backup Script\n";
	echo "# Dump of tables for $db_name\n";
	echo "#\n# DATE : " .  date("d-m-Y H:i:s", time()) . " GMT\n";
	echo "#\n";
	for($i = 0; $i < count($tables); $i++) {
		$table_name = $tables[$i];
		$table_def_function = "get_table_def_mysql";
		$table_content_function = "get_table_content_mysql";
		if($backup_type != 'data') {
			echo "\n#\n# TABLE: " . $db->prefix . $table_name . "\n#\n\n";
			echo $table_def_function($db->prefix . $table_name, "\n") . "\n";
		}
		if($backup_type != 'structure') {
			$table_content_function($db->prefix . $table_name, "output_table_content");
		}
	}
	if($do_gzip_compress) {
		$Size = ob_get_length();
		$Crc = crc32(ob_get_contents());
		$contents = gzcompress(ob_get_contents());
		ob_end_clean();
		echo "\x1f\x8b\x08\x00\x00\x00\x00\x00".substr($contents, 0, strlen($contents) - 4).gzip_PrintFourChars($Crc).gzip_PrintFourChars($Size);
	}
	exit;
} elseif ( isset($_POST['restore_start']) ) {
	// Restore SQL Dump
	//
	// Handle the file upload
	// If no file was uploaded report an error
	//
	$backup_file_name = (!empty($_FILES['backup_file']['name'])) ? $_FILES['backup_file']['name'] : "";
	$backup_file_tmpname = ($_FILES['backup_file']['tmp_name'] != "none") ? $_FILES['backup_file']['tmp_name'] : "";
	$backup_file_type = (!empty($_FILES['backup_file']['type'])) ? $_FILES['backup_file']['type'] : "";
	if($backup_file_tmpname == "" || $backup_file_name == "")
		message_backstage(__('No file was uploaded or the upload failed, the database was not restored.', 'luna'));

	if( preg_match("/^(text\/[a-zA-Z]+)|(application\/(x\-)?gzip(\-compressed)?)|(application\/octet-stream)$/is", $backup_file_type) ) {
		if( preg_match("/\.gz$/is",$backup_file_name) ) {
			$do_gzip_compress = FALSE;
			$phpver = phpversion();
			if($phpver >= "4.0") {
				if(extension_loaded("zlib")) {
					$do_gzip_compress = TRUE;
				}
			}
			if($do_gzip_compress) {
				$gz_ptr = gzopen($backup_file_tmpname, 'rb');
				$sql_query = "";
				while( !gzeof($gz_ptr) ) {
					$sql_query .= gzgets($gz_ptr, 100000);
				}
			} else
				message_backstage(__('Sorry the database could not be restored.', 'luna'));
		} else {
			$sql_query = fread(fopen($backup_file_tmpname, 'r'), filesize($backup_file_tmpname));
		}
	} else
		message_backstage(__('Error the file name or file format caused an error, the database was not restored.', 'luna'));

	if ($sql_query != "") {
		// Strip out sql comments
		$sql_query = remove_remarks($sql_query);
		$pieces = split_sql_file($sql_query, ";");
		if(defined('LUNA_DEBUG')) {
		require 'header.php';
		load_admin_nav('maintenance', 'database');
?>
	<div>
		<h2><?php _e('Debug info', 'luna') ?></h2>
		<p>
<?php
		}
		$sql_count = count($pieces);
		for($i = 0; $i < $sql_count; $i++) {
			$sql = trim($pieces[$i]);
			if(!empty($sql)) {
				if(defined('LUNA_DEBUG')) {
					echo "Executing: $sql\n<br>";
					flush();
				}
				$result = $db->query($sql);
				if(!$result)
					message_backstage(__('Error imported backup file, the database probably has not been restored.', 'luna'));
			}
		}
		if(defined('LUNA_DEBUG')) {
?>
		</p>
	</div>
<?php
		}
	}
	if(defined('LUNA_DEBUG')) {
?>
	<h2><?php _e('Restore complete', 'luna') ?></h2>
<?php
	} else
		message_backstage(__('Restore completed', 'luna'));
} elseif (isset($_POST['repairall'])) {
	// repair all tables
	// Retrieve table list:
	$sql = 'SHOW TABLE STATUS';
	if (!$result = $db->query($sql)) // This makes no sense, the board would be dead :P
		message_backstage(__('Tables error, repair failed.', 'luna'));
	$tables = array();
	$counter = 0;
	while ($row = $db->fetch_assoc($result)) {
		$counter++;
		$tables[$counter] = $row['Name'];
	}
	$tablecount = $counter;

	// Repair All
	for ($i = 1; $i <= $tablecount; $i++) {
		$sql = 'REPAIR TABLE ' . $tables[$i];
		if (!$result = $db->query($sql))
			message_backstage(__('SQL error, repair failed.', 'luna'));
	}

	message_backstage('All tables repaired');
} elseif (isset($_POST['optimizeall'])) {
	// Retrieve table list:
	$sql = 'SHOW TABLE STATUS';
	if (!$result = $db->query($sql)) // This makes no sense, the board would be dead :P
		message_backstage(__('Tables error, optimize failed.', 'luna'));
	$tables = array();
	$counter = 0;
	while ($row = $db->fetch_assoc($result)) {
		$counter++;
		$tables[$counter] = $row['Name'];
	}
	$tablecount = $counter;

	// Optimize All
	for ($i = 1; $i <= $tablecount; $i++) {
		$sql = 'OPTIMIZE TABLE ' . $tables[$i];
		if (!$result = $db->query($sql))
			message_backstage(__('SQL error, optimize failed.', 'luna'));
	}

	message_backstage('All tables optimized');
} else {
	
	$action = isset($_GET['action']) ? $_GET['action'] : null;
	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('Database', 'luna'));
	define('LUNA_ACTIVE_PAGE', 'admin');
	require 'header.php';
		load_admin_nav('maintenance', 'database');
?>
<form class="form-horizontal" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Backup', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="backupstart"><span class="fa fa-fw fa-floppy-o"></span> <?php _e('Start backup', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<p><?php _e('If your server supports it, you may also gzip-compress the file to reduce its size.', 'luna') ?></p>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Backup type', 'luna') ?></label>
					<div class="col-sm-9">
						<label class="radio-inline">
							<input type="radio" name="backup_type" value="full" checked />
							<?php _e('Full', 'luna') ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="backup_type" value="structure" />
							<?php _e('Structure only', 'luna') ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="backup_type" value="data" />
							<?php _e('Data only', 'luna') ?>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Gzip compression', 'luna') ?></label>
					<div class="col-sm-9">
						<label class="radio-inline">
							<input type="radio" name="gzipcompress" value="1" />
							<?php _e('Yes', 'luna') ?>
						</label>
						<label class="radio-inline">
							<input type="radio" name="gzipcompress" value="0" checked />
							<?php _e('No', 'luna') ?>
						</label>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Restore', 'luna') ?><span class="pull-right"><button class="btn btn-primary" type="submit" name="restore_start"><span class="fa fa-fw fa-reply"></span> <?php _e('Start restore', 'luna') ?></button></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<p><?php _e('This will perform a full restore of all Luna tables from a saved file. If your server supports it, you may upload a gzip-compressed text file and it will automatically be decompressed. This will overwrite any existing data. The restore may take a long time to process, so please do not move from this page until it is complete.', 'luna') ?></p>
				<input type="file" name="backup_file" />
			</fieldset>
		</div>
	</div>
</form>
<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Additional functions', 'luna') ?></h3>
		</div>
		<div class="panel-body">
			<p><?php _e('Additional features to help run a database, optimize and repair both do what they say.', 'luna') ?></p>
		</div>
		<div class="panel-footer">
			<fieldset>
				<span class="btn-group">
					<button class="btn btn-primary" type="submit" name="repairall"><span class="fa fa-fw fa-wrench"></span> <?php _e('Repair all tables', 'luna') ?></button>
					<button class="btn btn-primary" type="submit" name="optimizeall"><span class="fa fa-fw fa-heartbeat"></span> <?php _e('Optimize all tables', 'luna') ?></button>
				</span>
			</fieldset>
		</div>
	</div>
</form>
<?php
}

require 'footer.php';