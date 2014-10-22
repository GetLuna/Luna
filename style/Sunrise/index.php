<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="first-run panel panel-default">
    <div class="row first-run-content">
        <?php if (!$luna_user['is_guest']) { ?>
        <div class="col-md-4 col-sm-6 first-run-profile">
        <?php } else { ?>
        <div class="col-md-8 col-sm-6 first-run-profile">
        <?php } ?>
            <h3 class="first-run-title"><?php echo sprintf($lang['Hi there'], luna_htmlspecialchars($luna_user['username'])) ?></h3>
            <span class="first-run-avatar thumbnail">
                <?php echo $user_avatar ?>
            </span>
        </div>
        <?php if (!$luna_user['is_guest']) { ?>
        <div class="col-md-4 hidden-sm">
            <h3 class="first-run-forumtitle"><?php echo sprintf($lang['Welcome to'], $luna_config['o_board_title']) ?></h3>
            <p><?php echo $luna_config['o_first_run_message']; ?></p>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="list-group first-run-list">
                <a href="profile.php?action=upload_avatar&id=<?php echo $luna_user['id'] ?>" class="list-group-item"><?php echo $lang['Change your avatar'] ?></a>
                <a href="profile.php?section=personality&id=<?php echo $luna_user['id'] ?>" class="list-group-item"><?php echo $lang['Extend profile'] ?></a>
                <a href="help.php" class="list-group-item"><?php echo $lang['Get help'] ?></a>
                <a href="index.php?action=do_not_show&id=<?php echo $luna_user['id'] ?>" class="list-group-item active"><?php echo $lang['Do not show again'] ?></a>
            </div>
        </div>
        <?php } else { ?>
        <div class="col-md-4 hidden-sm">
            <h3 class="first-run-forumtitle"><?php echo sprintf($lang['Welcome to'], $luna_config['o_board_title']) ?></h3>
            <div class="list-group first-run-list">
                <a href="#" data-toggle="modal" data-target="#login" class="list-group-item"><?php echo $lang['Login'] ?></a>
                <a href="register.php" class="list-group-item"><?php echo $lang['Register'] ?></a>
                <a href="login.php?action=forget" class="list-group-item"><?php echo $lang['Forgotten pass'] ?></a>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<?php draw_category_list() ?>
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