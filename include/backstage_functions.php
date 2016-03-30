<?php

/*
 * Copyright (C) 2013-2016 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

//
// Display the admin navigation menu
//
function load_admin_nav($section, $page) {
	global $luna_user, $luna_config, $is_admin, $db;

// Check for new notifications
$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'notifications WHERE viewed = 0 AND user_id = '.$luna_user['id']) or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
$num_notifications = $db->result($result);

if ($luna_config['o_notification_flyout'] == 1) {
	if ($num_notifications == '0') {
		$notificon = '<span class="fa fa-fw fa-circle-o"></span>';
		$ind_notification[] = '<li><a href="../notifications.php">'.__( 'No new notifications', 'luna' ).'</a></li>';
	} else {
		$notificon = $num_notifications.' <span class="fa fa-fw fa-circle"></span>';
		
		$notification_result = $db->query('SELECT * FROM '.$db->prefix.'notifications WHERE user_id = '.$luna_user['id'].' AND viewed = 0 ORDER BY time DESC LIMIT 10') or error ('Unable to load notifications', __FILE__, __LINE__, $db->error());
		while ($cur_notifi = $db->fetch_assoc($notification_result)) {
			$notifitime = format_time($cur_notifi['time'], false, null, $luna_config['o_time_format'], true, true);
			$ind_notification[] = '<li><a href="../notifications.php?notification='.$cur_notifi['id'].'"><span class="fa fa-fw luni luni-fw '.$cur_notifi['icon'].'"></span> '.$cur_notifi['message'].' <span class="timestamp pull-right">'.$notifitime.'</span></a></li>';
		}
	}

	$notifications = implode('<li class="divider"></li>', $ind_notification);
	$notification_menu_item = '
					<li class="dropdown">
					<a href="#" class="dropdown-toggle'.(($num_notifications != 0)? ' flash' : '').'" data-toggle="dropdown">'.$notificon.'<span class="visible-xs-inline"> '.__( 'Notifications', 'luna' ).'</span></a>
					<ul class="dropdown-menu notification-menu">
						<li role="presentation" class="dropdown-header">'.__( 'Notifications', 'luna' ).'</li>
						<li class="divider"></li>
						'.$notifications.'
						<li class="divider"></li>
                        <li class="dropdown-footer hidden-xs"><a class="pull-right" href="../notifications.php">'.__('More', 'luna').' <i class="fa fa-fw fa-arrow-right"></i></a></li>
                        <li class="dropdown-footer hidden-lg hidden-md hidden-sm"><a href="../notifications.php">'.__('More', 'luna').' <i class="fa fa-fw fa-arrow-right"></i></a></li>
					</ul>
				</li>';
} else {
	if ($num_notifications == '0')
		$notificon = '<span class="fa fa-fw fa-circle-o"></span>';
	else
		$notificon = $num_notifications.' <span class="fa fa-fw fa-circle"></span>';

	$notification_menu_item = '<li><a href="notifications.php" class="flash">'.$notificon.'<span class="visible-xs-inline"> '.__( 'Notifications', 'luna' ).'</span></a></li>';
}
    
?>
<nav class="navbar navbar-default" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="../index.php"><i class="fa fa-fw fa-angle-left hidden-xs"></i><span class="visible-xs-inline"><?php echo $luna_config['o_board_title'] ?></span></a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="<?php if ($section == 'backstage') echo 'active'; ?>"><a href="index.php"><i class="fa fa-fw fa-dashboard"></i> <?php _e('Backstage', 'luna') ?></a></li>
				<?php if ($is_admin) { ?>
					<li class="<?php if ($section == 'content') echo 'active'; ?>"><a href="board.php"><i class="fa fa-fw fa-file"></i> <?php _e('Content', 'luna') ?></a></li>
				<?php } else { ?>
					<li class="<?php if ($section == 'content') echo 'active'; ?>"><a href="reports.php"><i class="fa fa-fw fa-file"></i> <?php _e('Content', 'luna') ?></a></li>
				<?php } ?>
				<li class="<?php if ($section == 'users') echo 'active'; ?>"><a href="users.php"><i class="fa fa-fw fa-users"></i> <?php _e('Users', 'luna') ?></a></li>
				<?php if ($is_admin) { ?><li class="<?php if ($section == 'settings') echo 'active'; ?>"><a href="settings.php"><i class="fa fa-fw fa-cog"></i> <?php _e('Settings', 'luna') ?></a></li><?php } ?>
				<?php if ($is_admin) { ?><li class="<?php if ($section == 'maintenance') echo 'active'; ?>"><a href="maintenance.php"><i class="fa fa-fw fa-coffee"></i> <?php _e('Maintenance', 'luna') ?></a></li>	<?php } ?>
<?php

	// See if there are any plugins
	$plugins = forum_list_plugins($is_admin);

	// Did we find any plugins?
	if (!empty($plugins))
	{
?>
				<li class="dropdown<?php if ($section == ' extensions') echo 'active'; ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-fw fa-cogs"></i> <?php _e('Extensions', 'luna') ?> <i class="fa fa-fw fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu">
<?php
		foreach ($plugins as $plugin_name => $plugin)
			echo "\t\t\t\t\t".'<li><a href="loader.php?plugin='.$plugin_name.'">'.str_replace('_', ' ', $plugin).'</a></li>'."\n";
?>
					</ul>
				</li>
<?php } ?>
			</ul>
<?php
$logout_url = '../login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_csrf_token();
?>
			<ul class="nav navbar-nav navbar-right">
                <?php echo $notification_menu_item ?>
				<li class="dropdown usermenu">
					<a href="../profile.php?id=<?php echo $luna_user['id'] ?>" class="dropdown-toggle dropdown-user" data-toggle="dropdown">
                        <?php echo draw_user_avatar($luna_user['id'], true, 'avatar'); ?><span class="hidden-lg hidden-md hidden-sm"> <?php echo luna_htmlspecialchars($luna_user['username']); ?></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="../profile.php?id=<?php echo $luna_user['id'] ?>"><i class="fa fa-fw fa-user"></i> <?php _e('Profile', 'luna') ?></a></li>
						<li><a href="../inbox.php"><i class="fa fa-fw fa-paper-plane-o"></i> <?php _e('Inbox', 'luna') ?></a></li>
						<li><a href="../settings.php?id=<?php echo $luna_user['id'] ?>"><i class="fa fa-fw fa-cogs"></i> <?php _e('Settings', 'luna') ?></a></li>
						<li class="divider"></li>
						<li><a href="../help.php"><i class="fa fa-fw fa-info-circle"></i> <?php _e('Help', 'luna') ?></a></li>
						<li><a href="http://getluna.org"><i class="fa fa-fw fa-support"></i> <?php _e('Support', 'luna') ?></a></li>
						<li class="divider"></li>
						<li><a href="<?php echo $logout_url; ?>"><i class="fa fa-fw fa-sign-out"></i> <?php _e('Logout', 'luna') ?></a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</nav>
<div class="jumbotron jumboheader">
	<div class="container">
        <ul class="nav nav-tabs nav-main" role="tablist">
        <?php if ($section == 'backstage') { ?>
            <li<?php if($page == 'index') echo ' class="active"' ?>><a href="index.php"><i class="fa fa-fw fa-tachometer"></i> <?php _e('Backstage', 'luna') ?></a></li>
            <li<?php if($page == 'update') echo ' class="active"' ?>><a href="update.php"><i class="fa fa-fw fa-cloud-upload"></i> <?php _e('Update', 'luna') ?></a></li>
            <li<?php if($page == 'about') echo ' class="active"' ?>><a href="about.php"><i class="fa fa-fw fa-moon-o"></i> <?php _e('About', 'luna') ?></a></li>
        <?php } if ($section == 'content') { ?>
            <li<?php if($page == 'board') echo ' class="active"' ?>><a href="board.php"><i class="fa fa-fw fa-list"></i> <?php _e('Board', 'luna') ?></a></li>
            <li<?php if($page == 'reports') echo ' class="active"' ?>><a href="reports.php"><i class="fa fa-fw fa-flag"></i> <?php _e('Reports', 'luna') ?></a></li>
            <li<?php if($page == 'censoring') echo ' class="active"' ?>><a href="censoring.php"><i class="fa fa-fw fa-eye-slash"></i> <?php _e('Censoring', 'luna') ?></a></li>
        <?php } if ($section == 'users') { ?>
            <li<?php if($page == 'users') echo ' class="active"' ?>><a href="users.php"><i class="fa fa-fw fa-search"></i> <?php _e('Search', 'luna') ?></a></li>
            <li<?php if($page == 'ranks') echo ' class="active"' ?>><a href="ranks.php"><i class="fa fa-fw fa-trophy"></i> <?php _e('Ranks', 'luna') ?></a></li>
            <li<?php if($page == 'groups') echo ' class="active"' ?>><a href="groups.php"><i class="fa fa-fw fa-group"></i> <?php _e('Groups', 'luna') ?></a></li>
            <li<?php if($page == 'bans') echo ' class="active"' ?>><a href="bans.php"><i class="fa fa-fw fa-ban"></i> <?php _e('Bans', 'luna') ?></a></li>
        <?php } if ($section == 'settings') { ?>
            <li<?php if($page == 'settings') echo ' class="active"' ?>><a href="settings.php"><i class="fa fa-fw fa-cogs"></i> <?php _e('Settings', 'luna') ?></a></li>
            <li<?php if($page == 'features') echo ' class="active"' ?>><a href="features.php"><i class="fa fa-fw fa-sliders"></i> <?php _e('Features', 'luna') ?></a></li>
            <li<?php if($page == 'appearance') echo ' class="active"' ?>><a href="appearance.php"><i class="fa fa-fw fa-eye"></i> <?php _e('Theme', 'luna') ?></a></li>
            <li<?php if($page == 'menu') echo ' class="active"' ?>><a href="menu.php"><i class="fa fa-fw fa-bars"></i> <?php _e('Menu', 'luna') ?></a></li>
        <?php } if ($section == 'maintenance') { ?>
            <li<?php if($page == 'maintenance') echo ' class="active"' ?>><a href="maintenance.php"><i class="fa fa-fw fa-coffee"></i> <?php _e('Maintenance', 'luna') ?></a></li>
            <li<?php if($page == 'prune') echo ' class="active"' ?>><a href="prune.php"><i class="fa fa-fw fa-recycle"></i> <?php _e('Prune', 'luna') ?></a></li>
        <?php } ?>
        </ul>
	</div>
</div>
<div class="content">
	<div class="container main">
<?php

}

function check_is_admin() {
	global $luna_user;

	$is_admin = $luna_user['g_id'] == LUNA_ADMIN ? true : false;

	return $is_admin;
}
