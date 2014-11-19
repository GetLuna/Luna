<?php

/*
 * Copyright (C) 2013-2014 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://modernbb.be/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod']) {
    header("Location: ../login.php");
}

if (isset($_POST['form_sent'])) {
	confirm_referrer('backstage/index.php', $lang['Bad HTTP Referer message']);
	
	$form = array(
		'admin_note'			=> luna_trim($_POST['form']['admin_note'])
	);

	foreach ($form as $key => $input) {
		// Only update values that have changed
		if (array_key_exists('o_'.$key, $luna_config) && $luna_config['o_'.$key] != $input) {
			if ($input != '' || is_int($input))
				$value = '\''.$db->escape($input).'\'';
			else
				$value = 'NULL';

			$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$value.' WHERE conf_name=\'o_'.$db->escape($key).'\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());
		}
	}

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();
	clear_feed_cache();

	redirect('backstage/index.php?saved=true');
}

// Collect some statistics from the database
if (file_exists(FORUM_CACHE_DIR.'cache_users_info.php'))
	include FORUM_CACHE_DIR.'cache_users_info.php';

if (!defined('FORUM_USERS_INFO_LOADED')) {
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_users_info_cache();
	require FORUM_CACHE_DIR.'cache_users_info.php';
}

if (file_exists(FORUM_CACHE_DIR.'cache_update.php'))
	include FORUM_CACHE_DIR.'cache_update.php';

if ((!defined('FORUM_UPDATE_LOADED') || ($last_check_time > time() + (60 * 60 * 24)))) {
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_update_cache();
	require FORUM_CACHE_DIR.'cache_update.php';
}

$result = $db->query('SELECT SUM(num_topics), SUM(num_posts) FROM '.$db->prefix.'forums') or error('Unable to fetch topic/post count', __FILE__, __LINE__, $db->error());
list($stats['total_topics'], $stats['total_posts']) = array_map('intval', $db->fetch_row($result));

if ($stats['total_posts'] == 0)
	$stats['total_posts'] == '0';

if ($stats['total_topics'] == 0)
	$stats['total_topics'] == '0';

$latest_version = $update_cache;

$action = isset($_GET['action']) ? $_GET['action'] : null;
$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Admin'], $lang['Index']);
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('backstage', 'index');

if (isset($_GET['saved']))
	echo '<div class="alert alert-success"><h4>'.$lang['Settings saved'].'</h4></div>';

if (file_exists('../z.txt') && ($luna_config['o_notifications'] == '1' || $luna_config['o_forum_new_style'] == '1' || $luna_config['o_reading_list'] == '1')) {
?>
<div class="alert alert-danger">
	<h4>zSettings enabled!</h4>
	We've found out that some zSettings have been enabled. These settings control feature that are still in major development, might not work at all and/or can possibly corrupt your forum. We strongly recommend you to use these features only when necessary (for example, when you're developing Luna). Otherwise, you can disable them in <span class="fa fa-cog"></span> Settings > <span class="fa fa-cogs"></span> zSettings.
</div>
<?php } ?>
<div class="row">
	<div class="col-sm-8">
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo $lang['New reports head'] ?><span class="pull-right"><a class="btn btn-primary" href="reports.php"><?php echo $lang['View all'] ?></a></span></h3>
					</div>
					<table class="table">
						<thead>
							<tr>
								<th class="col-lg-3"><?php echo $lang['Reported by'] ?></th>
								<th class="col-lg-3"><?php echo $lang['Date and time'] ?></th>
								<th class="col-lg-6"><?php echo $lang['Message'] ?></th>
							</tr>
						</thead>
						<tbody>
<?php

$result = $db->query('SELECT r.id, r.topic_id, r.forum_id, r.reported_by, r.created, r.message, p.id AS pid, t.subject, f.forum_name, u.username AS reporter FROM '.$db->prefix.'reports AS r LEFT JOIN '.$db->prefix.'posts AS p ON r.post_id=p.id LEFT JOIN '.$db->prefix.'topics AS t ON r.topic_id=t.id LEFT JOIN '.$db->prefix.'forums AS f ON r.forum_id=f.id LEFT JOIN '.$db->prefix.'users AS u ON r.reported_by=u.id WHERE r.zapped IS NULL ORDER BY created DESC') or error('Unable to fetch report list', __FILE__, __LINE__, $db->error());

if ($db->num_rows($result)) {
	while ($cur_report = $db->fetch_assoc($result)) {
		$reporter = ($cur_report['reporter'] != '') ? '<a href="../profile.php?id='.$cur_report['reported_by'].'">'.luna_htmlspecialchars($cur_report['reporter']).'</a>' : $lang['Deleted user'];
		$forum = ($cur_report['forum_name'] != '') ? '<span><a href="../viewforum.php?id='.$cur_report['forum_id'].'">'.luna_htmlspecialchars($cur_report['forum_name']).'</a></span>' : '<span>'.$lang['Deleted'].'</span>';
		$topic = ($cur_report['subject'] != '') ? '<span> <span class="divider">/</span> <a href="../viewtopic.php?id='.$cur_report['topic_id'].'">'.luna_htmlspecialchars($cur_report['subject']).'</a></span>' : '<span>»&#160;'.$lang['Deleted'].'</span>';
		$post = str_replace("\n", '<br />', luna_htmlspecialchars($cur_report['message']));
		$post_id = ($cur_report['pid'] != '') ? '<span><a href="viewtopic.php?pid='.$cur_report['pid'].'#p'.$cur_report['pid'].'">'.sprintf($lang['Post ID'], $cur_report['pid']).'</a></span>' : '<span>'.$lang['Deleted'].'</span>';
		$report_location = array($forum, $topic, $post_id);

?>
							<tr>
								<td><?php printf($reporter) ?></td>
								<td><?php printf(format_time($cur_report['created'])) ?></td>
								<td><?php echo $post ?></td>
							</tr>
<?php

	}
} else {

?>
								<tr>
									<td colspan="4"><?php echo $lang['No new reports'] ?></td>
								</tr>
<?php

}

?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="col-lg-5">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo $lang['Backup head'] ?></h3>
					</div>
					<div class="panel-body">
						<a class="btn btn-block btn-primary" href="database.php"><?php echo $lang['Backup button'] ?></a>
					</div>
				 </div>
			</div>
			<div class="col-lg-7">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo $lang['Statistics head'] ?></h3>
					</div>
					<table class="table">
						<thead>
							<tr>
								<td style="text-align:center;"><h4><b><?php printf(forum_number_format($stats['total_posts'])) ?></b><br /><?php echo $lang['posts'] ?></h4></td>
								<td style="text-align:center;"><h4><b><?php printf(forum_number_format($stats['total_topics'])) ?></b><br /><?php echo $lang['topics'] ?></h4></td>
								<td style="text-align:center;"><h4><b><?php printf(forum_number_format($stats['total_users'])) ?></b><br /><?php echo $lang['users'] ?></h4></td>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
<?php
//Update checking
if (version_compare(Version::FORUM_VERSION, $latest_version, '<')) {
?>
		<div class="alert alert-info">
			<h4><?php echo sprintf($lang['Available'], $latest_version, '<a href="update.php">'.$lang['update now'].'</a>') ?></h4>
		</div>
<?php
}
?>
		<div class="row">
			<div class="col-lg-12">
				<form class="form-horizontal" method="post" action="index.php">
					<input type="hidden" name="form_sent" value="1" />
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Admin notes<span class="pull-right"><button class="btn btn-primary" type="submit" name="save"><span class="fa fa-check"></span> <?php echo $lang['Save'] ?></button></span></h3>
						</div>
						<div class="panel-body">
							<textarea class="form-control" name="form[admin_note]" placeholder="Add a note..." accesskey="n" rows="10"><?php echo luna_htmlspecialchars($luna_config['o_admin_note']) ?></textarea>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Welcome to the Luna Preview</h3>
			</div>
			<div class="panel-body">
				<p>Hello and welcome to the Luna Preview 1. It's great that you are using this software. However, we hope you are using it far away from a productive environment. This preview is only ment to show what's coming next to Luna.</p>
				<p>Keep an eye on new releases, we release every now and then a new build for Luna, one more stable then the other, for you to check out. You can keep track of it at <a href="http://modernbb.be/lunareleases.php">our website</a>. New builds can contain new features, improved features, or bugfixes (mostly all at once).</p>
				<p>We would like to ask you to send in feedback. Feedback is very important for us. Feedback can be about everything: bugs that need to be fixed, features you would like to see, etc. Be sure to check our shiplist (see links below) before you request a feature or fill a bug, as it might be noted already. Most obvious bugs that you'll find are already known and beeing solved. If you find any issues in the interface, we don't need to know those either, as we're still working on that too (through ideas are always welcome).</p>
			</div>
			<div class="panel-footer">
				<div class="btn-group">
					<a class="btn btn-info" href="https://github.com/ModernBB/Luna/issues/863">Shiplist</a>
					<a class="btn btn-info" href="http://modernbb.be/releases/luna1.0.php">Changelog</a>
				</div>
			</div>
		</div>
	</div>
</div>
<?php

require 'footer.php';
