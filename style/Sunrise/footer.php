<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

// Display a "mark all as read" link
$markread = '';
if (!$luna_user['is_guest'])
	$markread = '<a href="misc.php?action=markread">'.$lang['Mark as read'].'</a>';

?>
	</div>
	<div class="container">
<?php if (isset($footer_style) && ($footer_style == 'viewforum' || $footer_style == 'viewtopic') && $is_admmod) { ?>

		<div class="modcontrols">
		<?php if ($footer_style == 'viewforum') { ?>

			<a href="moderate.php?fid=<?php echo $forum_id ?>&p=<?php echo $p ?>" class="btn btn-primary btn-sm"><span class="fa fa-eye"></span> <?php echo $lang['Moderate forum'] ?></a>

		<?php } elseif ($footer_style == 'viewtopic') { ?>

			<div class="btn-toolbar"><div class="btn-group"><a href="moderate.php?fid=<?php echo $forum_id ?>&tid=<?php echo $id ?>&p=<?php echo $p ?>" class="btn btn-primary btn-sm"><span class="fa fa-eye"></span> <?php echo $lang['Moderate topic'] ?></a></div>

			<?php if($num_pages > 1) { ?>
				<div class="btn-group"><a href="moderate.php?fid=<?php echo $forum_id ?>&tid=<?php echo $id ?>&action=all" class="btn btn-primary btn-sm"><span class="fa fa-list"></span> <?php echo $lang['All'] ?></a></div>
			<?php } ?>

				<div class="btn-group"><a href="moderate.php?fid=<?php echo $forum_id ?>&move_topics=<?php echo $id ?>" class="btn btn-primary btn-sm"><span class="fa fa-arrows-alt"></span> <?php echo $lang['Move topic'] ?></a>
                
				<?php if ($cur_topic['closed'] == '1') { ?>
					<a href="moderate.php?fid=<?php echo $forum_id ?>&open=<?php echo $id ?>" class="btn btn-success btn-sm"><span class="fa fa-check"></span> <?php echo $lang['Open topic'] ?></a>
				<?php } else { ?>
					<a href="moderate.php?fid=<?php echo $forum_id ?>&close=<?php echo $id ?>" class="btn btn-danger btn-sm"><span class="fa fa-times"></span> <?php echo $lang['Close topic'] ?></a>
				<?php } ?>
                
				<?php if ($cur_topic['sticky'] == '1') { ?>
						<a href="moderate.php?fid=<?php echo $forum_id ?>&unstick=<?php echo $id ?>" class="btn btn-danger btn-sm"><span class="fa fa-thumb-tack"></span> <?php echo $lang['Unstick topic'] ?></a></div></div>
				<?php } else { ?>
						<a href="moderate.php?fid=<?php echo $forum_id ?>&stick=<?php echo $id ?>" class="btn btn-primary btn-sm"><span class="fa fa-thumb-tack"></span> <?php echo $lang['Stick topic'] ?></a></div></div>
				<?php } ?>

		<?php } ?>

<?php } ?>

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

if ($luna_config['o_feed_type'] == 1) {
		$feed_type = 'rss';
} elseif ($luna_config['o_feed_type'] == 2) {
		$feed_type = 'atom';
}

if (($luna_config['o_feed_type'] == 1 || $luna_config['o_feed_type'] == 2) && (isset($footer_style))) {
	'<span><a href="extern.php?action=feed&type='.$feed_type.$feed_id.'">'.$feed_lang.'</a></span>'."\n";
}

if (!defined('FORUM_FORM')) {
	echo $markread;
} ?>
		</div>
	</div>
</div>
<footer>
	<div class="container">
		<?php if ($luna_config['o_board_statistics'] == 1): ?>
		<div class="row stats">
			<div class="col-md-4 col-sm-6 col-xs-12 statistics">
				<div class="row">
					<div class="col-xs-6">
						<div class="statistic-item"><?php total_users() ?></div>
					</div>
					<div class="col-xs-6">
						<div class="statistic-item-stat"><?php echo $lang['No of users'] ?></div>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-6 col-xs-12 statistics">
				<div class="row">
					<div class="col-xs-6">
						<div class="statistic-item"><?php total_topics() ?></div>
					</div>
					<div class="col-xs-6">
						<div class="statistic-item-stat"><?php echo $lang['No of topics'] ?></div>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-6 col-xs-12 statistics">
				<div class="row">
					<div class="col-xs-6">
						<div class="statistic-item"><?php total_posts() ?></div>
					</div>
					<div class="col-xs-6">
						<div class="statistic-item-stat"><?php echo $lang['No of posts'] ?></div>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-6 col-xs-12 statistics">
				<div class="row">
					<div class="col-xs-6">
						<div class="statistic-item"><?php newest_user() ?></div>
					</div>
					<div class="col-xs-6">
						<div class="statistic-item-stat"><?php echo $lang['Newest user'] ?></div>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-6 col-xs-12 statistics">
				<div class="row">
					<div class="col-xs-6">
						<div class="statistic-item"><?php users_online() ?></div>
					</div>
					<div class="col-xs-6">
						<div class="statistic-item-stat">
							<div class="dropup">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<?php echo $lang['Users online'] ?> <span class="fa fa-angle-up"></span>
									<span class="sr-only">Toggle Dropdown</span>
								</a>
								<ul class="dropdown-menu" role="menu">
									<?php echo online_list() ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-sm-6 col-xs-12 statistics">
				<div class="row">
					<div class="col-xs-6">
						<div class="statistic-item"><?php guests_online() ?></div>
					</div>
					<div class="col-xs-6">
						<div class="statistic-item-stat"><?php echo $lang['Guests online'] ?></div>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<div class="copyright">
		<div class="container">
			<div class="row">
				<div class="col-sm-4 col-xs-12">
<?php
	if ($luna_config['o_show_copyright'] == '1') {
		if ($luna_config['o_copyright_type'] == '0')
			echo 'Copyright &copy; '.date(Y).' &middot '.$luna_config['o_board_title'];
		if ($luna_config['o_copyright_type'] == '1')
			echo $luna_config['o_custom_copyright'];
	}
?>
				</div>
				<div class="col-sm-4 col-xs-12"><?php if ($luna_config['o_back_to_top'] == '1'): ?><div class="text-center"><a href="#"><span class="fa fa-chevron-up"></span></a></div><?php endif; ?></div>
				<div class="col-sm-4 col-xs-12"><span class="pull-right" id="poweredby"><?php printf($lang['Powered by'], ' <a href="http://getluna.org/">Luna '.$luna_config['o_cur_version']).'</a>' ?></span></div>
			</div>
		</div>
	</div>
	<script src="include/js/jquery.js"></script>
	<script src="include/js/bootstrap.min.js"></script>
	<script src="include/js/prism.js"></script>
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