<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

?>
	</div>
	<div class="container">
<?php
// If no footer style has been specified, we use the default (only copyright/debug info)
$footer_style = isset($footer_style) ? $footer_style : NULL;

// Generate the feed links
if ($footer_style == 'index') {
	$feed_lang = ($luna_config['o_feed_type'] == '1') ? $lang['RSS active topics feed'] : $lang['Atom active topics feed'];
	$feed_id = '';
} elseif ($footer_style == 'viewforum') {
	$feed_lang = ($luna_config['o_feed_type'] == '1') ? $lang['RSS forum feed'] : $lang['Atom forum feed'];
	$feed_id = '&fid='.$forum_id;
} elseif ($footer_style == 'viewtopic') {
	$feed_lang = ($luna_config['o_feed_type'] == '1') ? $lang['RSS topic feed'] : $lang['Atom topic feed'];
	$feed_id = '&tid='.$id;
}

if ($luna_config['o_feed_type'] == 1)
	$feed_type = 'rss';
elseif ($luna_config['o_feed_type'] == 2)
	$feed_type = 'atom';

if (($luna_config['o_feed_type'] == 1 || $luna_config['o_feed_type'] == 2) && (isset($footer_style)))
	'<span><a href="extern.php?action=feed&type='.$feed_type.$feed_id.'">'.$feed_lang.'</a></span>'."\n";

?>
		</div>
	</div>
</div>
<hr />
<footer class="container">
	<div class="row">
		<div class="col-sm-4 col-xs-12">
<?php
	if ($luna_config['o_show_copyright'] == '1') {
		if ($luna_config['o_copyright_type'] == '0')
			echo 'Copyright &copy; '.date('Y').' &middot; '.$luna_config['o_board_title'];
		if ($luna_config['o_copyright_type'] == '1')
			echo $luna_config['o_custom_copyright'];
	}
?>
		</div>
		<div class="col-sm-4 col-xs-12"><?php if ($luna_config['o_back_to_top'] == '1'): ?><div class="text-center"><a href="#"><span class="fa fa-fw fa-chevron-up"></span></a></div><?php endif; ?></div>
		<div class="col-sm-4 col-xs-12"><span class="pull-right" id="poweredby"><?php printf($lang['Powered by'], ' <a href="http://getluna.org/">Luna '.$luna_config['o_cur_version'].'</a>') ?></span></div>
	</div>
</footer>
<?php if (($luna_config['o_cookie_bar'] == 1) && ($luna_user['is_guest']) && (!isset($_COOKIE['LunaCookieBar']))) { ?>
<div class="navbar navbar-inverse navbar-fixed-bottom cookie-bar">
	<div class="container">
		<p class="navbar-text"><?php echo $lang['Cookie info'] ?></p>
		<form class="navbar-form navbar-right">
			<div class="form-group">
				<div class="btn-toolbar"><a class="btn btn-link" href="http://getluna.org/docs/cookies.php"><?php echo $lang['More info'] ?></a><a class="btn btn-default" href="index.php?action=disable_cookiebar"><?php echo $lang['Do not show again'] ?></a></div>
			</div>
		</form>
	</div>
</div>
<style>
body { margin-bottom: 40px; }
@media screen and (max-width: 767px) { body { margin-bottom: 80px; } }
</style>
<?php
}

// Display debug info (if enabled/defined)
if (defined('FORUM_DEBUG')) {
	echo '<p id="debug">[ ';

	// Calculate script generation time
	$time_diff = sprintf('%.3f', get_microtime() - $luna_start);
	echo sprintf($lang['Querytime'], $time_diff, $db->get_num_queries());

	if (function_exists('memory_get_usage')) {
		echo ' - '.sprintf($lang['Memory usage'], file_size(memory_get_usage()));

		if (function_exists('memory_get_peak_usage'))
			echo ' '.sprintf($lang['Peak usage'], file_size(memory_get_peak_usage()));
	}

	echo ' ]</p>'."\n";
}


// End the transaction
$db->end_transaction();

// Display executed queries (if enabled)
if (defined('FORUM_SHOW_QUERIES'))
	display_saved_queries();


// Close the db connection (and free up any result data)
$db->close();
?>
		</div>
	</body>
</html>