<?php

/*
 * Copyright (C) 2013-2014 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

// Display the me navigation
function load_me_nav($page = '') {
	global $lang, $luna_config, $luna_user, $id;

?>
<div class="list-group">
    <a class="<?php if ($page == 'view') echo 'active'; ?> list-group-item" href="profile.php?section=view&amp;id=<?php echo $id ?>"><?php echo $lang['Profile'] ?></a>
    <?php if ($luna_user['id'] == $id && !$luna_user['is_guest'] || ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1'))): ?>
        <a class="<?php if ($page == 'personality') echo 'active'; ?> list-group-item" href="profile.php?section=personality&amp;id=<?php echo $id ?>"><?php echo $lang['Section personality'] ?></a>
        <a class="<?php if ($page == 'settings') echo 'active'; ?> list-group-item" href="profile.php?section=settings&amp;id=<?php echo $id ?>"><?php echo $lang['Settings'] ?></a>
        <?php if ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && $luna_user['g_mod_ban_users'] == '1')): ?>
            <a class="<?php if ($page == 'admin') echo 'active'; ?> list-group-item" href="profile.php?section=admin&amp;id=<?php echo $id ?>"><?php echo $lang['Section admin'] ?></a>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php

}