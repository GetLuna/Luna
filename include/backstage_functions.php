<?php

/*
 * Copyright (C) 2013-2015 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

//
// Display the admin navigation menu
//
function load_admin_nav($section, $page) {
	global $luna_user, $lang, $luna_config, $is_admin;

	// What page are we on?
	if ($page == 'index')
		$page_title = '<span class="fa fa-fw fa-tachometer"></span> '.$lang['Backstage'];
	elseif ($page == 'stats')
		$page_title = '<span class="fa fa-fw fa-info-circle"></span> '.$lang['System info'];
	elseif ($page == 'update')
		$page_title = '<span class="fa fa-fw fa-cloud-upload"></span> '.$lang['Luna update'];
	elseif ($page == 'about')
		$page_title = '<span class="fa fa-fw fa-moon-o"></span> '.$lang['About Luna'];

	elseif ($page == 'board')
		$page_title = '<span class="fa fa-fw fa-sort-amount-desc"></span> '.$lang['Board'];
	elseif ($page == 'moderate')
		$page_title = '<span class="fa fa-fw fa-tasks"></span> '.$lang['Moderate'];
	elseif ($page == 'censoring')
		$page_title = '<span class="fa fa-fw fa-eye-slash"></span> '.$lang['Censoring'];
	elseif ($page == 'reports')
		$page_title = '<span class="fa fa-fw fa-exclamation-triangle"></span> '.$lang['Reports'];

	elseif ($page == 'users')
		$page_title = '<span class="fa fa-fw fa-search"></span> '.$lang['Search'];
	elseif ($page == 'tools')
		$page_title = '<span class="fa fa-fw fa-wrench"></span> '.$lang['Tools'];
	elseif ($page == 'ranks')
		$page_title = '<span class="fa fa-fw fa-chevron-up"></span> '.$lang['Ranks'];
	elseif ($page == 'groups')
		$page_title = '<span class="fa fa-fw fa-group"></span> '.$lang['Groups'];
	elseif ($page == 'permissions')
		$page_title = '<span class="fa fa-fw fa-check-circle"></span> '.$lang['Permissions'];
	elseif ($page == 'bans')
		$page_title = '<span class="fa fa-fw fa-ban"></span> '.$lang['Bans'];

	elseif ($page == 'settings')
		$page_title = '<span class="fa fa-fw fa-cogs"></span> '.$lang['Settings'];
	elseif ($page == 'features')
		$page_title = '<span class="fa fa-fw fa-sliders"></span> '.$lang['Features'];
	elseif ($page == 'appearance')
		$page_title = '<span class="fa fa-fw fa-eye"></span> '.$lang['Appearance'];
	elseif ($page == 'registration')
		$page_title = '<span class="fa fa-fw fa-plus-circle"></span> '.$lang['Registration'];
	elseif ($page == 'email')
		$page_title = '<span class="fa fa-fw fa-envelope"></span> '.$lang['Email'];
	elseif ($page == 'menu')
		$page_title = '<span class="fa fa-fw fa-bars"></span> '.$lang['Menu'];
	elseif ($page == 'theme')
		$page_title = '<span class="fa fa-fw fa-paint-brush"></span> '.$lang['Theme'];

	elseif ($page == 'maintenance')
		$page_title = '<span class="fa fa-fw fa-coffee"></span> '.$lang['Maintenance'];
	elseif ($page == 'prune')
		$page_title = '<span class="fa fa-fw fa-recycle"></span> '.$lang['Prune'];
	elseif ($page == 'database')
		$page_title = '<span class="fa fa-fw fa-database"></span> '.$lang['Database management'];
		
	else
		$page_title = $page;

?>
<nav class="navbar navbar-fixed-top navbar-default" role="navigation">
	<div class="container">
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
				<li class="<?php if ($section == 'backstage') echo 'active'; ?>"><a href="index.php"><span class="fa fa-fw fa-dashboard"></span> <?php echo $lang['Backstage'] ?></a></li>
				<li class="<?php if ($section == 'content') echo 'active'; ?>"><a href="board.php"><span class="fa fa-fw fa-file"></span> <?php echo $lang['Content'] ?></a></li>
				<li class="<?php if ($section == 'users') echo 'active'; ?>"><a href="users.php"><span class="fa fa-fw fa-users"></span> <?php echo $lang['Users'] ?></a></li>
				<li class="<?php if ($section == 'settings') echo 'active'; ?>"><a href="settings.php"><span class="fa fa-fw fa-cog"></span> <?php echo $lang['Settings'] ?></a></li>
				<li class="<?php if ($section == 'maintenance') echo 'active'; ?>"><a href="maintenance.php"><span class="fa fa-fw fa-coffee"></span> <?php echo $lang['Maintenance'] ?></a></li>	
<?php

	// See if there are any plugins
	$plugins = forum_list_plugins($is_admin);

	// Did we find any plugins?
	if (!empty($plugins))
	{
?>
				<li class="dropdown <?php if ($section == 'extensions') echo 'active'; ?>">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<span class="fa fa-fw fa-cogs"></span> <?php echo $lang['Extensions'] ?> <span class="fa fa-fw fa-angle-down">
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
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown usermenu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<?php print(luna_htmlspecialchars($luna_user['username'])) ?> <?php echo draw_user_avatar($luna_user['id'], 'avatar'); ?> <span class="fa fa-fw fa-angle-down"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="../profile.php?id=<?php echo $luna_user['id'] ?>"><?php echo $lang['Profile'] ?></a></li>
						<li><a href="../settings.php?id=<?php echo ''.$luna_user['id'] ?>"><?php echo $lang['Settings'] ?></a></li>
						<li class="divider"></li>
						<li><a href="../help.php"><?php echo $lang['Help'] ?></a></li>
						<li><a href="http://getluna.org"><?php echo $lang['Support'] ?></a></li>
						<li class="divider"></li>
						<li><a href="../login.php?action=out&amp;id=<?php echo ''.$luna_user['id'].'&amp;csrf_token='.luna_hash($luna_user['id'].luna_hash(get_remote_address())) ?>"><?php echo $lang['Logout'] ?></a></li>
					</ul>
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
						echo '<span class="pull-right" style="font-size: 70%;">Core '.Version::FORUM_CORE_VERSION.'</span>';
				?>
			</h2>
			<?php if ($section == 'backstage') { ?>
			<ul class="nav nav-tabs" role="tablist">
				<li<?php if($page == 'index') echo ' class="active"' ?>><a href="index.php"><span class="fa fa-fw fa-tachometer"></span><span class="hidden-xs"> <?php echo $lang['Backstage'] ?></span></a></li>
				<li<?php if($page == 'stats') echo ' class="active"' ?>><a href="system.php"><span class="fa fa-fw fa-info-circle"></span><span class="hidden-xs"> <?php echo $lang['System info'] ?></span></a></li>
				<li<?php if($page == 'update') echo ' class="active"' ?>><a href="update.php"><span class="fa fa-fw fa-cloud-upload"></span><span class="hidden-xs"> <?php echo $lang['Update'] ?></span></a></li>
				<li class="pull-right<?php if($page == 'about') echo ' active' ?>"><a href="about.php"><span class="fa fa-fw fa-moon-o"></span><span class="hidden-xs"> <?php echo $lang['About'] ?></span></a></li>
			</ul>
			<?php } if ($section == 'content') { ?>
			<ul class="nav nav-tabs" role="tablist">
				<li<?php if($page == 'board') echo ' class="active"' ?>><a href="board.php"><span class="fa fa-fw fa-sort-amount-desc"></span><span class="hidden-xs"> <?php echo $lang['Board'] ?></span></a></li>
				<li<?php if($page == 'moderate') echo ' class="active"' ?>><a href="moderate.php"><span class="fa fa-fw fa-tasks"></span><span class="hidden-xs"> <?php echo $lang['Moderate'] ?></span></a></li>
				<li<?php if($page == 'censoring') echo ' class="active"' ?>><a href="censoring.php"><span class="fa fa-fw fa-eye-slash"></span><span class="hidden-xs"> <?php echo $lang['Censoring'] ?></span></a></li>
				<li<?php if($page == 'reports') echo ' class="active"' ?>><a href="reports.php"><span class="fa fa-fw fa-exclamation-triangle"></span><span class="hidden-xs"> <?php echo $lang['Reports'] ?></span></a></li>
			</ul>
			<?php } if ($section == 'users') { ?>
			<ul class="nav nav-tabs" role="tablist">
				<li<?php if($page == 'users') echo ' class="active"' ?>><a href="users.php"><span class="fa fa-fw fa-search"></span><span class="hidden-xs"> <?php echo $lang['Search'] ?></span></a></li>
				<li<?php if($page == 'tools') echo ' class="active"' ?>><a href="tools.php"><span class="fa fa-fw fa-wrench"></span><span class="hidden-xs"> <?php echo $lang['Tools'] ?></span></a></li>
				<li<?php if($page == 'ranks') echo ' class="active"' ?>><a href="ranks.php"><span class="fa fa-fw fa-chevron-up"></span><span class="hidden-xs"> <?php echo $lang['Ranks'] ?></span></a></li>
				<li<?php if($page == 'groups') echo ' class="active"' ?>><a href="groups.php"><span class="fa fa-fw fa-group"></span><span class="hidden-xs"> <?php echo $lang['Groups'] ?></span></a></li>
				<li<?php if($page == 'permissions') echo ' class="active"' ?>><a href="permissions.php"><span class="fa fa-fw fa-check-circle"></span><span class="hidden-xs"> <?php echo $lang['Permissions'] ?></span></a></li>
				<li<?php if($page == 'bans') echo ' class="active"' ?>><a href="bans.php"><span class="fa fa-fw fa-ban"></span><span class="hidden-xs"> <?php echo $lang['Bans'] ?></span></a></li>
			</ul>
			<?php } if ($section == 'settings') { ?>
			<ul class="nav nav-tabs" role="tablist">
				<li<?php if($page == 'settings') echo ' class="active"' ?>><a href="settings.php"><span class="fa fa-fw fa-cogs"></span><span class="hidden-xs"> <?php echo $lang['Settings'] ?></span></a></li>
				<li<?php if($page == 'features') echo ' class="active"' ?>><a href="features.php"><span class="fa fa-fw fa-sliders"></span><span class="hidden-xs"> <?php echo $lang['Features'] ?></span></a></li>
				<li<?php if($page == 'appearance') echo ' class="active"' ?>><a href="appearance.php"><span class="fa fa-fw fa-eye"></span><span class="hidden-xs"> <?php echo $lang['Appearance'] ?></span></a></li>
				<li<?php if($page == 'registration') echo ' class="active"' ?>><a href="registration.php"><span class="fa fa-fw fa-plus-circle"></span><span class="hidden-xs"> <?php echo $lang['Registration'] ?></span></a></li>
				<li<?php if($page == 'email') echo ' class="active"' ?>><a href="email.php"><span class="fa fa-fw fa-envelope"></span><span class="hidden-xs"> <?php echo $lang['Email'] ?></span></a></li>
				<li<?php if($page == 'menu') echo ' class="active"' ?>><a href="menu.php"><span class="fa fa-fw fa-bars"></span><span class="hidden-xs"> <?php echo $lang['Menu'] ?></span></a></li>
				<li<?php if($page == 'theme') echo ' class="active"' ?>><a href="theme.php"><span class="fa fa-fw fa-paint-brush"></span><span class="hidden-xs"> <?php echo $lang['Theme'] ?></span></a></li>
			</ul>
			<?php } if ($section == 'maintenance') { ?>
			<ul class="nav nav-tabs" role="tablist">
				<li<?php if($page == 'maintenance') echo ' class="active"' ?>><a href="maintenance.php"><span class="fa fa-fw fa-coffee"></span><span class="hidden-xs"> <?php echo $lang['Maintenance'] ?></span></a></li>
				<li<?php if($page == 'prune') echo ' class="active"' ?>><a href="prune.php"><span class="fa fa-fw fa-recycle"></span><span class="hidden-xs"> <?php echo $lang['Prune'] ?></span></a></li>
				<li<?php if($page == 'database') echo ' class="active"' ?>><a href="database.php"><span class="fa fa-fw fa-database"></span><span class="hidden-xs"> <?php echo $lang['Database'] ?></span></a></li>
			</ul>
			<?php } ?>
		</div>
	</div>
</div>
<div class="content">
	<div class="container">
		<div class="row">

<?php

}