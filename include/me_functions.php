<?php

/*
 * Copyright (C) 2013-2016 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

// Display the me navigation
function load_me_nav($page = '') {
	global $luna_config, $luna_user, $id;

?>
<div class="list-group list-group-luna">
	<a class="<?php if ($page == 'profile') echo 'active'; ?> list-group-item" href="profile.php?id=<?php echo $id ?>"><?php _e('Profile', 'luna') ?></a>
<?php if ($luna_user['id'] == $id && !$luna_user['is_guest'] || ($luna_user['g_id'] == LUNA_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1')) || ($luna_user['g_id'] == LUNA_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1'))): ?>
	<?php if ($luna_config['o_enable_inbox'] == '1' && $luna_user['g_inbox'] == '1' && $luna_user['use_inbox'] == '1'): ?>
		<a class="<?php if ($page == 'inbox') echo 'active'; ?> list-group-item" href="inbox.php"><?php _e('Inbox', 'luna') ?></a>
	<?php endif; ?>
	<a class="<?php if ($page == 'notifications') echo 'active'; ?> list-group-item" href="notifications.php"><?php _e('Notifications', 'luna') ?></a>
	<a class="<?php if ($page == 'settings') echo 'active'; ?> list-group-item" href="settings.php?id=<?php echo $id ?>"><?php _e('Settings', 'luna') ?></a>
<?php endif; ?>
</div>
<?php
}