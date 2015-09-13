<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;
?>
<div class="row">
	<div class="col-sm-3 profile-nav">
		<div class="profile-card">
			<div class="profile-card-head profile-card-nav">
				<div class="user-avatar">
					<?php echo $avatar_user_card; ?>
				</div>
				<h2><?php echo $user_username; ?></h2>
				<h3><?php echo $user_usertitle; ?></h3>
				<?php load_me_nav('notifications', 'list-group-transparent'); ?>
			</div>
		</div>
	</div>
	<div class="col-sm-9 profile">
		<h2 class="profile-title"><?php _e('Notifications', 'luna') ?></h2>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php _e('New notifications', 'luna'); ?><span class="pull-right"><a href="notifications.php?id=2&action=readnoti" class="btn btn-primary"><span class="fa fa-fw fa-eye"></span> <?php _e('Mark as seen', 'luna') ?></a></span></h3>
			</div>
			<div class="list-group">
	<?php if (empty($unviewed_notifications)) { ?>
				<a class="list-group-item disabled" href="notifications.php?id=<?php echo $id; ?>"><?php _e('No new notifications', 'luna'); ?></a>
	<?php
	} else {
		foreach ($unviewed_notifications as $notification) {
	?>
				<a class="list-group-item" href="<?php echo $notification->link; ?>"><span class="fa fa-fw <?php echo $notification->icon; ?>"></span>&nbsp; <?php echo $notification->message; ?><span class="timestamp pull-right"><?php echo format_time($notification->time, false, null, $luna_config['o_time_format'], true, true); ?></span></a>
	
	<?php
		}
	}
	?>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php _e('Seen notifications', 'luna') ?><span class="pull-right"><a href="notifications.php?id=2&action=delnoti" class="btn btn-danger"><span class="fa fa-fw fa-trash"></span> <?php _e('Delete notifications', 'luna') ?></a></span></h3>
			</div>
			<div class="list-group">
	<?php if (empty($viewed_notifications)) { ?>
				<a class="list-group-item disabled" href="notifications.php?id=<?php echo $id; ?>"><?php _e('No new notifications', 'luna'); ?></a>
	<?php
	} else {
		foreach ($viewed_notifications as $notification) {
	?>
				<a class="list-group-item" href="<?php echo $notification->link; ?>"><span class="fa fa-fw <?php echo $notification->icon; ?>"></span>&nbsp; <?php echo $notification->message; ?><span class="timestamp pull-right"><?php echo format_time($notification->time, false, null, $luna_config['o_time_format'], true, true); ?></span></a>
	
	<?php
		}
	}
	?>
		</div>
	</div>
</div>