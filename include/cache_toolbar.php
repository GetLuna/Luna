<?php
/***********************************************************************

  Copyright (C) 2010-2011 Mpok
  based on code Copyright (C) 2006 Vincent Garnier
  License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher

************************************************************************/

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

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

	// Include the fluxtoolbar language files
	$output .= '<?php'."\n".
		'if (file_exists(FORUM_ROOT.\'lang/\'.$pun_user[\'language\'].\'/fluxtoolbar.php\'))'."\n".
		"\t".'require_once FORUM_ROOT.\'lang/\'.$pun_user[\'language\'].\'/fluxtoolbar.php\';'."\n".
		'else'."\n".
		"\t".'require_once FORUM_ROOT.\'lang/English/fluxtoolbar.php\';'."\n".
		'?>'."\n";

	// Start output JS
	$output .=
		'<script type="text/javascript" src="include/toolbar_func.js"></script>'."\n".
		'<script type="text/javascript" src="include/jscolor/jscolor.js"></script>'."\n".
		'<noscript><p><strong><?php echo $lang_ftb[\'enable_js\'] ?></strong></p></noscript>'."\n".
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
				"\t\t".'var width = '.$ftb_conf['pop_up_width'].';'."\n".
				"\t\t".'var height = '.$ftb_conf['pop_up_height'].';'."\n".
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
			$output .= "\t".'tb.btPrompt_2(\''.$button['image'].'\', \''.$button['code'].'\', \'<?php echo str_replace("\'","\\\'", $lang_ftb[\''.$name.'\']) ?>\', \'<?php echo str_replace("\'","\\\'", $lang_ftb[\''.$msg_1.'\']) ?>\', \'<?php echo str_replace("\'","\\\'", $lang_ftb[\''.$msg_2.'\']) ?>\', 1);'."\n";
		}

		// Stop if bbcode not enabled
		else if (!$enable || $pun_config['p_message_bbcode'] != '1')
				continue;

		// Color stuff
		else if	($button['name'] == 'color')
			$output .= "\t".'tb.btColor(\''.$button['image'].'\', \'<?php echo str_replace("\'","\\\'", $lang_ftb[\''.$name.'\']) ?>\');'."\n";

		// All other buttons
		else
			switch ($button['func'])
			{
				case '3' :
					$msg_1 = 'bt_'.$button['name'].'_msg_1';
					$output .= "\t".'tb.btPrompt_1inside(\''.$button['image'].'\', \''.$button['code'].'\', \'<?php echo str_replace("\'","\\\'", $lang_ftb[\''.$name.'\']) ?>\', \'<?php echo str_replace("\'","\\\'", $lang_ftb[\''.$msg_1.'\']) ?>\');'."\n";
					break;
				case '2' :
					$msg_1 = 'bt_'.$button['name'].'_msg_1';
					$msg_2 = 'bt_'.$button['name'].'_msg_2';
					$output .= "\t".'tb.btPrompt_2(\''.$button['image'].'\', \''.$button['code'].'\', \'<?php echo str_replace("\'","\\\'", $lang_ftb[\''.$name.'\']) ?>\', \'<?php echo str_replace("\'","\\\'", $lang_ftb[\''.$msg_1.'\']) ?>\', \'<?php echo str_replace("\'","\\\'", $lang_ftb[\''.$msg_2.'\']) ?>\', 0);'."\n";
					break;
				case '1' :
					$msg_1 = 'bt_'.$button['name'].'_msg_1';
					$output .= "\t".'tb.btPrompt_1(\''.$button['image'].'\', \''.$button['code'].'\', \'<?php echo str_replace("\'","\\\'", $lang_ftb[\''.$name.'\']) ?>\', \'<?php echo str_replace("\'","\\\'", $lang_ftb[\''.$msg_1.'\']) ?>\');'."\n";
					break;
				case '0' :
				default :
					$output .= "\t".'tb.btSingle(\''.$button['image'].'\', \''.$button['code'].'\', \'<?php echo str_replace("\'","\\\'", $lang_ftb[\''.$name.'\']) ?>\');'."\n";
					break;
			}
	}

	// Smilies (output after all buttons)
	if ($do_smilies)
	{
		$output .= 
			"\t".'tb.btSmilies(\''.$img_smilies.'\', \'<?php echo str_replace("\'","\\\'",$lang_ftb[\'bt_smilies\']) ?>\');'."\n".
			"\t".'tb.barSmilies(smilies);'."\n";
	}

	// Construction
	$output .= "\t".'tb.draw();'."\n";

	// Add "All smilies" link
	if ($do_smilies && $more_smilies)
		$output .= "\t".'tb.moreSmilies(\'<?php echo str_replace("\'","\\\'",$lang_ftb[\'all_smilies\']) ?>\');'."\n";

	// End JS
	$output .=
		'/* ]]> */'."\n".
		'</script>'."\n";

	// Writing cache file
	write_cache($cache_file, $output);
}
?>
