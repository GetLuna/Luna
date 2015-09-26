<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';

if (!$luna_user['is_admmod'])
	header("Location: login.php");
// Zap a report
if (isset($_POST['zap_id'])) {
	confirm_referrer('backstage/reports.php');
	
	$zap_id = intval(key($_POST['zap_id']));

	$result = $db->query('SELECT zapped FROM '.$db->prefix.'reports WHERE id='.$zap_id) or error('Unable to fetch report info', __FILE__, __LINE__, $db->error());
	$zapped = $db->result($result);

	if ($zapped == '') {
		$db->query('UPDATE '.$db->prefix.'reports SET zapped='.time().', zapped_by='.$luna_user['id'].' WHERE id='.$zap_id) or error('Unable to zap report', __FILE__, __LINE__, $db->error());
		$result = $db->query('SELECT post_id FROM '.$db->prefix.'reports WHERE id='.$zap_id) or error('Unable to fetch report info', __FILE__, __LINE__, $db->error());
		$post_id = $db->result($result);
		$db->query('UPDATE '.$db->prefix.'posts SET marked = 0 WHERE id='.$post_id) or error('Unable to zap report', __FILE__, __LINE__, $db->error());
	}

	// Delete old reports (which cannot be viewed anyway)
	$result = $db->query('SELECT zapped FROM '.$db->prefix.'reports WHERE zapped IS NOT NULL ORDER BY zapped DESC LIMIT 10,1') or error('Unable to fetch read reports to delete', __FILE__, __LINE__, $db->error());
	if ($db->num_rows($result) > 0) {
		$zapped_threshold = $db->result($result);
		$db->query('DELETE FROM '.$db->prefix.'reports WHERE zapped <= '.$zapped_threshold) or error('Unable to delete old read reports', __FILE__, __LINE__, $db->error());
	}

	redirect('backstage/reports.php');
}


$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), __('Admin', 'luna'), __('Reports', 'luna'));
define('FORUM_ACTIVE_PAGE', 'admin');
require 'header.php';
	load_admin_nav('content', 'reports');

?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e('New reports', 'luna') ?></h3>
	</div>
	<form method="post" action="reports.php?action=zap">
		<fieldset>
			<table class="table">
				<thead>
					<tr>
						<th class="col-xs-2"><?php _e('Reported by', 'luna') ?></th>
						<th class="col-xs-2"><?php _e('Date and time', 'luna') ?></th>
						<th class="col-xs-6"><?php _e('Message', 'luna') ?></th>
						<th class="col-xs-2"><?php _e('Actions', 'luna') ?></th>
					</tr>
				</thead>
				<tbody>
<?php

$result = $db->query('SELECT r.id, r.topic_id, r.forum_id, r.reported_by, r.created, r.message, p.id AS pid, t.subject, f.forum_name, u.username AS reporter FROM '.$db->prefix.'reports AS r LEFT JOIN '.$db->prefix.'posts AS p ON r.post_id=p.id LEFT JOIN '.$db->prefix.'topics AS t ON r.topic_id=t.id LEFT JOIN '.$db->prefix.'forums AS f ON r.forum_id=f.id LEFT JOIN '.$db->prefix.'users AS u ON r.reported_by=u.id WHERE r.zapped IS NULL ORDER BY created DESC') or error('Unable to fetch report list', __FILE__, __LINE__, $db->error());

if ($db->num_rows($result)) {
	while ($cur_report = $db->fetch_assoc($result)) {
		$reporter = ($cur_report['reporter'] != '') ? '<a href="../profile.php?id='.$cur_report['reported_by'].'">'.luna_htmlspecialchars($cur_report['reporter']).'</a>' : __('Deleted user', 'luna');
		$forum = ($cur_report['forum_name'] != '') ? '<span><a href="../viewforum.php?id='.$cur_report['forum_id'].'">'.luna_htmlspecialchars($cur_report['forum_name']).'</a></span>' : '<span>'.__('Deleted', 'luna').'</span>';
		$topic = ($cur_report['subject'] != '') ? '<span> <span class="divider">/</span> <a href="../viewtopic.php?id='.$cur_report['topic_id'].'">'.luna_htmlspecialchars($cur_report['subject']).'</a></span>' : ' <span class="divider">/</span><span>'.__('Deleted', 'luna').'</span>';
		$post = str_replace("\n", '<br />', luna_htmlspecialchars($cur_report['message']));
		$post_id = ($cur_report['pid'] != '') ? '<span> <span class="divider">/</span> <a href="../viewtopic.php?pid='.$cur_report['pid'].'#p'.$cur_report['pid'].'">'.sprintf(__('Comment #%s', 'luna'), $cur_report['pid']).'</a></span>' : '<span>'.__('Deleted', 'luna').'</span>';
		$report_location = array($forum, $topic, $post_id);

?>
					<tr>
						<td><?php printf($reporter) ?></td>
						<td><?php printf(format_time($cur_report['created'])) ?></td>
						<td>
							<div class="breadcrumb"><?php echo implode(' ', $report_location) ?></div>
							<?php echo $post ?>
						</td>
						<td><button class="btn btn-primary" type="submit" name="zap_id[<?php echo $cur_report['id'] ?>]"><span class="fa fa-fw fa-eye"></span> <?php _e('Mark as read', 'luna') ?></button></td>
					</tr>
<?php

	}
} else {

?>
					<tr>
						<td colspan="4"><?php _e('There are no new reports.', 'luna') ?></td>
					</tr>
<?php

}

?>
				</tbody>
			</table>
		</fieldset>
	</form>
</div>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e('10 last read reports', 'luna') ?></h3>
	</div>
	<table class="table">
		<thead>
			<tr>
				<th class="col-xs-2"><?php _e('Reported by', 'luna') ?></th>
				<th class="col-xs-2"><?php _e('Marked as read by', 'luna') ?></th>
				<th class="col-xs-2"><?php _e('Date and time', 'luna') ?></th>
				<th class="col-xs-6"><?php _e('Message', 'luna') ?></th>
			</tr>
		</thead>
		<tbody>
<?php

$result = $db->query('SELECT r.id, r.topic_id, r.forum_id, r.reported_by, r.message, r.zapped, r.zapped_by AS zapped_by_id, p.id AS pid, t.subject, f.forum_name, u.username AS reporter, u2.username AS zapped_by FROM '.$db->prefix.'reports AS r LEFT JOIN '.$db->prefix.'posts AS p ON r.post_id=p.id LEFT JOIN '.$db->prefix.'topics AS t ON r.topic_id=t.id LEFT JOIN '.$db->prefix.'forums AS f ON r.forum_id=f.id LEFT JOIN '.$db->prefix.'users AS u ON r.reported_by=u.id LEFT JOIN '.$db->prefix.'users AS u2 ON r.zapped_by=u2.id WHERE r.zapped IS NOT NULL ORDER BY zapped DESC LIMIT 10') or error('Unable to fetch report list', __FILE__, __LINE__, $db->error());

if ($db->num_rows($result)) {
	while ($cur_report = $db->fetch_assoc($result)) {
		$reporter = ($cur_report['reporter'] != '') ? '<a href="../profile.php?id='.$cur_report['reported_by'].'">'.luna_htmlspecialchars($cur_report['reporter']).'</a>' : __('Deleted user', 'luna');
		$forum = ($cur_report['forum_name'] != '') ? '<span><a href="../viewforum.php?id='.$cur_report['forum_id'].'">'.luna_htmlspecialchars($cur_report['forum_name']).'</a></span>' : '<span>'.__('Deleted', 'luna').'</span>';
		$topic = ($cur_report['subject'] != '') ? '<span> <span class="divider">/</span> <a href="../viewtopic.php?id='.$cur_report['topic_id'].'">'.luna_htmlspecialchars($cur_report['subject']).'</a></span>' : ' <span class="divider">/</span><span>'.__('Deleted', 'luna').'</span>';
		$post = str_replace("\n", '<br />', luna_htmlspecialchars($cur_report['message']));
		$post_id = ($cur_report['pid'] != '') ? '<span> <span class="divider">/</span> <a href="../viewtopic.php?pid='.$cur_report['pid'].'#p'.$cur_report['pid'].'">'.sprintf(__('Comment #%s', 'luna'), $cur_report['pid']).'</a></span>' : '<span> <span class="divider">/</span> '.__('Deleted', 'luna').'</span>';
		$zapped_by = ($cur_report['zapped_by'] != '') ? '<a href="../profile.php?id='.$cur_report['zapped_by_id'].'">'.luna_htmlspecialchars($cur_report['zapped_by']).'</a>' : __('N/A', 'luna');
		$zapped_by = ($cur_report['zapped_by'] != '') ? '<strong>'.luna_htmlspecialchars($cur_report['zapped_by']).'</strong>' : __('N/A', 'luna');
		$report_location = array($forum, $topic, $post_id);

?>
			<tr>
				<td><?php printf($reporter) ?></td>
				<td><?php printf($zapped_by) ?></td>
				<td><?php printf(format_time($cur_report['zapped'])) ?></td>
				<td>
					<div class="breadcrumb"><?php echo implode(' ', $report_location) ?></div>
					<?php echo $post ?>
				</td>
			</tr>
<?php

	}
} else {

?>
			<tr>
				<td colspan="4"><?php _e('There are no read reports.', 'luna') ?></td>
			</tr>
<?php

} ?>
		</tbody>
	</table>
</div>
<?php
require 'footer.php';
