<?php

/*
 * Copyright (C) 2013-2015 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

//
// Display the admin navigation menu
//
function load_admin_nav($section, $page) {
	global $luna_user, $luna_config, $is_admin;

	// What page are we on?
	if ($page == 'index')
		$page_title = '<span class="fa fa-fw fa-tachometer"></span> '.__('Backstage', 'luna');
	elseif ($page == 'stats')
		$page_title = '<span class="fa fa-fw fa-info-circle"></span> '.__('System info', 'luna');
	elseif ($page == 'update')
		$page_title = '<span class="fa fa-fw fa-cloud-upload"></span> '.__('Luna software update', 'luna');
	elseif ($page == 'about')
		$page_title = '<span class="fa fa-fw fa-moon-o"></span> '.__('About Luna', 'luna');

	elseif ($page == 'board')
		$page_title = '<span class="fa fa-fw fa-sort-amount-desc"></span> '.__('Board', 'luna');
	elseif ($page == 'moderate')
		$page_title = '<span class="fa fa-fw fa-tasks"></span> '.__('Moderate', 'luna');
	elseif ($page == 'censoring')
		$page_title = '<span class="fa fa-fw fa-eye-slash"></span> '.__('Censoring', 'luna');
	elseif ($page == 'reports')
		$page_title = '<span class="fa fa-fw fa-exclamation-triangle"></span> '.__('Reports', 'luna');

	elseif ($page == 'users')
		$page_title = '<span class="fa fa-fw fa-search"></span> '.__('Search', 'luna');
	elseif ($page == 'tools')
		$page_title = '<span class="fa fa-fw fa-wrench"></span> '.__('Tools', 'luna');
	elseif ($page == 'ranks')
		$page_title = '<span class="fa fa-fw fa-chevron-up"></span> '.__('Ranks', 'luna');
	elseif ($page == 'groups')
		$page_title = '<span class="fa fa-fw fa-group"></span> '.__('Groups', 'luna');
	elseif ($page == 'permissions')
		$page_title = '<span class="fa fa-fw fa-check-circle"></span> '.__('Permissions', 'luna');
	elseif ($page == 'bans')
		$page_title = '<span class="fa fa-fw fa-ban"></span> '.__('Bans', 'luna');

	elseif ($page == 'settings')
		$page_title = '<span class="fa fa-fw fa-cogs"></span> '.__('Settings', 'luna');
	elseif ($page == 'features')
		$page_title = '<span class="fa fa-fw fa-sliders"></span> '.__('Features', 'luna');
	elseif ($page == 'appearance')
		$page_title = '<span class="fa fa-fw fa-eye"></span> '.__('Appearance', 'luna');
	elseif ($page == 'registration')
		$page_title = '<span class="fa fa-fw fa-plus-circle"></span> '.__('Registration', 'luna');
	elseif ($page == 'email')
		$page_title = '<span class="fa fa-fw fa-envelope"></span> '.__('Email', 'luna');
	elseif ($page == 'menu')
		$page_title = '<span class="fa fa-fw fa-bars"></span> '.__('Menu', 'luna');
	elseif ($page == 'theme')
		$page_title = '<span class="fa fa-fw fa-paint-brush"></span> '.__('Theme', 'luna');

	elseif ($page == 'maintenance')
		$page_title = '<span class="fa fa-fw fa-coffee"></span> '.__('Maintenance', 'luna');
	elseif ($page == 'prune')
		$page_title = '<span class="fa fa-fw fa-recycle"></span> '.__('Prune', 'luna');
	elseif ($page == 'database')
		$page_title = '<span class="fa fa-fw fa-database"></span> '.__('Database management', 'luna');

	elseif ($page == 'info')
		$page_title = '<span class="fa fa-fw fa-info-circle"></span> '.__('Info', 'luna');
		
	else
		$page_title = $page;
?>
<nav class="navbar navbar-fixed-top navbar-default" role="navigation">
	<div class="container navbar-container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="../index.php"><span class="fa fa-fw fa-arrow-left hidden-xs"></span><span class="visible-xs-inline"><?php echo $page_title ?></span></a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li class="<?php if ($section == 'backstage') echo 'active'; ?>"><a href="index.php"><span class="fa fa-fw fa-dashboard"></span> <?php _e('Backstage', 'luna') ?></a></li>
				<?php if ($is_admin) { ?>
					<li class="<?php if ($section == 'content') echo 'active'; ?>"><a href="board.php"><span class="fa fa-fw fa-file"></span> <?php _e('Content', 'luna') ?></a></li>
				<?php } else { ?>
					<li class="<?php if ($section == 'content') echo 'active'; ?>"><a href="reports.php"><span class="fa fa-fw fa-file"></span> <?php _e('Content', 'luna') ?></a></li>
				<?php } ?>
				<li class="<?php if ($section == 'users') echo 'active'; ?>"><a href="users.php"><span class="fa fa-fw fa-users"></span> <?php _e('Users', 'luna') ?></a></li>
				<?php if ($is_admin) { ?><li class="<?php if ($section == 'settings') echo 'active'; ?>"><a href="settings.php"><span class="fa fa-fw fa-cog"></span> <?php _e('Settings', 'luna') ?></a></li><?php } ?>
				<?php if ($is_admin) { ?><li class="<?php if ($section == 'maintenance') echo 'active'; ?>"><a href="maintenance.php"><span class="fa fa-fw fa-coffee"></span> <?php _e('Maintenance', 'luna') ?></a></li>	<?php } ?>
<?php

	// See if there are any plugins
	$plugins = forum_list_plugins($is_admin);

	// Did we find any plugins?
	if (!empty($plugins))
	{
?>
				<li class="dropdown<?php if ($section == ' extensions') echo 'active'; ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<span class="fa fa-fw fa-cogs"></span> <?php _e('Extensions', 'luna') ?> <span class="fa fa-fw fa-angle-down">
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
				<li class="dropdown usermenu">
					<a href="../profile.php?id=<?php echo $luna_user['id'] ?>" class="dropdown-toggle dropdown-user" data-toggle="dropdown">
                        <?php echo draw_user_avatar($luna_user['id'], true, 'avatar'); ?><span class="hidden-lg hidden-md hidden-sm"> <?php echo luna_htmlspecialchars($luna_user['username']); ?></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="../profile.php?id=<?php echo $luna_user['id'] ?>"><?php _e('Profile', 'luna') ?></a></li>
						<li><a href="../settings.php?id=<?php echo ''.$luna_user['id'] ?>"><?php _e('Settings', 'luna') ?></a></li>
						<li class="divider"></li>
						<li><a href="../help.php"><?php _e('Help', 'luna') ?></a></li>
						<li><a href="http://getluna.org"><?php _e('Support', 'luna') ?></a></li>
						<li class="divider"></li>
						<li><a href="<?php echo $logout_url; ?>"><?php _e('Logout', 'luna') ?></a></li>
					</ul>
				</li>
				<li id="navlogout" class="hide-if-js">
					<a href="<?php echo $logout_url; ?>" title="<?php echo $item['logout']['title']; ?>">
						<span class="fa fa-fw fa-sign-out"></span>
					</a>
				</li>
			</ul>
		</div>
	</div>
</nav>
<div class="jumbotron jumboheader">
	<div class="container">
		<div class="row">
			<h2 class="hidden-xs">
				<?php
					echo $page_title;
					if ($luna_config['o_update_ring'] > 1)
						echo '<span class="pull-right" style="font-size: 70%;">Core '.Version::LUNA_CORE_VERSION.'</span>';
				?>
			</h2>
			<?php if ($section == 'backstage') { ?>
			<ul class="nav nav-tabs" role="tablist">
				<li<?php if($page == 'index') echo ' class="active"' ?>><a href="index.php"><span class="fa fa-fw fa-tachometer"></span><span class="hidden-xs"> <?php _e('Backstage', 'luna') ?></span></a></li>
				<li<?php if($page == 'stats') echo ' class="active"' ?>><a href="system.php"><span class="fa fa-fw fa-info-circle"></span><span class="hidden-xs"> <?php _e('System info', 'luna') ?></span></a></li>
				<li<?php if($page == 'update') echo ' class="active"' ?>><a href="update.php"><span class="fa fa-fw fa-cloud-upload"></span><span class="hidden-xs"> <?php _e('Update', 'luna') ?></span></a></li>
				<li class="pull-right<?php if($page == 'about') echo ' active' ?>"><a href="about.php"><span class="fa fa-fw fa-moon-o"></span><span class="hidden-xs"> <?php _e('About', 'luna') ?></span></a></li>
			</ul>
			<?php } if ($section == 'content') { ?>
			<ul class="nav nav-tabs" role="tablist">
				<li<?php if($page == 'board') echo ' class="active"' ?>><a href="board.php"><span class="fa fa-fw fa-sort-amount-desc"></span><span class="hidden-xs"> <?php _e('Board', 'luna') ?></span></a></li>
				<li<?php if($page == 'censoring') echo ' class="active"' ?>><a href="censoring.php"><span class="fa fa-fw fa-eye-slash"></span><span class="hidden-xs"> <?php _e('Censoring', 'luna') ?></span></a></li>
				<li<?php if($page == 'reports') echo ' class="active"' ?>><a href="reports.php"><span class="fa fa-fw fa-exclamation-triangle"></span><span class="hidden-xs"> <?php _e('Reports', 'luna') ?></span></a></li>
			</ul>
			<?php } if ($section == 'users') { ?>
			<ul class="nav nav-tabs" role="tablist">
				<li<?php if($page == 'users') echo ' class="active"' ?>><a href="users.php"><span class="fa fa-fw fa-search"></span><span class="hidden-xs"> <?php _e('Search', 'luna') ?></span></a></li>
				<li<?php if($page == 'tools') echo ' class="active"' ?>><a href="tools.php"><span class="fa fa-fw fa-wrench"></span><span class="hidden-xs"> <?php _e('Tools', 'luna') ?></span></a></li>
				<li<?php if($page == 'ranks') echo ' class="active"' ?>><a href="ranks.php"><span class="fa fa-fw fa-chevron-up"></span><span class="hidden-xs"> <?php _e('Ranks', 'luna') ?></span></a></li>
				<li<?php if($page == 'groups') echo ' class="active"' ?>><a href="groups.php"><span class="fa fa-fw fa-group"></span><span class="hidden-xs"> <?php _e('Groups', 'luna') ?></span></a></li>
				<li<?php if($page == 'permissions') echo ' class="active"' ?>><a href="permissions.php"><span class="fa fa-fw fa-check-circle"></span><span class="hidden-xs"> <?php _e('Permissions', 'luna') ?></span></a></li>
				<li<?php if($page == 'bans') echo ' class="active"' ?>><a href="bans.php"><span class="fa fa-fw fa-ban"></span><span class="hidden-xs"> <?php _e('Bans', 'luna') ?></span></a></li>
			</ul>
			<?php } if ($section == 'settings') { ?>
			<ul class="nav nav-tabs" role="tablist">
				<li<?php if($page == 'settings') echo ' class="active"' ?>><a href="settings.php"><span class="fa fa-fw fa-cogs"></span><span class="hidden-xs"> <?php _e('Settings', 'luna') ?></span></a></li>
				<li<?php if($page == 'features') echo ' class="active"' ?>><a href="features.php"><span class="fa fa-fw fa-sliders"></span><span class="hidden-xs"> <?php _e('Features', 'luna') ?></span></a></li>
				<li<?php if($page == 'appearance') echo ' class="active"' ?>><a href="appearance.php"><span class="fa fa-fw fa-eye"></span><span class="hidden-xs"> <?php _e('Appearance', 'luna') ?></span></a></li>
				<li<?php if($page == 'registration') echo ' class="active"' ?>><a href="registration.php"><span class="fa fa-fw fa-plus-circle"></span><span class="hidden-xs"> <?php _e('Registration', 'luna') ?></span></a></li>
				<li<?php if($page == 'email') echo ' class="active"' ?>><a href="email.php"><span class="fa fa-fw fa-envelope"></span><span class="hidden-xs"> <?php _e('Email', 'luna') ?></span></a></li>
				<li<?php if($page == 'menu') echo ' class="active"' ?>><a href="menu.php"><span class="fa fa-fw fa-bars"></span><span class="hidden-xs"> <?php _e('Menu', 'luna') ?></span></a></li>
				<li<?php if($page == 'theme') echo ' class="active"' ?>><a href="theme.php"><span class="fa fa-fw fa-paint-brush"></span><span class="hidden-xs"> <?php _e('Theme', 'luna') ?></span></a></li>
			</ul>
			<?php } if ($section == 'maintenance') { ?>
			<ul class="nav nav-tabs" role="tablist">
				<li<?php if($page == 'maintenance') echo ' class="active"' ?>><a href="maintenance.php"><span class="fa fa-fw fa-coffee"></span><span class="hidden-xs"> <?php _e('Maintenance', 'luna') ?></span></a></li>
				<li<?php if($page == 'prune') echo ' class="active"' ?>><a href="prune.php"><span class="fa fa-fw fa-recycle"></span><span class="hidden-xs"> <?php _e('Prune', 'luna') ?></span></a></li>
				<li<?php if($page == 'database') echo ' class="active"' ?>><a href="database.php"><span class="fa fa-fw fa-database"></span><span class="hidden-xs"> <?php _e('Database', 'luna') ?></span></a></li>
			</ul>
			<?php } ?>
		</div>
	</div>
</div>
<div class="content">
	<div class="container">
<?php

}

function check_is_admin() {
	global $luna_user;

	$is_admin = $luna_user['g_id'] == LUNA_ADMIN ? true : false;
	
	return $is_admin;
}