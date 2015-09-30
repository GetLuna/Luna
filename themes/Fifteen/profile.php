<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<div class="col-sm-3 profile-nav">
	<div class="user-card-profile">
		<h3 class="user-card-title"><?php echo luna_htmlspecialchars($user['username']) ?></h3>
		<span class="user-card-avatar thumbnail">
			<?php echo $avatar_user_card ?>
		</span>
	</div>
<?php
	load_me_nav('profile');
?>
</div>
<div class="profile col-sm-9">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('About user', 'luna') ?></h3>
		</div>
		<div class="panel-body">
			<?php echo implode("\n\t\t\t\t\t\t\t".'<br />', $user_personality)."\n" ?>
		</div>
	</div>
<?php if (!empty($user_messaging)): ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Contact', 'luna'); ?></h3>
		</div>
		<div class="panel-body">
			<p><?php echo implode("\n\t\t\t\t\t\t\t".'<br />', $user_messaging)."\n" ?></p>
		</div>
	</div>
<?php
endif;

if ($luna_config['o_signatures'] == '1') {
	if (isset($parsed_signature)) {
?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Signature', 'luna'); ?></h3>
		</div>
		<div class="panel-body">
			<?php echo $user_signature ?>
		</div>
	</div>
<?php
	}
}
?>
</div>