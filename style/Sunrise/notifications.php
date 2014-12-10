<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

// Show notifications
$result = $db->query('SELECT COUNT(id) FROM '.$db_prefix.'notifications WHERE user_id = '.$luna_user['id']) or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
$num_not = $db->result($result);

$ind_not[] = '';

if ($num_not == '0') {
	$ind_not[] = '<a class="list-group-item disabled" href="me.php?section=notifications&id='.$luna_user['id'].'">No new notifications</a>';
} else {
	$result = $db->query('SELECT * FROM '.$db_prefix.'notifications WHERE user_id = '.$luna_user['id'].' AND viewed = 0 ORDER BY time DESC') or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());

	while ($cur_notifi = $db->fetch_assoc($result)) {
		$notifitime = format_time($cur_notifi['time'], false, null, $time_format, true, true);
		$ind_not[] = '<a class="list-group-item" href="'.$cur_notifi['link'].'"><span class="fa fa-fw '.$cur_notifi['icon'].'"></span> '.$cur_notifi['message'].' <span class="timestamp pull-right">'.$notifitime.'</span></a>';
	}
}

$not = implode('', $ind_not);
?>

</div>
<div class="jumbotron me-jumbotron">
	<div class="container">
        <div class="media">
            <a class="pull-left" href="#">
                <?php echo generate_avatar_markup($luna_user['id']) ?>
            </a>
            <div class="media-body">
                <h2 class="media-heading"><?php echo $user['username']; ?></h2>
            </div>
        </div>
	</div>
</div>
<div class="container">
<div class="col-sm-3 profile-nav">
<?php
    generate_me_menu('notifications');
?>
</div>
<div class="col-sm-9 col-profile">
	<h2>Notifications</h2>
	<span class="btn-group">
		<a href="me.php?section=notifications&id=2&action=newnoti&type=windows" class="btn btn-default">Windows</a>
		<a href="me.php?section=notifications&id=2&action=newnoti&type=comment" class="btn btn-default">Comment</a>
		<a href="me.php?section=notifications&id=2&action=newnoti&type=check" class="btn btn-default">Check</a>
		<a href="me.php?section=notifications&id=2&action=newnoti&type=version" class="btn btn-default">Luna version report</a>
	</span>
	<div class="list-group">
		<h3>New notifications</h3>
		<?php echo $not ?>
	</div>
</div>