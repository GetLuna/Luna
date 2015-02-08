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
					<div class="user-avatar thumbnail is-online">
						<?php echo generate_avatar_markup($luna_user['id']) ?>
					</div>
					<h2><?php echo $luna_user['username'] ?></h2>
					<h3><?php echo get_title($luna_user) ?></h3>
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