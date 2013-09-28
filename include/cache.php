<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

//
// Generate the config cache PHP script
//
function generate_config_cache()
{
	global $db;

	// Get the forum config from the DB
	$result = $db->query('SELECT * FROM '.$db->prefix.'config', true) or error('Unable to fetch forum config', __FILE__, __LINE__, $db->error());

	$output = array();
	while ($cur_config_item = $db->fetch_row($result))
		$output[$cur_config_item[0]] = $cur_config_item[1];

	// Output config as PHP code
	$content = '<?php'."\n\n".'define(\'FORUM_CONFIG_LOADED\', 1);'."\n\n".'$pun_config = '.var_export($output, true).';'."\n\n".'?>';
	fluxbb_write_cache_file('cache_config.php', $content);
}


//
// Generate the bans cache PHP script
//
function generate_bans_cache()
{
	global $db;

	// Get the ban list from the DB
	$result = $db->query('SELECT * FROM '.$db->prefix.'bans', true) or error('Unable to fetch ban list', __FILE__, __LINE__, $db->error());

	$output = array();
	while ($cur_ban = $db->fetch_assoc($result))
		$output[] = $cur_ban;

	// Output ban list as PHP code
	$content = '<?php'."\n\n".'define(\'FORUM_BANS_LOADED\', 1);'."\n\n".'$pun_bans = '.var_export($output, true).';'."\n\n".'?>';
	fluxbb_write_cache_file('cache_bans.php', $content);
}


//
// Generate the ranks cache PHP script
//
function generate_ranks_cache()
{
	global $db;

	// Get the rank list from the DB
	$result = $db->query('SELECT * FROM '.$db->prefix.'ranks ORDER BY min_posts', true) or error('Unable to fetch rank list', __FILE__, __LINE__, $db->error());

	$output = array();
	while ($cur_rank = $db->fetch_assoc($result))
		$output[] = $cur_rank;

	// Output ranks list as PHP code
	$content = '<?php'."\n\n".'define(\'FORUM_RANKS_LOADED\', 1);'."\n\n".'$pun_ranks = '.var_export($output, true).';'."\n\n".'?>';
	fluxbb_write_cache_file('cache_ranks.php', $content);
}


//
// Generate the censoring cache PHP script
//
function generate_censoring_cache()
{
	global $db;

	$result = $db->query('SELECT search_for, replace_with FROM '.$db->prefix.'censoring') or error('Unable to fetch censoring list', __FILE__, __LINE__, $db->error());
	$num_words = $db->num_rows($result);

	$search_for = $replace_with = array();
	for ($i = 0; $i < $num_words; $i++)
	{
		list($search_for[$i], $replace_with[$i]) = $db->fetch_row($result);
		$search_for[$i] = '%(?<=[^\p{L}\p{N}])('.str_replace('\*', '[\p{L}\p{N}]*?', preg_quote($search_for[$i], '%')).')(?=[^\p{L}\p{N}])%iu';
	}

	// Output censored words as PHP code
	$content = '<?php'."\n\n".'define(\'FORUM_CENSOR_LOADED\', 1);'."\n\n".'$search_for = '.var_export($search_for, true).';'."\n\n".'$replace_with = '.var_export($replace_with, true).';'."\n\n".'?>';
	fluxbb_write_cache_file('cache_censoring.php', $content);
}


//
// Generate the stopwords cache PHP script
//
function generate_stopwords_cache()
{
	$stopwords = array();

	$d = dir(FORUM_ROOT.'lang');
	while (($entry = $d->read()) !== false)
	{
		if ($entry{0} == '.')
			continue;

		if (is_dir(FORUM_ROOT.'lang/'.$entry) && file_exists(FORUM_ROOT.'lang/'.$entry.'/stopwords.txt'))
			$stopwords = array_merge($stopwords, file(FORUM_ROOT.'lang/'.$entry.'/stopwords.txt'));
	}
	$d->close();

	// Tidy up and filter the stopwords
	$stopwords = array_map('pun_trim', $stopwords);
	$stopwords = array_filter($stopwords);

	// Output stopwords as PHP code
	$content = '<?php'."\n\n".'$cache_id = \''.generate_stopwords_cache_id().'\';'."\n".'if ($cache_id != generate_stopwords_cache_id()) return;'."\n\n".'define(\'FORUM_STOPWORDS_LOADED\', 1);'."\n\n".'$stopwords = '.var_export($stopwords, true).';'."\n\n".'?>';
	fluxbb_write_cache_file('cache_stopwords.php', $content);
}


//
// Load some information about the latest registered users
//
function generate_users_info_cache()
{
	global $db;

	$stats = array();

	$result = $db->query('SELECT COUNT(id)-1 FROM '.$db->prefix.'users WHERE group_id!='.FORUM_UNVERIFIED) or error('Unable to fetch total user count', __FILE__, __LINE__, $db->error());
	$stats['total_users'] = $db->result($result);

	$result = $db->query('SELECT id, username FROM '.$db->prefix.'users WHERE group_id!='.FORUM_UNVERIFIED.' ORDER BY registered DESC LIMIT 1') or error('Unable to fetch newest registered user', __FILE__, __LINE__, $db->error());
	$stats['last_user'] = $db->fetch_assoc($result);

	// Output users info as PHP code
	$content = '<?php'."\n\n".'define(\'FORUM_USERS_INFO_LOADED\', 1);'."\n\n".'$stats = '.var_export($stats, true).';'."\n\n".'?>';
	fluxbb_write_cache_file('cache_users_info.php', $content);
}


//
// Generate the admins cache PHP script
//
function generate_admins_cache()
{
	global $db;

	// Get admins from the DB
	$result = $db->query('SELECT id FROM '.$db->prefix.'users WHERE group_id='.FORUM_ADMIN) or error('Unable to fetch users info', __FILE__, __LINE__, $db->error());

	$output = array();
	while ($row = $db->fetch_row($result))
		$output[] = $row[0];

	// Output admin list as PHP code
	$content = '<?php'."\n\n".'define(\'FORUM_ADMINS_LOADED\', 1);'."\n\n".'$pun_admins = '.var_export($output, true).';'."\n\n".'?>';
	fluxbb_write_cache_file('cache_admins.php', $content);
}


//
// Safely write out a cache file.
//
function fluxbb_write_cache_file($file, $content)
{
	$fh = @fopen(FORUM_CACHE_DIR.$file, 'wb');
	if (!$fh)
		error('Unable to write cache file '.pun_htmlspecialchars($file).' to cache directory. Please make sure PHP has write access to the directory \''.pun_htmlspecialchars(FORUM_CACHE_DIR).'\'', __FILE__, __LINE__);

	flock($fh, LOCK_EX);
	ftruncate($fh, 0);

	fwrite($fh, $content);

	flock($fh, LOCK_UN);
	fclose($fh);

	if (function_exists('apc_delete_file'))
		@apc_delete_file(FORUM_CACHE_DIR.$file);
}


//
// Delete all feed caches
//
function clear_feed_cache()
{
	$d = dir(FORUM_CACHE_DIR);
	while (($entry = $d->read()) !== false)
	{
		if (substr($entry, 0, 10) == 'cache_feed' && substr($entry, -4) == '.php')
			@unlink(FORUM_CACHE_DIR.$entry);
	}
	$d->close();
}

//
function write_cache($cache, $text)
{
	$fh = @fopen(FORUM_CACHE_DIR.$cache, 'wb');
	if (!$fh)
		error('Unable to write configuration cache file to cache directory. Please make sure PHP has write access to the directory \''.pun_htmlspecialchars(FORUM_CACHE_DIR).'\'', __FILE__, __LINE__);		
	fwrite($fh, $text);
	fclose($fh);
}

function generate_ftb_cache($form = 'form')
{
	global $db, $pun_config, $smilies;

	$output = '';

	// Tags cache (for new bbcode)
	if ($form == 'tags')
	{
		$output_search = $output_check = '<?php'."\n";

		// Retrieve new bbcode tags
		$result = $db->query('SELECT code, func FROM '.$db->prefix.'toolbar_tags WHERE code NOT IN (\'b\', \'u\', \'i\', \'s\', \'h\', \'color\', \'quote\', \'code\', \'img\', \'url\', \'email\', \'list\', \'*\', \'\')') or error('Unable to retrieve new bbcode tags', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result))
		{
			$tags = array();
			$tags_prompt = array();
			while ($tag = $db->fetch_assoc($result))
			{
				$tags[] = $tag['code'];
				if ($tag['func'] != 0)
					$tags_prompt[] = $tag['code'];
			}

			// Output for checking
			$output_check .= 'if (preg_match(\'%(?:\[/?(?:'.implode($tags, '|').')\]';
			if (!empty($tags_prompt))
				$output_check .= '|\[(?:'.implode($tags_prompt, '|').')=';
			$output_check .= ')%i\', $username))'."\n";
			$output_check .= "\t".'$errors[] = $lang_prof_reg[\'Username BBCode\'];'."\n";

			// Output for search
			$output_search .= '$text = preg_replace(\'%\[/?('.implode($tags, '|').')(?:\=[^\]]*)?\]%\', \' \', $text);'."\n";
		}

		write_cache('cache_toolbar_tag_check.php', $output_check);
		write_cache('cache_toolbar_tag_search.php', $output_search);
		return;
	}

	// Retrieve configuration
	$ftb_conf = array();
	$result = $db->query('SELECT conf_name, conf_value FROM '.$db->prefix.'toolbar_conf') or error('Unable to retrieve toolbar configuration', __FILE__, __LINE__, $db->error());
	while ($conf = $db->fetch_assoc($result))
		$ftb_conf[$conf['conf_name']] = $conf['conf_value'];

	// Checking if toolbar enabled
	if ($form == 'quickform')
	{
		$cache_file = 'cache_toolbar_quickform.php';
		if (!$ftb_conf['enable_quickform'])
		{
			write_cache($cache_file, $output);
			return;
		}
	}
	else
	{
		$cache_file = 'cache_toolbar_form.php';
		if (!$ftb_conf['enable_form'])
		{
			write_cache($cache_file, $output);
			return;
		}
	}

	// Start output JS
	$output .=
		'<script type="text/javascript" src="include/toolbar_func.js"></script>'."\n".
		'<script type="text/javascript" src="include/jscolor/jscolor.js"></script>'."\n".
		'<noscript><p><strong><?php echo $lang_common[\'enable_js\'] ?></strong></p></noscript>'."\n".
		'<script type="text/javascript">'."\n".
		'/* <![CDATA[ */'."\n";
	$output .= 
		"\t".'var tb = new toolBar(document.getElementById(\'req_message\'), \'img/toolbar/'.$ftb_conf['img_pack'].'/\', \'img/smilies/\');'."\n";

	// Retrieve buttons
	$do_smilies = false;
	$result = $db->query('SELECT name, code, enable_form, enable_quick, image, func FROM '.$db->prefix.'toolbar_tags ORDER by position') or error('Unable to retrieve toolbar buttons', __FILE__, __LINE__, $db->error());

	// Output each button
	while ($button = $db->fetch_assoc($result))
	{
		$enable = ($form == 'quickform') ? $button['enable_quick'] : $button['enable_form'];
		$name = 'bt_'.$button['name'];

		// Smilies stuff
		if ($button['name'] == 'smilies')
		{
			if (!$enable || $pun_config['o_smilies'] != '1')
				continue;
			$do_smilies = true;
			$img_smilies = $button['image'];

			// Retrieve smilies
			if (!isset($smilies))
				require FORUM_ROOT.'include/parser.php';

			// Remove duplicates (in images)
			$smiley_dups = array();
			$smiley_text = array_keys($smilies);
			$smiley_img = array_values($smilies);
			$num_smilies = count($smiley_text);
			for ($i = 0; $i < $num_smilies; ++$i)
			{
				if (in_array($smiley_img[$i], $smiley_dups))
				{
					// Unset duplicate entries
					unset($smiley_text[$i]);
					unset($smiley_img[$i]);
				}
				else
					$smiley_dups[] = $smiley_img[$i];
			}

			// Re-index the arrays
			$smiley_text = array_values($smiley_text);
			$smiley_img = array_values($smiley_img);

			// Pop-up function
			$output .= 
				"\t".'function popup_smilies()'."\n".
				"\t".'{'."\n".
				"\t\t".'document.getElementById(\'req_message\').focus();'."\n".
				"\t\t".'window.open(\'smiley_picker.php\', \'sp\', \'alwaysRaised=yes, dependent=yes, resizable=yes, location=no, width=\'+width+\', height=\'+height+\', menubar=no, status=yes, scrollbars=yes, menubar=no\');'."\n".
				"\t".'}'."\n";

			// Smilies array
			$output .= 
				"\t".'var smilies = new Array();'."\n";
			$stop = count($smiley_text);
			if ($stop > $ftb_conf['nb_smilies'])
				$more_smilies = true;
			else
				$more_smilies = false;
			// Output only first smilies
			for ($i = 0; $i < $stop && $i < $ftb_conf['nb_smilies']; ++$i)
				$output .= "\t".'smilies["'.stripslashes($smiley_text[$i]).'"] = "'.$smiley_img[$i].'";'."\n";
		}

		// Images stuff
		else if	($button['name'] == 'img')
		{
			if (!$enable || $pun_config['p_message_img_tag'] != '1')
				continue;
			$msg_1 = 'bt_'.$button['name'].'_msg_1';
			$msg_2 = 'bt_'.$button['name'].'_msg_2';
			$output .= "\t".'tb.btPrompt_2(\''.$button['image'].'\', \''.$button['code'].'\', \'<?php echo str_replace("\'","\\\'", $lang_common[\''.$name.'\']) ?>\', \'<?php echo str_replace("\'","\\\'", $lang_common[\''.$msg_1.'\']) ?>\', \'<?php echo str_replace("\'","\\\'", $lang_common[\''.$msg_2.'\']) ?>\', 1);'."\n";
		}

		// Stop if bbcode not enabled
		else if (!$enable || $pun_config['p_message_bbcode'] != '1')
				continue;

		// Color stuff
		else if	($button['name'] == 'color')
			$output .= "\t".'tb.btColor(\''.$button['image'].'\', \'<?php echo str_replace("\'","\\\'", $lang_common[\''.$name.'\']) ?>\');'."\n";

		// All other buttons
		else
			switch ($button['func'])
			{
				case '3' :
					$msg_1 = 'bt_'.$button['name'].'_msg_1';
					$output .= "\t".'tb.btPrompt_1inside(\''.$button['image'].'\', \''.$button['code'].'\', \'<?php echo str_replace("\'","\\\'", $lang_common[\''.$name.'\']) ?>\', \'<?php echo str_replace("\'","\\\'", $lang_common[\''.$msg_1.'\']) ?>\');'."\n";
					break;
				case '2' :
					$msg_1 = 'bt_'.$button['name'].'_msg_1';
					$msg_2 = 'bt_'.$button['name'].'_msg_2';
					$output .= "\t".'tb.btPrompt_2(\''.$button['image'].'\', \''.$button['code'].'\', \'<?php echo str_replace("\'","\\\'", $lang_common[\''.$name.'\']) ?>\', \'<?php echo str_replace("\'","\\\'", $lang_common[\''.$msg_1.'\']) ?>\', \'<?php echo str_replace("\'","\\\'", $lang_common[\''.$msg_2.'\']) ?>\', 0);'."\n";
					break;
				case '1' :
					$msg_1 = 'bt_'.$button['name'].'_msg_1';
					$output .= "\t".'tb.btPrompt_1(\''.$button['image'].'\', \''.$button['code'].'\', \'<?php echo str_replace("\'","\\\'", $lang_common[\''.$name.'\']) ?>\', \'<?php echo str_replace("\'","\\\'", $lang_common[\''.$msg_1.'\']) ?>\');'."\n";
					break;
				case '0' :
				default :
					$output .= "\t".'tb.btSingle(\''.$button['image'].'\', \''.$button['code'].'\', \'<?php echo str_replace("\'","\\\'", $lang_common[\''.$name.'\']) ?>\');'."\n";
					break;
			}
	}

	// Smilies (output after all buttons)
	if ($do_smilies)
	{
		$output .= 
			"\t".'tb.btSmilies(\''.$img_smilies.'\', \'<?php echo str_replace("\'","\\\'",$lang_common[\'bt_smilies\']) ?>\');'."\n".
			"\t".'tb.barSmilies(smilies);'."\n";
	}

	// Construction
	$output .= "\t".'tb.draw();'."\n";

	// Add "All smilies" link
	if ($do_smilies && $more_smilies)
		$output .= "\t".'tb.moreSmilies(\'<?php echo str_replace("\'","\\\'",$lang_common[\'all_smilies\']) ?>\');'."\n";

	// End JS
	$output .=
		'/* ]]> */'."\n".
		'</script>'."\n";

	// Writing cache file
	write_cache($cache_file, $output);
}


define('FORUM_CACHE_FUNCTIONS_LOADED', true);