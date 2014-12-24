<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

// Show notifications
$result = $db->query('SELECT COUNT(id) FROM '.$db_prefix.'notifications WHERE viewed = 0 AND user_id = '.$user['id']) or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
$num_not_unseen = $db->result($result);

if ($num_not_unseen == '0') {
	$ind_not[] = '<a class="list-group-item disabled" href="me.php?section=notifications&id='.$user['id'].'">No new notifications</a>';
} else {
	$result = $db->query('SELECT * FROM '.$db_prefix.'notifications WHERE viewed = 0 AND user_id = '.$user['id'].' ORDER BY time DESC') or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
	while ($cur_notifi = $db->fetch_assoc($result)) {
		$notifitime = format_time($cur_notifi['time'], false, null, $time_format, true, true);
		$ind_not[] = '<a class="list-group-item" href="'.$cur_notifi['link'].'"><span class="fa fa-fw '.$cur_notifi['icon'].'"></span> '.$cur_notifi['message'].' <span class="timestamp pull-right">'.$notifitime.'</span></a>';
	}
}

$result = $db->query('SELECT COUNT(id) FROM '.$db_prefix.'notifications WHERE viewed = 1 AND user_id = '.$user['id']) or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
$num_not_seen = $db->result($result);

if ($num_not_seen == '0') {
	$ind_not_seen[] = '<a class="list-group-item disabled" href="me.php?section=notifications&id='.$user['id'].'">No new notifications</a>';
} else {
	$result = $db->query('SELECT * FROM '.$db_prefix.'notifications WHERE viewed = 1 AND user_id = '.$user['id'].' ORDER BY time DESC') or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
	while ($cur_notifi = $db->fetch_assoc($result)) {
		$notifitime = format_time($cur_notifi['time'], false, null, $time_format, true, true);
		$ind_not_seen[] = '<a class="list-group-item" href="'.$cur_notifi['link'].'"><span class="fa fa-fw '.$cur_notifi['icon'].'"></span> '.$cur_notifi['message'].' <span class="timestamp pull-right">'.$notifitime.'</span></a>';
	}
}

$not = implode('', $ind_not);
$not_seen = implode('', $ind_not_seen);
?>
<div class="col-sm-3 profile-nav">
	<div class="user-card-profile">
		<h3 class="user-card-title"><?php echo luna_htmlspecialchars($user['username']) ?></h3>
		<span class="user-card-avatar thumbnail">
			<?php echo $avatar_user_card ?>
		</span>
	</div>
<?php
    load_me_nav('notifications');
?>
</div>
<div class="col-sm-9">
	<h2 class="profile-settings-head">Notifications</h2>
	<span class="btn-toolbar">
		<span class="btn-group">
			<a href="me.php?section=notifications&id=2&action=newnoti&type=windows" class="btn btn-default"><span class="fa fa-fw fa-windows"></span></a>
			<a href="me.php?section=notifications&id=2&action=newnoti&type=comment" class="btn btn-default"><span class="fa fa-fw fa-comment"></span></a>
			<a href="me.php?section=notifications&id=2&action=newnoti&type=check" class="btn btn-default"><span class="fa fa-fw fa-check"></span></a>
			<a href="me.php?section=notifications&id=2&action=newnoti&type=version" class="btn btn-default"><span class="fa fa-fw fa-moon-o"></span></a>
			<a href="me.php?section=notifications&id=2&action=newnoti&type=cogs" class="btn btn-default"><span class="fa fa-fw fa-cogs"></span></a>
		</span>
		<span class="btn-group">
			<a href="me.php?section=notifications&id=2&action=readnoti" class="btn btn-default">Mark as seen</a>
			<a href="me.php?section=notifications&id=2&action=delnoti" class="btn btn-default">Delete seen notifications</a>
		</span>
	</span>
	<div class="list-group">
		<h3>New notifications</h3>
		<?php echo $not ?>
		<h3>Old notifications</h3>
		<?php echo $not_seen ?>
	</div>
</div>