<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>
<div class="row">
	<div class="col-md-8 col-sm-12">
		<?php draw_category_list() ?>
	</div>
    <div class="col-md-4 hidden-xs sidebar">
        <div class="first-run panel panel-default">
            <div class="row first-run-content">
                <div class="col-xs-12 first-run-profile">
                    <div class="media">
                        <a class="pull-left" href="#">
                            <?php echo $user_avatar; ?>
                        </a>
                        <div class="media-body">
                            <h2 class="first-run-title"><small>Welcome back,</small><br /><?php echo luna_htmlspecialchars($luna_user['username']) ?></h2>
                            <span class="btn-group btn-group-sidebar pull-right"><a href="me.php?id=<?php echo $luna_user['id'] ?>" class="btn btn-default"><i class="fa fa-cogs"></i></a><a href="login.php" class="btn btn-default"><i class="fa fa-sign-out"></i></a></span>
                        </div>
                    </div>
                </div>
                <?php if ((!$luna_user['is_guest'] && ($luna_user['first_run'] == '0'))) { ?>
                <div class="col-xs-12">
                    <h3 class="first-run-forumtitle"><?php echo sprintf($lang['Welcome to'], $luna_config['o_board_title']) ?></h3>
                    <p><?php echo $luna_config['o_first_run_message']; ?></p>
                </div>
                <div class="col-xs-12">
                    <div class="list-group first-run-list">
                        <a href="profile.php?action=upload_avatar&id=<?php echo $luna_user['id'] ?>" class="list-group-item"><?php echo $lang['Change your avatar'] ?></a>
                        <a href="profile.php?section=personality&id=<?php echo $luna_user['id'] ?>" class="list-group-item"><?php echo $lang['Extend profile'] ?></a>
                        <a href="help.php" class="list-group-item"><?php echo $lang['Get help'] ?></a>
                        <a href="index.php?action=do_not_show&id=<?php echo $luna_user['id'] ?>" class="list-group-item active"><?php echo $lang['Do not show again'] ?></a>
                    </div>
                </div>
                <?php } else if ($luna_user['is_guest']) { ?>
                <div class="col-xs-12">
                    <h3 class="first-run-forumtitle"><?php echo sprintf($lang['Welcome to'], $luna_config['o_board_title']) ?></h3>
                    <div class="list-group first-run-list">
                        <a href="#" data-toggle="modal" data-target="#login" class="list-group-item"><?php echo $lang['Login'] ?></a>
                        <a href="register.php" class="list-group-item"><?php echo $lang['Register'] ?></a>
                        <a href="login.php?action=forget" class="list-group-item"><?php echo $lang['Forgotten pass'] ?></a>
                    </div>
                </div>
                <?php } ?>
            </div>
            <!--
            <div class="panel panel-default">
            	<div class="panel-heading">
                	<h3 class="panel-title">Notifications</h3>
                </div>
                <div class="panel-body">
                	<p>No notifications</p>
                </div>
            </div>
            -->
        </div>
	</div>
</div>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2 col-sm-4 col-xs-6"><div class="statistic-item"><?php total_users() ?></div><div class="statistic-grey"><?php echo $lang['No of users'] ?></div></div>
            <div class="col-md-2 col-sm-4 col-xs-6"><div class="statistic-item"><?php total_topics() ?></div><div class="statistic-grey"><?php echo $lang['No of topics'] ?></div></div>
            <div class="col-md-2 col-sm-4 col-xs-6"><div class="statistic-item"><?php total_posts() ?></div><div class="statistic-grey"><?php echo $lang['No of post'] ?></div></div>
            <div class="col-md-2 col-sm-4 col-xs-6"><div class="statistic-item"><?php newest_user() ?></div><div class="statistic-grey"><?php echo $lang['Newest user'] ?></div></div>
			<div class="col-md-2 col-sm-4 col-xs-6"><div class="statistic-item"><?php users_online() ?></div><div class="statistic-grey"><?php echo $lang['Users online'] ?></div></div>
			<div class="col-md-2 col-sm-4 col-xs-6"><div class="statistic-item"><?php guests_online() ?></div><div class="statistic-grey"><?php echo $lang['Guests online'] ?></div></div>
        </div>
	</div>
	<div class="panel-footer">
		<span class="users-online"><?php online_list() ?></span>
    </div>
</div>