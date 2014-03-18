<?php

/**
 * Copyright (C) 2013-2014 ModernBB Group
 * Based on code by Mpok copyright (C) 2010-2013 Mpok
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */
 
define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';

if ($pun_user['g_read_board'] == '0')
	message($lang['No view']);

if ($pun_config['o_smilies'] == '1')
{
	// Retrieve smilies
	require_once FORUM_ROOT.'include/parser.php';

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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang['lang_identifier'] ?>" lang="<?php echo $lang['lang_identifier'] ?>" dir="<?php echo $lang['lang_direction'] ?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo $lang['all_smilies'] ?></title>
  <style type="text/css">
  body {
	background-color: #dddddd;
  }
  p {
	margin-top: 0.5em;
	text-align: center;
  }
  </style>
  <script type="text/javascript">
/* <![CDATA[ */
	var textarea = window.opener.document.getElementById('req_message');
	var smilies_tab = document.createElement("div");
	smilies_tab.id = "smilies_tab";

	var smilies = new Array();
<?php
	$stop = count($smiley_text);
	for ($i = 0; $i < $stop; ++$i)
		echo "\t".'smilies["'.stripslashes($smiley_text[$i]).'"] = "'.$smiley_img[$i].'";'."\n";
?>

	function addSmiley(src, txt)
	{
		var i = document.createElement('img');
		var htxt = txt;
		htxt = htxt.replace(new RegExp(/&amp;/g), '&');
		htxt = htxt.replace(new RegExp(/&quot;/g), '"');
		htxt = htxt.replace(new RegExp(/&lt;/g), '<');
		htxt = htxt.replace(new RegExp(/&gt;/g), '>');
		i.src = 'img/smilies/' + src;
		i.title = htxt;
		i.style.padding = '0.2em';
		i.onclick = function() { try { insertSmiley(htxt) } catch (e) { } return false };
		i.tabIndex = 400;
		smilies_tab.appendChild(i);
	}

	function insertSmiley(txt)
	{
		encloseSelection(txt, '');
		window.opener.focus();
		window.close();
	}

	function encloseSelection(prefix, suffix, fn)
	{
		textarea.focus();
		var start, end, sel, scrollPos, subst;

		if (typeof(window.opener.document["selection"]) != "undefined")
			sel = window.opener.document.selection.createRange().text;
		else if (typeof(textarea["setSelectionRange"]) != "undefined")
		{
			start = textarea.selectionStart;
			end = textarea.selectionEnd;
			scrollPos = textarea.scrollTop;
			sel = textarea.value.substring(start, end);
		}

		if (sel.match(/ $/))
		{
			// exclude ending space char, if any
			sel = sel.substring(0, sel.length - 1);
			suffix = suffix + " ";
		}

		if (typeof(fn) == 'function')
			var res = (sel) ? fn(sel) : fn('');
		else
			var res = (sel) ? sel : '';

		subst = prefix + res + suffix;

		if (typeof(window.opener.document["selection"]) != "undefined")
		{
			var range = window.opener.document.selection.createRange().text = subst;
			textarea.caretPos -= suffix.length;
		}
		else if (typeof(textarea["setSelectionRange"]) != "undefined")
		{
			textarea.value = textarea.value.substring(0, start) + subst + textarea.value.substring(end);
			if (sel)
				textarea.setSelectionRange(start + subst.length, start + subst.length);
			else
				textarea.setSelectionRange(start + prefix.length, start + prefix.length);
			textarea.scrollTop = scrollPos;
		}
	}
/* ]]> */
  </script>
</head>
<body>
 <div id="smilies">
  <script type="text/javascript">
/* <![CDATA[ */
	for (var code in smilies)
		addSmiley(smilies[code], code);
	document.getElementById('smilies').appendChild(smilies_tab);
/* ]]> */
  </script>
 </div>
 <p><input type="button" name="btn_cancel" value="<?php echo $lang['Cancel'] ?>" onclick="window.close();" /></p>
</body>
</html>
<?php
}
else
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang['lang_identifier'] ?>" lang="<?php echo $lang['lang_identifier'] ?>" dir="<?php echo $lang['lang_direction'] ?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo $lang['all_smilies'] ?></title>
  <style type="text/css">
  body {
	text-align: center;
	color: #000000;
	background-color: #dddddd;
  }
  </style>
</head>
<body>
 <p><?php echo $lang['Smilies'].' '.$lang['off'] ?></p>
 <p><input type="button" name="btn_cancel" value="<?php echo $lang['Cancel'] ?>" onclick="window.close();" /></p>
</body>
</html>
<?php
}
?>
