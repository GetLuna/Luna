<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

    require_once FORUM_ROOT.'include/parser.php';
    $preview_message = parse_message($message, $hide_smilies);

?>

<div class="postview">
    <div class="row topic">
        <div class="col-md-3">
            <div class="profile-card">
                <div class="profile-card-head profile-card-quickpost">
                    <div class="user-avatar thumbnail <?php if (!$user_avatar) echo 'noavatar'?>">
                        <?php if ($user_avatar != '') echo "\t\t\t\t\t\t".$user_avatar."\n"; ?>
                    </div>
                    <h2 <?php if (!$user_avatar) echo 'class="noavatar"'; ?>><?php echo $username ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-9">
			<div class="panel panel-default panel-border">
				<div class="panel-heading">
					<div class="comment-arrow hidden-sm hidden-xs"></div>
					<h3 class="panel-title"><?php echo $lang['Post preview'] ?></h3>
				</div>
				<div class="panel-body">
					<?php echo $preview_message."\n" ?>
				</div>
			</div>
		</div>
	</div>
</div>