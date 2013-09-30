<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$pun_user['is_admmod']) {
    header("Location: ../login.php");
}

// Load the backstage.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/backstage.php';

//
// Increase maximum execution time, but don't complain about it if it isn't
// allowed.
//
@set_time_limit(0);

function gzip_PrintFourChars($Val)
{
	for ($i = 0; $i < 4; $i ++)
	{
		$return = chr($Val % 256);
		$Val = floor($Val / 256);
	}
	return $return;
}


//db functions (not in punbb dblayer)
function field_name($offset, $query_id = 0)
{
	global $db_type;

	if(!$query_id)
	{
		$query_id = $this->query_result;
	}
	if($query_id)
	{
		switch($db_type)
		{
			case 'mysql':
				$result = @mysql_field_name($query_id, $offset);
			break;
			case 'mysqli':
				$finfo = @mysqli_fetch_field_direct($query_id, $offset);
				$result = $finfo->name;
		}
		return $result;
	}
	else
		return false;
}

function num_fields($query_id = 0)
{
	global $db_type;
	if (!$query_id)
		$query_id = $this->query_result;
		switch($db_type)
		{
			case 'mysql':
				return ($query_id) ? @mysql_num_fields($query_id) : false;
			break;
			case 'mysqli':
				return ($query_id) ? @mysqli_num_fields($query_id) : false;
		}
}

//
// This function returns the "CREATE TABLE" syntax for mysql dbms...
//
function get_table_def_mysql($table, $crlf)
{
	global $drop, $db;

	$schema_create = "";
	$field_query = "SHOW FIELDS FROM $table";
	$key_query = "SHOW KEYS FROM $table";
	$schema_create = "DROP TABLE IF EXISTS $table;$crlf";

	$schema_create .= "CREATE TABLE $table($crlf";

	//
	// Ok lets grab the fields...
	//
	$result = $db->query($field_query);
	if(!$result)
	{
		generate_admin_menu('database');
		message('Failed to get field list');
	}

	while ($row = $db->fetch_assoc($result))
	{
		$schema_create .= '	' . $row['Field'] . ' ' . $row['Type'];

		if(!empty($row['Default']))
		{
			$schema_create .= ' DEFAULT \'' . $row['Default'] . '\'';
		}

		if($row['Null'] != "YES")
		{
			$schema_create .= ' NOT NULL';
		}

		if($row['Extra'] != "")
		{
			$schema_create .= ' ' . $row['Extra'];
		}

		$schema_create .= ",$crlf";
	}
	//
	// Drop the last ',$crlf' off ;)
	//
	$schema_create = ereg_replace(',' . $crlf . '$', "", $schema_create);

	//
	// Get any Indexed fields from the database...
	//
	$result = $db->query($key_query);
	if(!$result)
	{
		generate_admin_menu('database');
		message('Failed to get Indexed Fields');
	}

	while($row = $db->fetch_assoc($result))
	{
		$kname = $row['Key_name'];

		if(($kname != 'PRIMARY') && ($row['Non_unique'] == 0))
		{
			$kname = "UNIQUE|$kname";
		}
		if (!isset($index[$kname]))
			$index[$kname] = array();

		$index[$kname][] = $row['Column_name'];
	}

	while(list($x, $columns) = @each($index))
	{
		$schema_create .= ", $crlf";

		if($x == 'PRIMARY')
		{
			$schema_create .= '	PRIMARY KEY (' . implode($columns, ', ') . ')';
		}
		elseif (substr($x,0,6) == 'UNIQUE')
		{
			$schema_create .= '	UNIQUE ' . substr($x,7) . ' (' . implode($columns, ', ') . ')';
		}
		else
		{
			$schema_create .= "	KEY $x (" . implode($columns, ', ') . ')';
		}
	}

	$schema_create .= "$crlf);";

	if(get_magic_quotes_runtime())
	{
		return(stripslashes($schema_create));
	}
	else
	{
		return($schema_create);
	}

} // End get_table_def_mysql


//
// This function is for getting the data from a mysql table.
//

function get_table_content_mysql($table, $handler)
{
	global $db;

	// Grab the data from the table.
	if (!($result = $db->query("SELECT * FROM $table")))
	{
		generate_admin_menu('database');
		message('Failed to get table content');
	}

	// Loop through the resulting rows and build the sql statement.
	if ($row = $db->fetch_assoc($result))
	{
		$handler("\n#\n# Table Data for $table\n#\n");
		$field_names = array();

		// Grab the list of field names.
		$num_fields = num_fields($result);
		$table_list = '(';
		for ($j = 0; $j < $num_fields; $j++)
		{
			$field_names[$j] = field_name($j, $result);
			$table_list .= (($j > 0) ? ', ' : '') . $field_names[$j];

		}
		$table_list .= ')';

		do
		{
			// Start building the SQL statement.
			$schema_insert = "INSERT INTO $table $table_list VALUES(";

			// Loop through the rows and fill in data for each column
			for ($j = 0; $j < $num_fields; $j++)
			{
				$schema_insert .= ($j > 0) ? ', ' : '';

				if(!isset($row[$field_names[$j]]))
				{
					//
					// If there is no data for the column set it to null.
					// There was a problem here with an extra space causing the
					// sql file not to reimport if the last column was null in
					// any table.  Should be fixed now :) JLH
					//
					$schema_insert .= 'NULL';
				}
				elseif ($row[$field_names[$j]] != '')
				{
					$schema_insert .= '\'' . addslashes($row[$field_names[$j]]) . '\'';
				}
				else
				{
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

function output_table_content($content)
{
	global $tempfile;

	//fwrite($tempfile, $content . "\n");
	//$backup_sql .= $content . "\n";
	echo $content ."\n";
	return;
}

function remove_remarks($sql)
{
	$lines = explode("\n", $sql);

	// try to keep mem. use down
	$sql = "";

	$linecount = count($lines);
	$output = "";

	for ($i = 0; $i < $linecount; $i++)
	{
		if ((($i != ($linecount - 1)) || (strlen($lines[$i]) > 0)) and $lines[$i])
		{
			if ($lines[$i][0] != "#")
			{
				$output .= $lines[$i] . "\n";
				// Trading a bit of speed for lower mem. use here.
				$lines[$i] = "";
			}
		}
	}
	return $output;
}

function split_sql_file($sql, $delimiter)
{
	// Split up our string into "possible" SQL statements.
	$tokens = explode($delimiter, $sql);

	// try to save mem.
	$sql = "";
	$output = array();

	// we don't actually care about the matches preg gives us.
	$matches = array();

	// this is faster than calling count($oktens) every time thru the loop.
	$token_count = count($tokens);
	for ($i = 0; $i < $token_count; $i++)
	{
		// Don't wanna add an empty string as the last thing in the array.
		if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0)))
		{
			// This is the total number of single quotes in the token.
			$total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
			// Counts single quotes that are preceded by an odd number of backslashes,
			// which means they're escaped quotes.
			$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);

			$unescaped_quotes = $total_quotes - $escaped_quotes;

			// If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
			if (($unescaped_quotes % 2) == 0)
			{
				// It's a complete sql statement.
				$output[] = $tokens[$i];
				// save memory.
				$tokens[$i] = "";
			}
			else
			{
				// incomplete sql statement. keep adding tokens until we have a complete one.
				// $temp will hold what we have so far.
				$temp = $tokens[$i] . $delimiter;
				// save memory..
				$tokens[$i] = "";

				// Do we have a complete statement yet?
				$complete_stmt = false;

				for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++)
				{
					// This is the total number of single quotes in the token.
					$total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
					// Counts single quotes that are preceded by an odd number of backslashes,
					// which means they're escaped quotes.
					$escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);

					$unescaped_quotes = $total_quotes - $escaped_quotes;

					if (($unescaped_quotes % 2) == 1)
					{
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
					}
					else
					{
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

//Check this is a mysql punbb setup
switch($db_type)
{
	case 'mysql':
	case 'mysqli':
		break;
	default:
		generate_admin_menu('database');
		message('Sorry your database type is not yet supported');
}
//Start actual db stuff
if (isset($_POST['backupstart'])) {
	//output sql dump
	$tables = array('bans', 'categories', 'censoring', 'config', 'forum_perms', 'forums', 'forum_subscriptions', 'groups', 'online', 'posts', 'ranks', 'reports', 'search_cache', 'search_matches', 'search_words', 'subscriptions', 'topics', 'topic_subscriptions', 'users'
	);
	$backup_type = (isset($_POST['backup_type'])) ? $_POST['backup_type'] : ( (isset($HTTP_GET_VARS['backup_type'])) ? $HTTP_GET_VARS['backup_type'] : "" );
	$gzipcompress = (!empty($_POST['gzipcompress'])) ? $_POST['gzipcompress'] : ( (!empty($HTTP_GET_VARS['gzipcompress'])) ? $HTTP_GET_VARS['gzipcompress'] : 0 );
	$drop = (!empty($_POST['drop'])) ? intval($_POST['drop']) : ( (!empty($HTTP_GET_VARS['drop'])) ? intval($HTTP_GET_VARS['drop']) : 0 );

	header("Pragma: no-cache");
	$do_gzip_compress = FALSE;
	if( $gzipcompress )
	{
		$phpver = phpversion();
		if($phpver >= "4.0")
		{
			if(extension_loaded("zlib"))
			{
				$do_gzip_compress = TRUE;
			}
		}
	}
	if($do_gzip_compress)
	{
		@ob_start();
		@ob_implicit_flush(0);
		header("Content-Type: application/x-gzip; name=\"punbb_db_backup." . gmdate("Y-m-d") . ".sql.gz\"");
		header("Content-disposition: attachment; filename=punbb_db_backup." . gmdate("Y-m-d") . ".sql.gz");
	}
	else
	{
		header("Content-Type: text/x-delimtext; name=\"punbb_db_backup." . gmdate("Y-m-d") . ".sql\"");
		header("Content-disposition: attachment; filename=punbb_db_backup." . gmdate("Y-m-d") . ".sql");
	}
	//
	// Build the sql script file...
	//
	echo "#\n";
	echo "# Punbb Backup Script\n";
	echo "# Dump of tables for $db_name\n";
	echo "#\n# DATE : " .  gmdate("d-m-Y H:i:s", time()) . " GMT\n";
	echo "#\n";
	for($i = 0; $i < count($tables); $i++)
	{
		$table_name = $tables[$i];
		$table_def_function = "get_table_def_mysql";
		$table_content_function = "get_table_content_mysql";
		if($backup_type != 'data')
		{
			echo "\n#\n# TABLE: " . $db->prefix . $table_name . "\n#\n\n";
			echo $table_def_function($db->prefix . $table_name, "\n") . "\n";
		}
		if($backup_type != 'structure')
		{
			$table_content_function($db->prefix . $table_name, "output_table_content");
		}
	}
	if($do_gzip_compress)
	{
		$Size = ob_get_length();
		$Crc = crc32(ob_get_contents());
		$contents = gzcompress(ob_get_contents());
		ob_end_clean();
		echo "\x1f\x8b\x08\x00\x00\x00\x00\x00".substr($contents, 0, strlen($contents) - 4).gzip_PrintFourChars($Crc).gzip_PrintFourChars($Size);
	}
exit;
}
elseif ( isset($_POST['restore_start']) ) {
	// Restore SQL Dump
	//
	// Handle the file upload ....
	// If no file was uploaded report an error...
	//
	$backup_file_name = (!empty($HTTP_POST_FILES['backup_file']['name'])) ? $HTTP_POST_FILES['backup_file']['name'] : "";
	$backup_file_tmpname = ($HTTP_POST_FILES['backup_file']['tmp_name'] != "none") ? $HTTP_POST_FILES['backup_file']['tmp_name'] : "";
	$backup_file_type = (!empty($HTTP_POST_FILES['backup_file']['type'])) ? $HTTP_POST_FILES['backup_file']['type'] : "";
	if($backup_file_tmpname == "" || $backup_file_name == "")
	{
		generate_admin_menu('database');
		message('No file was uploaed or the upload failed, the database was not restored');
	}
	if( preg_match("/^(text\/[a-zA-Z]+)|(application\/(x\-)?gzip(\-compressed)?)|(application\/octet-stream)$/is", $backup_file_type) )
	{
		if( preg_match("/\.gz$/is",$backup_file_name) )
		{
			$do_gzip_compress = FALSE;
			$phpver = phpversion();
			if($phpver >= "4.0")
			{
				if(extension_loaded("zlib"))
				{
					$do_gzip_compress = TRUE;
				}
			}
			if($do_gzip_compress)
			{
				$gz_ptr = gzopen($backup_file_tmpname, 'rb');
				$sql_query = "";
				while( !gzeof($gz_ptr) )
				{
					$sql_query .= gzgets($gz_ptr, 100000);
				}
			}
			else
			{
				generate_admin_menu('database');
				message('Sorry the database could not be restored');
			}
		}
		else
		{
			$sql_query = fread(fopen($backup_file_tmpname, 'r'), filesize($backup_file_tmpname));
		}
	}
	else
	{
		generate_admin_menu('database');
		message('Error the file name or file format caused an error, the database was not restored');
	}
	if($sql_query != "")
	{
		// Strip out sql comments...
		$sql_query = remove_remarks($sql_query);
		$pieces = split_sql_file($sql_query, ";");
		if(defined('FORUM_DEBUG'))
		{
		generate_admin_menu($plugin);
?>
	<div class="block">
		<h2><span>Debug info</span></h2>
		<div class="box">
			<div class="inbox">
				<p>
<?php
		}
		$sql_count = count($pieces);
		for($i = 0; $i < $sql_count; $i++)
		{
			$sql = trim($pieces[$i]);
			if(!empty($sql))
			{
				if(defined('FORUM_DEBUG'))
				{
					echo "Executing: $sql\n<br>";
					flush();
				}
				$result = $db->query($sql);
				if(!$result)
				{
					generate_admin_menu('database');
					message('Error imported backup file, the database probably has not been restored');
				}
			}
		}
		if(defined('FORUM_DEBUG'))
		{
?>
				</p>
			</div>
		</div>
	</div>
<?php
		}
	}
	if(defined('FORUM_DEBUG'))
	{
?>
	<div class="block">
	<h2 class="block2"><span><?php echo $lang_back['Restore complete'] ?></span></h2>
		<div class="box">
			<div class="inbox">
				<p>
					<a href="database.php"><?php echo $lang_common['Go back'] ?></a>
				</p>
			</div>
		</div>
	</div>
<?php
	}
	else
	{
		generate_admin_menu('database');
		message('Restore Complete');
	}
}
elseif (isset($_POST['repairall']))
{
	//repair all tables
	// Retrieve table list:
	$sql = 'SHOW TABLE STATUS';
	if (!$result = $db->query($sql))
	{
		// This makes no sense, the board would be dead... :P
		generate_admin_menu('database');
		message('Tables error, repair failed');
	}
	$tables = array();
	$counter = 0;
	while ($row = $db->fetch_assoc($result))
	{
		$counter++;
		$tables[$counter] = $row['Name'];
	}
	$tablecount = $counter;

	// Repair All
	for ($i = 1; $i <= $tablecount; $i++)
	{
		$sql = 'REPAIR TABLE ' . $tables[$i];
		if (!$result = $db->query($sql))
		{
			generate_admin_menu('database');
			message('SQL error, repair failed');
		}
	}
	generate_admin_menu('database');
	message('All tables repaired');
}
elseif (isset($_POST['optimizeall']))
{
	// Retrieve table list:
	$sql = 'SHOW TABLE STATUS';
	if (!$result = $db->query($sql))
	{
		// This makes no sense, the board would be dead... :P
		generate_admin_menu('database');
		message('Tables error, optimise failed');
	}
	$tables = array();
	$counter = 0;
	while ($row = $db->fetch_assoc($result))
	{
		$counter++;
		$tables[$counter] = $row['Name'];
	}
	$tablecount = $counter;

	// Optimize All
	for ($i = 1; $i <= $tablecount; $i++)
	{
		$sql = 'OPTIMIZE TABLE ' . $tables[$i];
		if (!$result = $db->query($sql))
		{
			generate_admin_menu('database');
			message('SQL error, optimise failed');
		}
	}
	generate_admin_menu('database');
	message('All tables optimised');
}
elseif (isset($_POST['submit']))
{
	//fix for no admin menu
	echo "<div>";
	$this_query = $_POST['this_query'];
	if (empty($this_query))
	{
		//no query error
		generate_admin_menu('database');
		message('No Query Duh!');
	}
	// Add a semi-colon to the end if there isn't one:
	if ((!strrpos($this_query, ";")) || (substr($this_query, -1) != ';'))
	{
		$this_query .= ";";
	}
	$this_query = str_replace(' #__',' '.$db->prefix,$this_query);
	// Cut into multiple queries:
	$this_query = remove_remarks($this_query);
	$queries = split_sql_file($this_query, ";");
	$queries = array_map('trim', $queries);
	//old query splitter (seems less reliable)
	//$queries = explode(';', $this_query, (count(explode(';', $this_query)) - 1));
	// For the final normal die message:
	$queriesdone = "";
	foreach($queries as $query)
	{
		if (!$query)
			continue;

		// Add a semi-colon to the end if there isn't one:
		if (!strrpos($query, ";"))
		{
			$query .= ";";
		}
		$result = $db->query($query);
		if (!$result)
		{
			//query error
			generate_admin_menu('database');
			message('SQL Error');
		}
		$queriesdone .= $query."\n";
		// Handle output of SELECT statements
		$query_words = explode(" ", $query);
		if ($db->num_rows($result))
		{
			if ($db->num_rows($result) > 500)
			{
				generate_admin_menu('database');
				message('Query result too long to be displayed');
			}
			// Remember the number of fields (aka columns) and the number of rows:
			$field_count = num_fields($result);
			$row_count = $db->num_rows($result);
			echo '<div><div class="linkst"><div class="inbox"><div><a href="javascript:history.go(-1)" />Go back</a></div></div></div>';
			echo '<div class="blocktable"><h2 class="block2"><span>';
			echo pun_htmlspecialchars($query);
			echo '</span></h2><div class="box"><div class="inbox"><div class="scrollbox" style="max-height: 500px"><table cellspacing="0"><thead><tr>';
			// The field header:
			for ($i = 0; $i < $field_count; $i++)
			{
				$field[$i] = field_name($i, $result);
				echo '<th>';
				echo pun_htmlspecialchars($field[$i]);
				echo '</th>';
			}
			echo '</tr></thead><tbody>';
			// OK, we have the data... let's put it out to a thingy!
			while ($row = $db->fetch_assoc($result))
			{
				echo '<tr>';
				for ($i = 0; $i < $field_count; $i++)
				{
					echo '<td>';
					$temp = isset($row[$field[$i]]) ? pun_htmlspecialchars($row[$field[$i]]) : '&nbsp;';
					echo $temp;
					echo '</td>';
				}
				echo "</tr>";
			}
			echo '</tbody></table></div></div></div></div>';
		}
		elseif (substr(trim($query), 0, 6) == 'SELECT')
		{
			echo '<div><div class="linkst"><div class="inbox"><div><a href="javascript:history.go(-1)" />Go back</a></div></div></div>';
			echo '<div class="block"><h2 class="block2"><span>'.pun_htmlspecialchars($query).'</span></h2><div class="box"><div class="inbox"><p>';
			echo "No data found";
			echo '</p></div></div></div>';

		}
	}
	echo '<div class="block"><h2 class="block2"><span>Queries Done</span></h2><div class="box"><div class="inbox"><p>';
	echo nl2br(pun_htmlspecialchars($queriesdone));
	echo '</p></div></div></div>';
	echo '<div><div class="linkst"><div class="inbox"><div><a href="javascript:history.go(-1)" />Go back</a></div></div></div>';
}
else
{
	$action = isset($_GET['action']) ? $_GET['action'] : null;
	$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['Database']);
	define('FORUM_ACTIVE_PAGE', 'admin');
	require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('database');
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $lang_back['Backup options'] ?></h3>
	</div>
	<div class="panel-body">
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<fieldset>
				<p><?php echo $lang_back['Backup info 1'] ?></p>
				<table class="table">
					<tr>
						<th class="col-2"><?php echo $lang_back['Backup type'] ?></th>
						<td>
							<label class="conl"><input type="radio" name="backup_type" value="full" checked="checked" />&#160;<strong><?php echo $lang_back['Full'] ?></strong></label>
							<label class="conl"><input type="radio" name="backup_type" value="structure" />&#160;<strong><?php echo $lang_back['Structure only'] ?></strong></label>
							<label class="conl"><input type="radio" name="backup_type" value="data" />&#160;<strong><?php echo $lang_back['Data only'] ?></strong></label>
							<span class="help-block"><?php echo $lang_back['Backup info 2'] ?></span>
						</td>
					</tr>
					<tr>
						<th><?php echo $lang_back['Gzip compression'] ?></th>
						<td>
							<label class="conl"><input type="radio" name="gzipcompress" value="1" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
							<label class="conl"><input type="radio" name="gzipcompress" value="0" checked="checked" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
						</td>
					</tr>
				</table>
			</fieldset>
			<p class="control-group"><input class="btn btn-primary" type="submit" name="backupstart" value="<?php echo $lang_back['Start backup'] ?>" class="mainoption" /></p>
		</form>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $lang_back['Restore options'] ?></h3>
	</div>
	<div class="panel-body">
		<form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<fieldset>
				<p><?php echo $lang_back['Restore info 1'] ?></p>
				<table class="table">
					<tr>
						<th class="col-2"><?php echo $lang_back['Restore from file'] ?></th>
						<td>
							<input type="file" name="backup_file" />
							<input class="btn btn-primary" type="submit" name="restore_start" value="<?php echo $lang_back['Start restore'] ?>" class="mainoption" />
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>
</div>
<div class="alert alert-danger alert-update">
	<h4><?php echo $lang_back['Warning'] ?></h4>
	<p><?php echo $lang_back['Warning info'] ?></p>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo $lang_back['Run SQL query'] ?></h3>
	</div>
	<div class="panel-body">
		<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
			<fieldset>
				<p><?php echo $lang_back['Run info 1'] ?></p>
				<textarea class="form-control" placeholder="<?php echo $lang_back['SQL Query'] ?>" name="this_query" rows="5" cols="50"></textarea>
			</fieldset>
			<div class="control-group"><input class="btn btn-primary" type="submit" name="submit" value="<?php echo $lang_back['Run query'] ?>" /></div>
			<fieldset>
				<h3><?php echo $lang_back['Additional functions'] ?></h3>
				<p><?php echo $lang_back['Additional info 1'] ?></p>
				<input class="btn btn-primary" type="submit" name="repairall" value="<?php echo $lang_back['Repair all tables'] ?>" />&nbsp;<input class="btn btn-primary" type="submit" name="optimizeall" value="<?php echo $lang_back['Optimise all tables'] ?>" />
			</fieldset>
		</form>
	</div>
</div>
<?php
}
require FORUM_ROOT.'backstage/footer.php';