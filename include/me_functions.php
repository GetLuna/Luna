<?php

/*
 * Copyright (C) 2013-2015 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

// Display the me navigation
function load_me_nav($page = '') {
	global $luna_config, $luna_user, $id;

?>
<div class="hidden-xs">
	<div class="list-group">
		<a class="<?php if ($page == 'profile') echo 'active'; ?> list-group-item" href="profile.php?id=<?php echo $id ?>"><?php _e('Profile', 'luna') ?></a>
	</div>
	<?php if ($luna_user['id'] == $id && !$luna_user['is_guest'] || ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1')) || ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1'))): ?>
		<div class="list-group">
			<?php if ($luna_config['o_pms_enabled'] == '1' && $luna_user['g_pm'] == '1' && $luna_user['use_pm'] == '1'): ?>
				<a class="<?php if ($page == 'inbox') echo 'active'; ?> list-group-item" href="inbox.php"><?php _e('Inbox', 'luna') ?></a>
			<?php endif; ?>
			<a class="<?php if ($page == 'notifications') echo 'active'; ?> list-group-item" href="notifications.php"><?php _e('Notifications', 'luna') ?></a>
		</div>
		<div class="list-group">
			<a class="<?php if ($page == 'settings') echo 'active'; ?> list-group-item" href="settings.php?id=<?php echo $id ?>"><?php _e('Settings', 'luna') ?></a>
		</div>
<?php endif; ?>
</div>

<ul class="nav nav-tabs visible-xs-block">
	<li class="<?php if ($page == 'profile') echo 'active'; ?>"><a href="profile.php?id=<?php echo $id ?>"><?php _e('Profile', 'luna') ?></a></li>
<?php if ($luna_user['id'] == $id && !$luna_user['is_guest'] || ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1')) || ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1'))): ?>
	<?php if ($luna_config['o_pms_enabled'] == '1' && $luna_user['g_pm'] == '1' && $luna_user['use_pm'] == '1'): ?>
		<li class="<?php if ($page == 'inbox') echo 'active'; ?>"><a href="inbox.php"><?php _e('Inbox', 'luna') ?></a></li>
	<?php endif; ?>
	<li class="<?php if ($page == 'notifications') echo 'active'; ?>"><a href="notifications.php"><?php _e('Notifications', 'luna') ?></a></li>
	<li class="<?php if ($page == 'settings') echo 'active'; ?>"><a href="settings.php?id=<?php echo $id ?>"><?php _e('Settings', 'luna') ?></a></li>
<?php endif; ?>
</ul>
<?php 
}