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
                        echo '<i class="fa fa-tachometer"></i> Backstage';
                    else if ($page == 'stats')
                        echo '<i class="fa fa-info-circle"></i> System info';
                    else if ($page == 'update')
                        echo '<i class="fa fa-cloud-upload"></i> Luna software update';

                    else if ($page == 'board')
                        echo '<i class="fa fa-sort-amount-desc"></i> Board structure';
                    else if ($page == 'censoring')
                        echo '<i class="fa fa-eye-slash"></i> Censoring';
                    else if ($page == 'reports')
                        echo '<i class="fa fa-exclamation-triangle"></i> Reports';

                    else if ($page == 'users')
                        echo '<i class="fa fa-search"></i> User search';
                    else if ($page == 'ranks')
                        echo '<i class="fa fa-chevron-up"></i> Ranks';
                    else if ($page == 'groups')
                        echo '<i class="fa fa-group"></i> Groups';
                    else if ($page == 'permissions')
                        echo '<i class="fa fa-check-circle"></i> Permissions';
                    else if ($page == 'bans')
                        echo '<i class="fa fa-ban"></i> Bans';

                    else if ($page == 'settings')
                        echo '<i class="fa fa-cogs"></i> Settings';
                    else if ($page == 'features')
                        echo '<i class="fa fa-sliders"></i> Features';
                    else if ($page == 'registration')
                        echo '<i class="fa fa-plus-circle"></i> Registration';
                    else if ($page == 'email')
                        echo '<i class="fa fa-envelope"></i> Email';
                    else if ($page == 'appearance')
                        echo '<i class="fa fa-eye"></i> Appearance';
                    else if ($page == 'menu')
                        echo '<i class="fa fa-bars"></i> Menu';
                    else if ($page == 'theme')
                        echo '<i class="fa fa-paint-brush"></i> Theme';
                    else if ($page == 'maintenance')
                        echo '<i class="fa fa-coffee"></i> Maintenance';
                    else if ($page == 'private_messages')
                        echo '<i class="fa fa-comments"></i> zPrivateMessages';
                    else if ($page == 'zsettings')
                        echo '<i class="fa fa-cogs"></i> zSettings';

                    else if ($page == 'database')
                        echo 'Database management';
						
					else
						echo $page;
						
					echo '<span class="pull-right" style="font-size: 70%;">Core '.Version::FORUM_CORE_VERSION.'</span>';
                ?>
            </h2>
            <?php if ($section == 'backstage') { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li<?php if($page == 'index') echo ' class="active"' ?>><a href="index.php"><i class="fa fa-tachometer"></i> Backstage</a></li>
                <li<?php if($page == 'stats') echo ' class="active"' ?>><a href="system.php"><i class="fa fa-info-circle"></i> System info</a></li>
                <li<?php if($page == 'update') echo ' class="active"' ?>><a href="update.php"><i class="fa fa-cloud-upload"></i> Update</a></li>
            </ul>
            <?php } if ($section == 'content') { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li<?php if($page == 'board') echo ' class="active"' ?>><a href="board.php"><i class="fa fa-sort-amount-desc"></i> Board structure</a></li>
                <li<?php if($page == 'censoring') echo ' class="active"' ?>><a href="censoring.php"><i class="fa fa-eye-slash"></i> Censoring</a></li>
                <li<?php if($page == 'reports') echo ' class="active"' ?>><a href="reports.php"><i class="fa fa-exclamation-triangle"></i> Reports</a></li>
            </ul>
            <?php } if ($section == 'users') { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li<?php if($page == 'users') echo ' class="active"' ?>><a href="users.php"><i class="fa fa-search"></i> User search</a></li>
                <li<?php if($page == 'ranks') echo ' class="active"' ?>><a href="ranks.php"><i class="fa fa-chevron-up"></i> Ranks</a></li>
                <li<?php if($page == 'groups') echo ' class="active"' ?>><a href="groups.php"><i class="fa fa-group"></i> Groups</a></li>
                <li<?php if($page == 'permissions') echo ' class="active"' ?>><a href="permissions.php"><i class="fa fa-check-circle"></i> Permissions</a></li>
                <li<?php if($page == 'bans') echo ' class="active"' ?>><a href="bans.php"><i class="fa fa-ban"></i> Bans</a></li>
            </ul>
            <?php } if ($section == 'settings') { ?>
            <ul class="nav nav-tabs" role="tablist">
                <li<?php if($page == 'settings') echo ' class="active"' ?>><a href="settings.php"><i class="fa fa-cogs"></i> Settings</a></li>
                <li<?php if($page == 'features') echo ' class="active"' ?>><a href="features.php"><i class="fa fa-sliders"></i> Features</a></li>
                <li<?php if($page == 'registration') echo ' class="active"' ?>><a href="registration.php"><i class="fa fa-plus-circle"></i> Registration</a></li>
                <li<?php if($page == 'email') echo ' class="active"' ?>><a href="email.php"><i class="fa fa-envelope"></i> Email</a></li>
                <li<?php if($page == 'appearance') echo ' class="active"' ?>><a href="appearance.php"><i class="fa fa-eye"></i> Appearance</a></li>
                <li<?php if($page == 'menu') echo ' class="active"' ?>><a href="menu.php"><i class="fa fa-bars"></i> Menu</a></li>
                <li<?php if($page == 'theme') echo ' class="active"' ?>><a href="theme.php"><i class="fa fa-paint-brush"></i> Theme</a></li>
                <li<?php if($page == 'maintenance') echo ' class="active"' ?>><a href="maintenance.php"><i class="fa fa-coffee"></i> Maintenance</a></li>
				<?php if (file_exists('../z.txt')) { ?>
					<?php if ($luna_config['o_private_message'] == '1') { ?>
					<li<?php if($page == 'private_messages') echo ' class="active"' ?>><a href="private_messages.php"><i class="fa fa-comments"></i> zPrivateMessages</a></li>
					<?php } ?>
                <li<?php if($page == 'zsettings') echo ' class="active"' ?>><a href="zsettings.php"><i class="fa fa-cogs"></i> zSettings</a></li>
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