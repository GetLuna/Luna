<?php
if (file_exists(FORUM_ROOT.'lang/'.$pun_user['language'].'/fluxtoolbar.php'))
	require_once FORUM_ROOT.'lang/'.$pun_user['language'].'/fluxtoolbar.php';
else
	require_once FORUM_ROOT.'lang/English/fluxtoolbar.php';
?>
<script type="text/javascript" src="include/toolbar_func.js"></script>
<script type="text/javascript" src="include/jscolor/jscolor.js"></script>
<noscript><p><strong><?php echo $lang_ftb['enable_js'] ?></strong></p></noscript>
<script type="text/javascript">
/* <![CDATA[ */
	var tb = new toolBar(document.getElementById('req_message'), 'img/toolbar/smooth/', 'img/smilies/');
	function popup_smilies()
	{
		document.getElementById('req_message').focus();
		var width = 240;
		var height = 200;
		window.open('smiley_picker.php', 'sp', 'alwaysRaised=yes, dependent=yes, resizable=yes, location=no, width='+width+', height='+height+', menubar=no, status=yes, scrollbars=yes, menubar=no');
	}
	var smilies = new Array();
	smilies[":)"] = "smile.png";
	smilies[":|"] = "neutral.png";
	smilies[":("] = "sad.png";
	smilies[":D"] = "big_smile.png";
	smilies[":o"] = "yikes.png";
	smilies[";)"] = "wink.png";
	smilies[":/"] = "hmm.png";
	smilies[":P"] = "tongue.png";
	smilies[":lol:"] = "lol.png";
	smilies[":mad:"] = "mad.png";
	smilies[":rolleyes:"] = "roll.png";
	smilies[":cool:"] = "cool.png";
	tb.btSingle('bt_bold.png', 'b', '<?php echo str_replace("'","\'", $lang_ftb['bt_bold']) ?>');
	tb.btSingle('bt_italic.png', 'i', '<?php echo str_replace("'","\'", $lang_ftb['bt_italic']) ?>');
	tb.btSingle('bt_underline.png', 'u', '<?php echo str_replace("'","\'", $lang_ftb['bt_underline']) ?>');
	tb.btSingle('bt_strike.png', 's', '<?php echo str_replace("'","\'", $lang_ftb['bt_strike']) ?>');
	tb.btSingle('bt_sup.png', 'sup', '<?php echo str_replace("'","\'", $lang_ftb['bt_sup']) ?>');
	tb.btSingle('bt_sub.png', 'sub', '<?php echo str_replace("'","\'", $lang_ftb['bt_sub']) ?>');
	tb.btSingle('bt_size_plus.png', 'h', '<?php echo str_replace("'","\'", $lang_ftb['bt_heading']) ?>');
	tb.btSingle('bt_align_left.png', 'left', '<?php echo str_replace("'","\'", $lang_ftb['bt_left']) ?>');
	tb.btSingle('bt_align_right.png', 'right', '<?php echo str_replace("'","\'", $lang_ftb['bt_right']) ?>');
	tb.btSingle('bt_align_center.png', 'center', '<?php echo str_replace("'","\'", $lang_ftb['bt_center']) ?>');
	tb.btSingle('bt_align_justify.png', 'justify', '<?php echo str_replace("'","\'", $lang_ftb['bt_justify']) ?>');
	tb.btColor('bt_color.png', '<?php echo str_replace("'","\'", $lang_ftb['bt_color']) ?>');
	tb.btSingle('bt_quote.png', 'q', '<?php echo str_replace("'","\'", $lang_ftb['bt_q']) ?>');
	tb.btPrompt_1('bt_acronym.png', 'acronym', '<?php echo str_replace("'","\'", $lang_ftb['bt_acronym']) ?>', '<?php echo str_replace("'","\'", $lang_ftb['bt_acronym_msg_1']) ?>');
	tb.btPrompt_2('bt_img.png', 'img', '<?php echo str_replace("'","\'", $lang_ftb['bt_img']) ?>', '<?php echo str_replace("'","\'", $lang_ftb['bt_img_msg_1']) ?>', '<?php echo str_replace("'","\'", $lang_ftb['bt_img_msg_2']) ?>', 1);
	tb.btSingle('bt_pre.png', 'code', '<?php echo str_replace("'","\'", $lang_ftb['bt_code']) ?>');
	tb.btPrompt_1('bt_bquote.png', 'quote', '<?php echo str_replace("'","\'", $lang_ftb['bt_quote']) ?>', '<?php echo str_replace("'","\'", $lang_ftb['bt_quote_msg_1']) ?>');
	tb.btPrompt_2('bt_link.png', 'url', '<?php echo str_replace("'","\'", $lang_ftb['bt_link']) ?>', '<?php echo str_replace("'","\'", $lang_ftb['bt_link_msg_1']) ?>', '<?php echo str_replace("'","\'", $lang_ftb['bt_link_msg_2']) ?>', 0);
	tb.btPrompt_2('bt_email.png', 'email', '<?php echo str_replace("'","\'", $lang_ftb['bt_email']) ?>', '<?php echo str_replace("'","\'", $lang_ftb['bt_email_msg_1']) ?>', '<?php echo str_replace("'","\'", $lang_ftb['bt_email_msg_2']) ?>', 0);
	tb.btPrompt_1inside('bt_video.png', 'video', '<?php echo str_replace("'","\'", $lang_ftb['bt_video']) ?>', '<?php echo str_replace("'","\'", $lang_ftb['bt_video_msg_1']) ?>');
	tb.btSingle('bt_li.png', '*', '<?php echo str_replace("'","\'", $lang_ftb['bt_li']) ?>');
	tb.btPrompt_1('bt_ul.png', 'list', '<?php echo str_replace("'","\'", $lang_ftb['bt_list']) ?>', '<?php echo str_replace("'","\'", $lang_ftb['bt_list_msg_1']) ?>');
	tb.btSmilies('bt_smilies.png', '<?php echo str_replace("'","\'",$lang_ftb['bt_smilies']) ?>');
	tb.barSmilies(smilies);
	tb.draw();
	tb.moreSmilies('<?php echo str_replace("'","\'",$lang_ftb['all_smilies']) ?>');
/* ]]> */
</script>
