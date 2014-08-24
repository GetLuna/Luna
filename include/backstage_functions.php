<?php

/**
 * Copyright (C) 2013-2014 ModernBB
 * License: http://opensource.org/licenses/MIT MIT
 */

//
// Display the admin navigation menu
//
function generate_admin_menu($section ='', $page = '')
{
	global $luna_config, $luna_user, $lang;

	$is_admin = $luna_user['g_id'] == FORUM_ADMIN ? true : false;

?>
<nav class="navbar navbar-fixed-top navbar-default" role="navigation">
    <div class="nav-inner container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand visible-xs-inline" href="../index.php">ModernBB</a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
            	<li class="hidden-xs"><a href="../"><span class="glyphicon glyphicon-chevron-left"></span></a></li>
                <li class="<?php if ($section == 'backstage') echo 'active'; ?>"><a href="index.php"><span class="fa fa-dashboard"></span> <?php echo $lang['Backstage'] ?></a></li>
                <li class="<?php if ($section == 'content') echo 'active'; ?>"><a href="board.php"><span class="fa fa-file"></span> <?php echo $lang['Content'] ?></a></li>
                <li class="<?php if ($section == 'users') echo 'active'; ?>"><a href="users.php"><span class="fa fa-users"></span> <?php echo $lang['Users'] ?></a></li>
                <li class="<?php if ($section == 'settings') echo 'active'; ?>"><a href="settings.php"><span class="fa fa-cog"></span> <?php echo $lang['Settings'] ?></a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown usermenu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php print(luna_htmlspecialchars($luna_user['username'])) ?> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo '<a href="../profile.php?id='.$luna_user['id'].'">' ?><?php echo $lang['Profile'] ?></a></li>
                        <li class="divider"></li>
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
                    if($page == 'index')
                        echo 'Backstage';
                    if($page == 'stats')
                        echo 'System info';
                    if($page == 'update')
                        echo 'Luna software update';
                    if($page == 'about')
                        echo 'About Luna '.$luna_config['o_forum_version'].' Preview';

                    if($page == 'board')
                        echo 'Board structure';
                    if($page == 'censoring')
                        echo 'Censoring';
                    if($page == 'reports')
                        echo 'Reports';

                    if($page == 'users')
                        echo 'Users';
                    if($page == 'ranks')
                        echo 'Ranks';
                    if($page == 'groups')
                        echo 'Groups';
                    if($page == 'permissions')
                        echo 'Permissions';
                    if($page == 'bans')
                        echo 'Bans';

                    if($page == 'settings')
                        echo 'Global settings';
                    if($page == 'features')
                        echo 'Features';
                    if($page == 'registration')
                        echo 'Registration';
                    if($page == 'email')
                        echo 'Email';
                    if($page == 'appearance')
                        echo 'Appearance';
                    if($page == 'theme')
                        echo 'Theme';
                    if($page == 'maintenance')
                        echo 'Maintenance';
                ?>
            </h2>
            <?php if ($section == 'backstage') { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li<?php if($page == 'index') echo ' class="active"' ?>><a href="index.php">Backstage</a></li>
                <li<?php if($page == 'stats') echo ' class="active"' ?>><a href="statistics.php">System info</a></li>
                <li<?php if($page == 'update') echo ' class="active"' ?>><a href="update.php">Update</a></li>
                <li class="pull-right<?php if($page == 'about') echo ' active' ?>"><a href="about.php">About</a></li>
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
                <li<?php if($page == 'theme') echo ' class="active"' ?>><a href="theme.php">Theme</a></li>
                <li<?php if($page == 'maintenance') echo ' class="active"' ?>><a href="maintenance.php">Maintenance</a></li>
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