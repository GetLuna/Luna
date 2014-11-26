<?php

/*
 * Copyright (C) 2013-2014 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

//
// Display the admin navigation menu
//
function load_admin_nav($section, $page) {
	global $luna_user, $lang, $luna_config;

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
            <a class="navbar-brand" href="../index.php"><span class="fa fa-arrow-left hidden-xs"></span><span class="visible-xs-inline">Luna</span></a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="<?php if ($section == 'backstage') echo 'active'; ?>"><a href="index.php"><span class="fa fa-dashboard"></span> <?php echo $lang['Backstage'] ?></a></li>
                <li class="<?php if ($section == 'content') echo 'active'; ?>"><a href="board.php"><span class="fa fa-file"></span> <?php echo $lang['Content'] ?></a></li>
                <li class="<?php if ($section == 'users') echo 'active'; ?>"><a href="users.php"><span class="fa fa-users"></span> <?php echo $lang['Users'] ?></a></li>
                <li class="<?php if ($section == 'settings') echo 'active'; ?>"><a href="settings.php"><span class="fa fa-cog"></span> <?php echo $lang['Settings'] ?></a></li>		
<?php

	// See if there are any plugins
	$plugins = forum_list_plugins($is_admin);

	// Did we find any plugins?
	if (!empty($plugins))
	{
?>
                <li class="dropdown <?php if ($section == 'extensions') echo 'active'; ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="fa fa-cogs"></span> <?php echo $lang['Extensions'] ?> <span class="fa fa-angle-down">
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
                        <?php print(luna_htmlspecialchars($luna_user['username'])) ?> <span class="fa fa-angle-down"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo '<a href="../profile.php?id='.$luna_user['id'].'">' ?><?php echo $lang['Profile'] ?></a></li>
                        <li><a href="http://modernbb.be"><?php echo $lang['Support'] ?></a></li>
                        <li class="divider"></li>
                        <li><?php echo '<a href="../login.php?action=out&amp;id='.$luna_user['id'].'&amp;csrf_token='.luna_hash($luna_user['id'].luna_hash(get_remote_address())).'">' ?><?php echo $lang['Logout'] ?></a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="jumbotron jumboheader">
    <div class="container">
        <div class="row">
            <h2>
                <?php
                    if ($page == 'index')
                        echo '<span class="fa fa-fw fa-tachometer"></span> Backstage';
                    else if ($page == 'stats')
                        echo '<span class="fa fa-fw fa-info-circle"></span> System info';
                    else if ($page == 'update')
                        echo '<span class="fa fa-fw fa-cloud-upload"></span> Luna software update';

                    else if ($page == 'board')
                        echo '<span class="fa fa-fw fa-sort-amount-desc"></span> Board structure';
                    else if ($page == 'moderate')
                        echo '<span class="fa fa-fw fa-tasks"></span> Moderate';
                    else if ($page == 'censoring')
                        echo '<span class="fa fa-fw fa-eye-slash"></span> Censoring';
                    else if ($page == 'reports')
                        echo '<span class="fa fa-fw fa-exclamation-triangle"></span> Reports';

                    else if ($page == 'users')
                        echo '<span class="fa fa-fw fa-search"></span> User search';
                    else if ($page == 'ranks')
                        echo '<span class="fa fa-fw fa-chevron-up"></span> Ranks';
                    else if ($page == 'groups')
                        echo '<span class="fa fa-fw fa-group"></span> Groups';
                    else if ($page == 'permissions')
                        echo '<span class="fa fa-fw fa-check-circle"></span> Permissions';
                    else if ($page == 'bans')
                        echo '<span class="fa fa-fw fa-ban"></span> Bans';

                    else if ($page == 'settings')
                        echo '<span class="fa fa-fw fa-cogs"></span> Settings';
                    else if ($page == 'features')
                        echo '<span class="fa fa-fw fa-sliders"></span> Features';
                    else if ($page == 'registration')
                        echo '<span class="fa fa-fw fa-plus-circle"></span> Registration';
                    else if ($page == 'email')
                        echo '<span class="fa fa-fw fa-envelope"></span> Email';
                    else if ($page == 'appearance')
                        echo '<span class="fa fa-fw fa-eye"></span> Appearance';
                    else if ($page == 'menu')
                        echo '<span class="fa fa-fw fa-bars"></span> Menu';
                    else if ($page == 'theme')
                        echo '<span class="fa fa-fw fa-paint-brush"></span> Theme';
                    else if ($page == 'maintenance')
                        echo '<span class="fa fa-fw fa-coffee"></span> Maintenance';
                    else if ($page == 'zsettings')
                        echo '<span class="fa fa-fw fa-cogs"></span> zSettings';

                    else if ($page == 'database')
                        echo 'Database management';
						
					else
						echo $page;
						
					echo '<span class="pull-right" style="font-size: 70%;">Core '.Version::FORUM_CORE_VERSION.'</span>';
                ?>
            </h2>
            <?php if ($section == 'backstage') { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li<?php if($page == 'index') echo ' class="active"' ?>><a href="index.php"><span class="fa fa-tachometer"></span> Backstage</a></li>
                <li<?php if($page == 'stats') echo ' class="active"' ?>><a href="system.php"><span class="fa fa-info-circle"></span> System info</a></li>
                <li<?php if($page == 'update') echo ' class="active"' ?>><a href="update.php"><span class="fa fa-cloud-upload"></span> Update</a></li>
            </ul>
            <?php } if ($section == 'content') { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li<?php if($page == 'board') echo ' class="active"' ?>><a href="board.php"><span class="fa fa-sort-amount-desc"></span> Board structure</a></li>
                <li<?php if($page == 'moderate') echo ' class="active"' ?>><a href="moderate.php"><span class="fa fa-tasks"></span> Moderate</a></li>
                <li<?php if($page == 'censoring') echo ' class="active"' ?>><a href="censoring.php"><span class="fa fa-eye-slash"></span> Censoring</a></li>
                <li<?php if($page == 'reports') echo ' class="active"' ?>><a href="reports.php"><span class="fa fa-exclamation-triangle"></span> Reports</a></li>
            </ul>
            <?php } if ($section == 'users') { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li<?php if($page == 'users') echo ' class="active"' ?>><a href="users.php"><span class="fa fa-search"></span> User search</a></li>
                <li<?php if($page == 'ranks') echo ' class="active"' ?>><a href="ranks.php"><span class="fa fa-chevron-up"></span> Ranks</a></li>
                <li<?php if($page == 'groups') echo ' class="active"' ?>><a href="groups.php"><span class="fa fa-group"></span> Groups</a></li>
                <li<?php if($page == 'permissions') echo ' class="active"' ?>><a href="permissions.php"><span class="fa fa-check-circle"></span> Permissions</a></li>
                <li<?php if($page == 'bans') echo ' class="active"' ?>><a href="bans.php"><span class="fa fa-ban"></span> Bans</a></li>
            </ul>
            <?php } if ($section == 'settings') { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li<?php if($page == 'settings') echo ' class="active"' ?>><a href="settings.php"><span class="fa fa-cogs"></span> Settings</a></li>
                <li<?php if($page == 'features') echo ' class="active"' ?>><a href="features.php"><span class="fa fa-sliders"></span> Features</a></li>
                <li<?php if($page == 'registration') echo ' class="active"' ?>><a href="registration.php"><span class="fa fa-plus-circle"></span> Registration</a></li>
                <li<?php if($page == 'email') echo ' class="active"' ?>><a href="email.php"><span class="fa fa-envelope"></span> Email</a></li>
                <li<?php if($page == 'appearance') echo ' class="active"' ?>><a href="appearance.php"><span class="fa fa-eye"></span> Appearance</a></li>
                <li<?php if($page == 'menu') echo ' class="active"' ?>><a href="menu.php"><span class="fa fa-bars"></span> Menu</a></li>
                <li<?php if($page == 'theme') echo ' class="active"' ?>><a href="theme.php"><span class="fa fa-paint-brush"></span> Theme</a></li>
                <li<?php if($page == 'maintenance') echo ' class="active"' ?>><a href="maintenance.php"><span class="fa fa-coffee"></span> Maintenance</a></li>
				<?php if (file_exists('../z.txt')) { ?>
                <li<?php if($page == 'zsettings') echo ' class="active"' ?>><a href="zsettings.php"><span class="fa fa-cogs"></span> zSettings</a></li>
				<?php } ?>
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