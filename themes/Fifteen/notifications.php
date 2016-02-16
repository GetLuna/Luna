<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;
?>
<div class="profile-header container-fluid">
	<div class="jumbotron profile">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h2 class="username"><?php echo $user['username'] ?></h2>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="main profile container">
	<div class="row">
		<div class="col-xs-12 col-sm-3 sidebar">
			<div class="container-avatar">
				<img src="<?php echo get_avatar( $user['id'] ) ?>" alt="Avatar" class="img-avatar img-center">
			</div>
			<?php load_me_nav('notifications'); ?>
		</div>
		<div class="col-xs-12 col-sm-9">
			<div class="title-block title-block-primary title-block-nav">
				<h2><i class="fa fa-fw fa-circle-o"></i> <?php _e('Notifications', 'luna') ?></h2>
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#new" aria-controls="new" role="tab" data-toggle="tab"><i class="fa fa-fw fa-circle"></i><span class="hidden-sm hidden-xs"> <?php _e('New', 'luna') ?></span></a></li>
					<li role="presentation"><a href="#seen" aria-controls="seen" role="tab" data-toggle="tab"><i class="fa fa-fw fa-circle-o"></i><span class="hidden-sm hidden-xs"> <?php _e('Seen', 'luna') ?></span></a></li>
				</ul>
			</div>
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="new">
					<a href="notifications.php?id=2&action=readnoti" class="btn btn-primary"><span class="fa fa-fw fa-eye"></span> <?php _e('Mark as seen', 'luna') ?></a>
					<div class="list-group list-group-thread">
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
				<div role="tabpanel" class="tab-pane" id="seen">
					<a href="notifications.php?id=2&action=delnoti" class="btn btn-danger"><span class="fa fa-fw fa-trash"></span> <?php _e('Delete notifications', 'luna') ?></a>
					<div class="list-group list-group-thread">
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
		</div>
	</div>
</div>