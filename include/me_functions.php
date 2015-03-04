<?php

/*
 * Copyright (C) 2013-2015 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

// Display the me navigation
function load_me_nav($page = '') {
	global $lang, $luna_config, $luna_user, $id;

?>
<div class="list-group">
	<a class="<?php if ($page == 'profile') echo 'active'; ?> list-group-item" href="profile.php?id=<?php echo $id ?>">Profile</a>
	<?php if ($luna_user['id'] == $id && !$luna_user['is_guest'] || ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1')) || ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1'))): ?>
</div>
<div class="list-group">
		<a class="<?php if ($page == 'inbox') echo 'active'; ?> list-group-item" href="inbox.php">Inbox</a>
		<a class="<?php if ($page == 'notifications') echo 'active'; ?> list-group-item" href="notifications.php?id=<?php echo $id ?>">Notifications</a>
</div>
<div class="list-group">
		<a class="<?php if ($page == 'settings') echo 'active'; ?> list-group-item" href="settings.php?id=<?php echo $id ?>"><?php echo $lang['Settings'] ?></a>
	<?php endif; ?>
</div>
<?php

}