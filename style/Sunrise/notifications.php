<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;
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
			<a href="notifications.php?id=2&action=readnoti" class="btn btn-default">Mark as seen</a>
			<a href="notifications.php?id=2&action=delnoti" class="btn btn-default">Delete seen notifications</a>
		</span>
	</span>
	<div class="list-group">
		<h3>New notifications</h3>
		<?php echo $not ?>
		<h3>Old notifications</h3>
		<?php echo $not_seen ?>
	</div>
</div>