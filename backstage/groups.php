<?php

/**
 * Copyright (C) 2013 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 3 or higher
 */

// Tell header.php to use the admin template
define('FORUM_ADMIN_CONSOLE', 1);

define('FORUM_ROOT', '../');
require FORUM_ROOT.'include/common.php';
require FORUM_ROOT.'include/common_admin.php';

if (!$pun_user['is_admmod']) {
    header("Location: ../login.php");
}

// Load the backstage.php language file
require FORUM_ROOT.'lang/'.$admin_language.'/backstage.php';

// Add/edit a group (stage 1)
if (isset($_POST['add_group']) || isset($_GET['edit_group']))
{
	if (isset($_POST['add_group']))
	{
		$base_group = intval($_POST['base_group']);

		$result = $db->query('SELECT * FROM '.$db->prefix.'groups WHERE g_id='.$base_group) or error('Unable to fetch user group info', __FILE__, __LINE__, $db->error());
		$group = $db->fetch_assoc($result);

		$mode = 'add';
	}
	else // We are editing a group
	{
		$group_id = intval($_GET['edit_group']);
		if ($group_id < 1)
			message($lang_common['Bad request'], false, '404 Not Found');

		$result = $db->query('SELECT * FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to fetch user group info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang_common['Bad request'], false, '404 Not Found');

		$group = $db->fetch_assoc($result);

		$mode = 'edit';
	}


	$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['User groups']);
	$required_fields = array('req_title' => $lang_back['Group title label']);
	$focus_element = array('groups2', 'req_title');
	define('FORUM_ACTIVE_PAGE', 'admin');
	require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('groups');

?>
<h2><?php echo $lang_back['Group settings head'] ?></h2>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Group settings subhead'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="groups2" method="post" action="groups.php" onsubmit="return process_form(this)">
            <input type="hidden" name="mode" value="<?php echo $mode ?>" />
    <?php if ($mode == 'edit'): ?>					<input type="hidden" name="group_id" value="<?php echo $group_id ?>" />
    <?php endif; ?><?php if ($mode == 'add'): ?>					<input type="hidden" name="base_group" value="<?php echo $base_group ?>" />
    <?php endif; ?>					<fieldset>
                <p><?php echo $lang_back['Group settings info'] ?></p>
                <table class="table">
                    <tr>
                        <th><?php echo $lang_back['Group title label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="req_title" size="25" maxlength="50" value="<?php if ($mode == 'edit') echo pun_htmlspecialchars($group['g_title']); ?>" tabindex="1" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['User title label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="user_title" size="25" maxlength="50" value="<?php echo pun_htmlspecialchars($group['g_user_title']) ?>" tabindex="2" />
                            <br /><span><?php echo $lang_back['User title help'] ?></span>
                        </td>
                    </tr>
    <?php if ($group['g_id'] != FORUM_ADMIN): if ($group['g_id'] != FORUM_GUEST): if ($mode != 'edit' || $pun_config['o_default_user_group'] != $group['g_id']): ?>
                    <tr>
                        <th> <?php echo $lang_back['Mod privileges label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="moderator" value="1"<?php if ($group['g_moderator'] == '1') echo ' checked="checked"' ?> tabindex="5" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="moderator" value="0"<?php if ($group['g_moderator'] == '0') echo ' checked="checked"' ?> tabindex="6" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['Mod privileges help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Edit profile label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="mod_edit_users" value="1"<?php if ($group['g_mod_edit_users'] == '1') echo ' checked="checked"' ?> tabindex="7" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="mod_edit_users" value="0"<?php if ($group['g_mod_edit_users'] == '0') echo ' checked="checked"' ?> tabindex="8" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['Edit profile help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Rename users label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="mod_rename_users" value="1"<?php if ($group['g_mod_rename_users'] == '1') echo ' checked="checked"' ?> tabindex="9" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="mod_rename_users" value="0"<?php if ($group['g_mod_rename_users'] == '0') echo ' checked="checked"' ?> tabindex="10" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['Rename users help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Change passwords label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="mod_change_passwords" value="1"<?php if ($group['g_mod_change_passwords'] == '1') echo ' checked="checked"' ?> tabindex="11" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="mod_change_passwords" value="0"<?php if ($group['g_mod_change_passwords'] == '0') echo ' checked="checked"' ?> tabindex="12" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['Change passwords help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Ban users label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="mod_ban_users" value="1"<?php if ($group['g_mod_ban_users'] == '1') echo ' checked="checked"' ?> tabindex="13" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="mod_ban_users" value="0"<?php if ($group['g_mod_ban_users'] == '0') echo ' checked="checked"' ?> tabindex="14" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['Ban users help'] ?></span>
                        </td>
                    </tr>
    <?php endif; endif; ?>								<tr>
                        <th><?php echo $lang_back['Read board label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="read_board" value="1"<?php if ($group['g_read_board'] == '1') echo ' checked="checked"' ?> tabindex="15" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="read_board" value="0"<?php if ($group['g_read_board'] == '0') echo ' checked="checked"' ?> tabindex="16" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['Read board help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['View user info label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="view_users" value="1"<?php if ($group['g_view_users'] == '1') echo ' checked="checked"' ?> tabindex="17" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="view_users" value="0"<?php if ($group['g_view_users'] == '0') echo ' checked="checked"' ?> tabindex="18" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['View user info help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Post replies label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="post_replies" value="1"<?php if ($group['g_post_replies'] == '1') echo ' checked="checked"' ?> tabindex="19" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="post_replies" value="0"<?php if ($group['g_post_replies'] == '0') echo ' checked="checked"' ?> tabindex="20" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['Post replies help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Post topics label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="post_topics" value="1"<?php if ($group['g_post_topics'] == '1') echo ' checked="checked"' ?> tabindex="21" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="post_topics" value="0"<?php if ($group['g_post_topics'] == '0') echo ' checked="checked"' ?> tabindex="22" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['Post topics help'] ?></span>
                        </td>
                    </tr>
    <?php if ($group['g_id'] != FORUM_GUEST): ?>								<tr>
                        <th><?php echo $lang_back['Edit posts label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="edit_posts" value="1"<?php if ($group['g_edit_posts'] == '1') echo ' checked="checked"' ?> tabindex="23" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="edit_posts" value="0"<?php if ($group['g_edit_posts'] == '0') echo ' checked="checked"' ?> tabindex="24" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['Edit posts help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Delete posts label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="delete_posts" value="1"<?php if ($group['g_delete_posts'] == '1') echo ' checked="checked"' ?> tabindex="25" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="delete_posts" value="0"<?php if ($group['g_delete_posts'] == '0') echo ' checked="checked"' ?> tabindex="26" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['Delete posts help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Delete topics label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="delete_topics" value="1"<?php if ($group['g_delete_topics'] == '1') echo ' checked="checked"' ?> tabindex="27" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="delete_topics" value="0"<?php if ($group['g_delete_topics'] == '0') echo ' checked="checked"' ?> tabindex="28" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['Delete topics help'] ?></span>
                        </td>
                    </tr>
    <?php endif;
    if ($group['g_id'] != FORUM_GUEST): ?>								<tr>
                        <th><?php echo $lang_back['Set own title label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="set_title" value="1"<?php if ($group['g_set_title'] == '1') echo ' checked="checked"' ?> tabindex="31" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="set_title" value="0"<?php if ($group['g_set_title'] == '0') echo ' checked="checked"' ?> tabindex="32" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['Set own title help'] ?></span>
                        </td>
                    </tr>
    <?php endif; ?>								<tr>
                        <th><?php echo $lang_back['User search label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="search" value="1"<?php if ($group['g_search'] == '1') echo ' checked="checked"' ?> tabindex="33" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="search" value="0"<?php if ($group['g_search'] == '0') echo ' checked="checked"' ?> tabindex="34" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['User search help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['User list search label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="search_users" value="1"<?php if ($group['g_search_users'] == '1') echo ' checked="checked"' ?> tabindex="35" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="search_users" value="0"<?php if ($group['g_search_users'] == '0') echo ' checked="checked"' ?> tabindex="36" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['User list search help'] ?></span>
                        </td>
                    </tr>
    <?php if ($group['g_id'] != FORUM_GUEST): ?>								<tr>
                        <th><?php echo $lang_back['Send e-mails label'] ?></th>
                        <td>
                            <label class="conl"><input type="radio" name="send_email" value="1"<?php if ($group['g_send_email'] == '1') echo ' checked="checked"' ?> tabindex="37" />&#160;<strong><?php echo $lang_back['Yes'] ?></strong></label>
                            <label class="conl"><input type="radio" name="send_email" value="0"<?php if ($group['g_send_email'] == '0') echo ' checked="checked"' ?> tabindex="38" />&#160;<strong><?php echo $lang_back['No'] ?></strong></label>
                            <br /><span class="clearb"><?php echo $lang_back['Send e-mails help'] ?></span>
                        </td>
                    </tr>
    <?php endif; ?>								<tr>
                        <th><?php echo $lang_back['Post flood label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="post_flood" size="5" maxlength="4" value="<?php echo $group['g_post_flood'] ?>" tabindex="35" />
                            <br /><span><?php echo $lang_back['Post flood help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Search flood label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="search_flood" size="5" maxlength="4" value="<?php echo $group['g_search_flood'] ?>" tabindex="36" />
                            <br /><span><?php echo $lang_back['Search flood help'] ?></span>
                        </td>
                    </tr>
    <?php if ($group['g_id'] != FORUM_GUEST): ?>								<tr>
                        <th><?php echo $lang_back['E-mail flood label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="email_flood" size="5" maxlength="4" value="<?php echo $group['g_email_flood'] ?>" tabindex="37" />
                            <br /><span><?php echo $lang_back['E-mail flood help'] ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo $lang_back['Report flood label'] ?></th>
                        <td>
                            <input type="text" class="form-control" name="report_flood" size="5" maxlength="4" value="<?php echo $group['g_report_flood'] ?>" tabindex="38" />
                            <br /><span><?php echo $lang_back['Report flood help'] ?></span>
                        </td>
                    </tr>
    <?php endif; endif; ?>
                </table>
    <?php if ($group['g_moderator'] == '1' ): ?>							<p class="warntext"><?php echo $lang_back['Moderator info'] ?></p>
    <?php endif; ?>	
            </fieldset>
            <div class="control-group">
                <input class="btn btn-primary" type="submit" name="add_edit_group" value="<?php echo $lang_back['Save'] ?>" tabindex="39" />
            </div>
        </form>
    </div>
</div>
<?php

	require FORUM_ROOT.'backstage/footer.php';
}


// Add/edit a group (stage 2)
else if (isset($_POST['add_edit_group']))
{
	// Is this the admin group? (special rules apply)
	$is_admin_group = (isset($_POST['group_id']) && $_POST['group_id'] == FORUM_ADMIN) ? true : false;

	$title = pun_trim($_POST['req_title']);
	$user_title = pun_trim($_POST['user_title']);
	$moderator = isset($_POST['moderator']) && $_POST['moderator'] == '1' ? '1' : '0';
	$mod_edit_users = $moderator == '1' && isset($_POST['mod_edit_users']) && $_POST['mod_edit_users'] == '1' ? '1' : '0';
	$mod_rename_users = $moderator == '1' && isset($_POST['mod_rename_users']) && $_POST['mod_rename_users'] == '1' ? '1' : '0';
	$mod_change_passwords = $moderator == '1' && isset($_POST['mod_change_passwords']) && $_POST['mod_change_passwords'] == '1' ? '1' : '0';
	$mod_ban_users = $moderator == '1' && isset($_POST['mod_ban_users']) && $_POST['mod_ban_users'] == '1' ? '1' : '0';
	$read_board = isset($_POST['read_board']) ? intval($_POST['read_board']) : '1';
	$view_users = (isset($_POST['view_users']) && $_POST['view_users'] == '1') || $is_admin_group ? '1' : '0';
	$post_replies = isset($_POST['post_replies']) ? intval($_POST['post_replies']) : '1';
	$post_topics = isset($_POST['post_topics']) ? intval($_POST['post_topics']) : '1';
	$edit_posts = isset($_POST['edit_posts']) ? intval($_POST['edit_posts']) : ($is_admin_group) ? '1' : '0';
	$delete_posts = isset($_POST['delete_posts']) ? intval($_POST['delete_posts']) : ($is_admin_group) ? '1' : '0';
	$delete_topics = isset($_POST['delete_topics']) ? intval($_POST['delete_topics']) : ($is_admin_group) ? '1' : '0';
	$set_title = isset($_POST['set_title']) ? intval($_POST['set_title']) : ($is_admin_group) ? '1' : '0';
	$search = isset($_POST['search']) ? intval($_POST['search']) : '1';
	$search_users = isset($_POST['search_users']) ? intval($_POST['search_users']) : '1';
	$send_email = (isset($_POST['send_email']) && $_POST['send_email'] == '1') || $is_admin_group ? '1' : '0';
	$post_flood = (isset($_POST['post_flood']) && $_POST['post_flood'] >= 0) ? intval($_POST['post_flood']) : '0';
	$search_flood = (isset($_POST['search_flood']) && $_POST['search_flood'] >= 0) ? intval($_POST['search_flood']) : '0';
	$email_flood = (isset($_POST['email_flood']) && $_POST['email_flood'] >= 0) ? intval($_POST['email_flood']) : '0';
	$report_flood = (isset($_POST['report_flood']) && $_POST['report_flood'] >= 0) ? intval($_POST['report_flood']) : '0';

	if ($title == '')
		message($lang_back['Must enter title message']);

	$user_title = ($user_title != '') ? '\''.$db->escape($user_title).'\'' : 'NULL';

	if ($_POST['mode'] == 'add')
	{
		$result = $db->query('SELECT 1 FROM '.$db->prefix.'groups WHERE g_title=\''.$db->escape($title).'\'') or error('Unable to check group title collision', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result))
			message(sprintf($lang_back['Title already exists message'], pun_htmlspecialchars($title)));

		$db->query('INSERT INTO '.$db->prefix.'groups (g_title, g_user_title, g_moderator, g_mod_edit_users, g_mod_rename_users, g_mod_change_passwords, g_mod_ban_users, g_read_board, g_view_users, g_post_replies, g_post_topics, g_edit_posts, g_delete_posts, g_delete_topics, g_set_title, g_search, g_search_users, g_send_email, g_post_flood, g_search_flood, g_email_flood, g_report_flood) VALUES(\''.$db->escape($title).'\', '.$user_title.', '.$moderator.', '.$mod_edit_users.', '.$mod_rename_users.', '.$mod_change_passwords.', '.$mod_ban_users.', '.$read_board.', '.$view_users.', '.$post_replies.', '.$post_topics.', '.$edit_posts.', '.$delete_posts.', '.$delete_topics.', '.$set_title.', '.$search.', '.$search_users.', '.$send_email.', '.$post_flood.', '.$search_flood.', '.$email_flood.', '.$report_flood.')') or error('Unable to add group', __FILE__, __LINE__, $db->error());
		$new_group_id = $db->insert_id();

		// Now lets copy the forum specific permissions from the group which this group is based on
		$result = $db->query('SELECT forum_id, read_forum, post_replies, post_topics FROM '.$db->prefix.'forum_perms WHERE group_id='.intval($_POST['base_group'])) or error('Unable to fetch group forum permission list', __FILE__, __LINE__, $db->error());
		while ($cur_forum_perm = $db->fetch_assoc($result))
			$db->query('INSERT INTO '.$db->prefix.'forum_perms (group_id, forum_id, read_forum, post_replies, post_topics) VALUES('.$new_group_id.', '.$cur_forum_perm['forum_id'].', '.$cur_forum_perm['read_forum'].', '.$cur_forum_perm['post_replies'].', '.$cur_forum_perm['post_topics'].')') or error('Unable to insert group forum permissions', __FILE__, __LINE__, $db->error());
	}
	else
	{
		$result = $db->query('SELECT 1 FROM '.$db->prefix.'groups WHERE g_title=\''.$db->escape($title).'\' AND g_id!='.intval($_POST['group_id'])) or error('Unable to check group title collision', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result))
			message(sprintf($lang_back['Title already exists message'], pun_htmlspecialchars($title)));

		$db->query('UPDATE '.$db->prefix.'groups SET g_title=\''.$db->escape($title).'\', g_user_title='.$user_title.', g_moderator='.$moderator.', g_mod_edit_users='.$mod_edit_users.', g_mod_rename_users='.$mod_rename_users.', g_mod_change_passwords='.$mod_change_passwords.', g_mod_ban_users='.$mod_ban_users.', g_read_board='.$read_board.', g_view_users='.$view_users.', g_post_replies='.$post_replies.', g_post_topics='.$post_topics.', g_edit_posts='.$edit_posts.', g_delete_posts='.$delete_posts.', g_delete_topics='.$delete_topics.', g_set_title='.$set_title.', g_search='.$search.', g_search_users='.$search_users.', g_send_email='.$send_email.', g_post_flood='.$post_flood.', g_search_flood='.$search_flood.', g_email_flood='.$email_flood.', g_report_flood='.$report_flood.' WHERE g_id='.intval($_POST['group_id'])) or error('Unable to update group', __FILE__, __LINE__, $db->error());
	}

	// Regenerate the quick jump cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	$group_id = $_POST['mode'] == 'add' ? $new_group_id : intval($_POST['group_id']);
	generate_quickjump_cache($group_id);

	if ($_POST['mode'] == 'edit')
		redirect('backstage/groups.php', $lang_back['Group edited redirect']);
	else
		redirect('backstage/groups.php', $lang_back['Group added redirect']);
}


// Set default group
else if (isset($_POST['set_default_group']))
{
	$group_id = intval($_POST['default_group']);

	// Make sure it's not the admin or guest groups
	if ($group_id == FORUM_ADMIN || $group_id == FORUM_GUEST)
		message($lang_common['Bad request'], false, '404 Not Found');

	// Make sure it's not a moderator group
	$result = $db->query('SELECT 1 FROM '.$db->prefix.'groups WHERE g_id='.$group_id.' AND g_moderator=0') or error('Unable to check group moderator status', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message($lang_common['Bad request'], false, '404 Not Found');

	$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$group_id.' WHERE conf_name=\'o_default_user_group\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());

	// Regenerate the config cache
	if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
		require FORUM_ROOT.'include/cache.php';

	generate_config_cache();

	redirect('backstage/groups.php', $lang_back['Default group redirect']);
}


// Remove a group
else if (isset($_GET['del_group']))
{
	$group_id = isset($_POST['group_to_delete']) ? intval($_POST['group_to_delete']) : intval($_GET['del_group']);
	if ($group_id < 5)
		message($lang_common['Bad request'], false, '404 Not Found');

	// Make sure we don't remove the default group
	if ($group_id == $pun_config['o_default_user_group'])
		message($lang_back['Cannot remove default message']);

	// Check if this group has any members
	$result = $db->query('SELECT g.g_title, COUNT(u.id) FROM '.$db->prefix.'groups AS g INNER JOIN '.$db->prefix.'users AS u ON g.g_id=u.group_id WHERE g.g_id='.$group_id.' GROUP BY g.g_id, g_title') or error('Unable to fetch group info', __FILE__, __LINE__, $db->error());

	// If the group doesn't have any members or if we've already selected a group to move the members to
	if (!$db->num_rows($result) || isset($_POST['del_group']))
	{
		if (isset($_POST['del_group_comply']) || isset($_POST['del_group']))
		{
			if (isset($_POST['del_group']))
			{
				$move_to_group = intval($_POST['move_to_group']);
				$db->query('UPDATE '.$db->prefix.'users SET group_id='.$move_to_group.' WHERE group_id='.$group_id) or error('Unable to move users into group', __FILE__, __LINE__, $db->error());
			}

			// Delete the group and any forum specific permissions
			$db->query('DELETE FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to delete group', __FILE__, __LINE__, $db->error());
			$db->query('DELETE FROM '.$db->prefix.'forum_perms WHERE group_id='.$group_id) or error('Unable to delete group forum permissions', __FILE__, __LINE__, $db->error());

			redirect('backstage/groups.php', $lang_back['Group removed redirect']);
		}
		else
		{
			$result = $db->query('SELECT g_title FROM '.$db->prefix.'groups WHERE g_id='.$group_id) or error('Unable to fetch group title', __FILE__, __LINE__, $db->error());
			$group_title = $db->result($result);

			$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['User groups']);
			define('FORUM_ACTIVE_PAGE', 'admin');
			require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('groups');

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Group delete head'] ?></h3>
    </div>
    <div class="panel-body">
        <form method="post" action="groups.php?del_group=<?php echo $group_id ?>">
            <input type="hidden" name="group_to_delete" value="<?php echo $group_id ?>" />
                <fieldset>
                    <p><?php printf($lang_back['Confirm delete info'], pun_htmlspecialchars($group_title)) ?></p>
                    <div class="alert alert-danger"><?php echo $lang_back['Confirm delete warn'] ?></div>
                </fieldset>
            <div class="control-group">
                <input class="btn btn-danger" type="submit" name="del_group_comply" value="<?php echo $lang_back['Delete'] ?>" tabindex="1" />
                <a class="btn" href="javascript:history.go(-1)" tabindex="2"><?php echo $lang_back['Go back'] ?></a>
            </div>
        </form>
    </div>
</div>
<?php

			require FORUM_ROOT.'backstage/footer.php';
		}
	}

	list($group_title, $group_members) = $db->fetch_row($result);

	$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['User groups']);
	define('FORUM_ACTIVE_PAGE', 'admin');
	require FORUM_ROOT.'backstage/header.php';

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Delete group head'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="groups" method="post" action="groups.php?del_group=<?php echo $group_id ?>">
            <fieldset>
                <p><?php printf($lang_back['Move users info'], pun_htmlspecialchars($group_title), forum_number_format($group_members)) ?></p>
                <label><?php echo $lang_back['Move users label'] ?>
                    <select class="form-control" name="move_to_group">
<?php

	$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id!='.FORUM_GUEST.' AND g_id!='.$group_id.' ORDER BY g_title') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

	while ($cur_group = $db->fetch_assoc($result))
	{
		if ($cur_group['g_id'] == FORUM_MEMBER) // Pre-select the pre-defined Members group
			echo "\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.pun_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
		else
			echo "\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.pun_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
	}

?>
                    </select>
                </label>
            </fieldset>
            <p class="control-group">
                <input class="btn btn-danger" type="submit" name="del_group" value="<?php echo $lang_back['Delete group'] ?>" />
                <a class="btn" href="javascript:history.go(-1)"><?php echo $lang_back['Go back'] ?></a>
            </p>
        </form>
    </div>
</div>
<?php

	require FORUM_ROOT.'backstage/footer.php';
}


$page_title = array(pun_htmlspecialchars($pun_config['o_board_title']), $lang_back['Admin'], $lang_back['User groups']);
define('FORUM_ACTIVE_PAGE', 'admin');
require FORUM_ROOT.'backstage/header.php';
	generate_admin_menu('groups');

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Add group subhead'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="groups" method="post" action="groups.php">
            <fieldset>
                <table class="table">
                    <tr>
                        <th width="18%"><?php echo $lang_back['New group label'] ?></th>
                        <td>
                            <select class="form-control" id="base_group" name="base_group" tabindex="1">
<?php

$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id!='.FORUM_ADMIN.' AND g_id!='.FORUM_GUEST.' ORDER BY g_title') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

while ($cur_group = $db->fetch_assoc($result))
{
if ($cur_group['g_id'] == $pun_config['o_default_user_group'])
echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.pun_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
else
echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.pun_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
}

?>
                            </select>
                            <input class="btn btn-primary" type="submit" name="add_group" value="<?php echo $lang_back['Add'] ?>" tabindex="2" />
                            <span class="help-block"><?php echo $lang_back['New group help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Default group subhead'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="groups" method="post" action="groups.php">
        <fieldset>
            <table class="table">
                <tr>
                    <th width="18%"><?php echo $lang_back['Default group label'] ?></th>
                    <td>
                        <select class="form-control" id="default_group" name="default_group" tabindex="3">
<?php

$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id>'.FORUM_GUEST.' AND g_moderator=0 ORDER BY g_title') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

while ($cur_group = $db->fetch_assoc($result))
{
if ($cur_group['g_id'] == $pun_config['o_default_user_group'])
echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.pun_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
else
echo "\t\t\t\t\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.pun_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
}

?>
                            </select>
                            <input class="btn btn-primary" type="submit" name="set_default_group" value="<?php echo $lang_back['Save'] ?>" tabindex="4" />
                            <span class="help-block"><?php echo $lang_back['Default group help'] ?></span>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang_back['Existing groups head'] ?></h3>
    </div>
    <div class="panel-body">
        <fieldset>
            <p><?php echo $lang_back['Edit groups info'] ?></p>
            <table class="table">
<?php

$cur_index = 5;

$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups ORDER BY g_id') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

while ($cur_group = $db->fetch_assoc($result))
	echo "\t\t\t\t\t\t\t\t".'<tr><th><a class="btn btn-primary btn-mini" href="groups.php?edit_group='.$cur_group['g_id'].'" tabindex="'.$cur_index++.'">'.$lang_back['Edit link'].'</a>'.(($cur_group['g_id'] > FORUM_MEMBER) ? ' <a class="btn btn-danger btn-mini" href="groups.php?del_group='.$cur_group['g_id'].'" tabindex="'.$cur_index++.'">'.$lang_back['Delete link'].'</a>' : '').'</th><td>'.pun_htmlspecialchars($cur_group['g_title']).'</td></tr>'."\n";

?>
            </table>
        </fieldset>
    </div>
</div>
<?php

require FORUM_ROOT.'backstage/footer.php';
