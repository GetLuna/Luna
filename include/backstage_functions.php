<?php

/*
 * Copyright (C) 2013-2014 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

//
// Display the admin navigation menu
//
function load_admin_nav($section, $page) {
	global $luna_user, $lang;

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
                <li class="dropdown<?php if ($section == 'extensions') echo 'active'; ?>">
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
                        echo 'Backstage';
                    else if ($page == 'stats')
                        echo 'System info';
                    else if ($page == 'update')
                        echo 'Luna software update';

                    else if ($page == 'board')
                        echo 'Board structure';
                    else if ($page == 'censoring')
                        echo 'Censoring';
                    else if ($page == 'reports')
                        echo 'Reports';

                    else if ($page == 'users')
                        echo 'Users';
                    else if ($page == 'ranks')
                        echo 'Ranks';
                    else if ($page == 'groups')
                        echo 'Groups';
                    else if ($page == 'permissions')
                        echo 'Permissions';
                    else if ($page == 'bans')
                        echo 'Bans';

                    else if ($page == 'settings')
                        echo 'Settings';
                    else if ($page == 'features')
                        echo 'Features';
                    else if ($page == 'registration')
                        echo 'Registration';
                    else if ($page == 'email')
                        echo 'Email';
                    else if ($page == 'appearance')
                        echo 'Appearance';
                    else if ($page == 'menu')
                        echo 'Menu';
                    else if ($page == 'theme')
                        echo 'Theme';
                    else if ($page == 'maintenance')
                        echo 'Maintenance';
                    else if ($page == 'zsettings')
                        echo 'zSettings';

                    else if ($page == 'database')
                        echo 'Database management';
						
					else
						echo $page;
						
					echo '<span class="pull-right" style="font-size: 70%;">Core '.Version::FORUM_CORE_VERSION.'</span>';
                ?>
            </h2>
            <?php if ($section == 'backstage') { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li<?php if($page == 'index') echo ' class="active"' ?>><a href="index.php">Backstage</a></li>
                <li<?php if($page == 'stats') echo ' class="active"' ?>><a href="system.php">System info</a></li>
                <li<?php if($page == 'update') echo ' class="active"' ?>><a href="update.php">Update</a></li>
            </ul>
            <?php } if ($section == 'content') { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li<?php if($page == 'board') echo ' class="active"' ?>><a href="board.php">Board structure</a></li>
                <li<?php if($page == 'censoring') echo ' class="active"' ?>><a href="censoring.php">Censoring</a></li>
                <li<?php if($page == 'reports') echo ' class="active"' ?>><a href="reports.php">Reports</a></li>
            </ul>
            <?php } if ($section == 'users') { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li<?php if($page == 'users') echo ' class="active"' ?>><a href="users.php">Users</a></li>
                <li<?php if($page == 'ranks') echo ' class="active"' ?>><a href="ranks.php">Ranks</a></li>
                <li<?php if($page == 'groups') echo ' class="active"' ?>><a href="groups.php">Groups</a></li>
                <li<?php if($page == 'permissions') echo ' class="active"' ?>><a href="permissions.php">Permissions</a></li>
                <li<?php if($page == 'bans') echo ' class="active"' ?>><a href="bans.php">Bans</a></li>
            </ul>
            <?php } if ($section == 'settings') { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li<?php if($page == 'settings') echo ' class="active"' ?>><a href="settings.php">Settings</a></li>
                <li<?php if($page == 'features') echo ' class="active"' ?>><a href="features.php">Features</a></li>
                <li<?php if($page == 'registration') echo ' class="active"' ?>><a href="registration.php">Registration</a></li>
                <li<?php if($page == 'email') echo ' class="active"' ?>><a href="email.php">Email</a></li>
                <li<?php if($page == 'appearance') echo ' class="active"' ?>><a href="appearance.php">Appearance</a></li>
                <li<?php if($page == 'menu') echo ' class="active"' ?>><a href="menu.php">Menu</a></li>
                <li<?php if($page == 'theme') echo ' class="active"' ?>><a href="theme.php">Theme</a></li>
                <li<?php if($page == 'maintenance') echo ' class="active"' ?>><a href="maintenance.php">Maintenance</a></li>
				<?php if (file_exists('../z.txt')) { ?>
                <li<?php if($page == 'zsettings') echo ' class="active"' ?>><a href="zsettings.php">zSettings</a></li>
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