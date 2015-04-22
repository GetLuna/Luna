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
<div class="col-sm-9 profile">
	<nav class="navbar navbar-default" role="navigation">
		<div class="navbar-header">
			<a href="notifications.php?id=<?php echo $id ?>" class="navbar-brand"><span class="fa fa-fw fa-circle-o"></span> <?php echo $lang['Notifications'] ?></a>
		</div>
	</nav>
	<div class="list-group">
		<h3><?php echo $lang['New notifications'] ?><span class="pull-right"><a href="notifications.php?id=2&action=readnoti" class="btn btn-primary"><span class="fa fa-fw fa-eye"></span> <?php echo $lang['Mark as seen'] ?></a></span></h3>
		<?php echo $not ?>
		<h3><?php echo $lang['Seen notifications'] ?><span class="pull-right"><a href="notifications.php?id=2&action=delnoti" class="btn btn-danger"><span class="fa fa-fw fa-trash"></span> <?php echo $lang['Delete notifications'] ?></a></span></h3>
		<?php echo $not_seen ?>
	</div>
</div>