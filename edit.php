<?php

/**
 * Copyright (C) 2013-2014 ModernBB Group
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License under GPLv3
 */

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';


if ($luna_user['g_read_board'] == '0')
	message($lang['No view'], false, '403 Forbidden');


$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id < 1)
	message($lang['Bad request'], false, '404 Not Found');

// Fetch some info about the post, the topic and the forum
$result = $db->query('SELECT f.id AS fid, f.forum_name, f.moderators, f.redirect_url, fp.post_replies, fp.post_topics, t.id AS tid, t.subject, t.posted, t.first_post_id, t.sticky, t.closed, p.poster, p.poster_id, p.message, p.hide_smilies FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND p.id='.$id) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
if (!$db->num_rows($result))
	message($lang['Bad request'], false, '404 Not Found');

$cur_post = $db->fetch_assoc($result);

// Sort out who the moderators are and if we are currently a moderator (or an admin)
$mods_array = ($cur_post['moderators'] != '') ? unserialize($cur_post['moderators']) : array();
$is_admmod = ($luna_user['g_id'] == FORUM_ADMIN || ($luna_user['g_moderator'] == '1' && array_key_exists($luna_user['username'], $mods_array))) ? true : false;

$can_edit_subject = $id == $cur_post['first_post_id'];

if ($luna_config['o_censoring'] == '1')
{
	$cur_post['subject'] = censor_words($cur_post['subject']);
	$cur_post['message'] = censor_words($cur_post['message']);
}

// Do we have permission to edit this post?
if (($luna_user['g_edit_posts'] == '0' ||
	$cur_post['poster_id'] != $luna_user['id'] ||
	$cur_post['closed'] == '1') &&
	!$is_admmod)
	message($lang['No permission'], false, '403 Forbidden');
	
if ($is_admmod && $luna_user['g_id'] != FORUM_ADMIN && in_array($cur_post['poster_id'], get_admin_ids()))
	message($lang['No permission'], false, '403 Forbidden');

// Start with a clean slate
$errors = array();


if (isset($_POST['form_sent']))
{
	// Make sure they got here from the site
	confirm_referrer('edit.php');

	// If it's a topic it must contain a subject
	if ($can_edit_subject)
	{
		$subject = luna_trim($_POST['req_subject']);

		if ($luna_config['o_censoring'] == '1')
			$censored_subject = luna_trim(censor_words($subject));

		if ($subject == '')
			$errors[] = $lang['No subject'];
		else if ($luna_config['o_censoring'] == '1' && $censored_subject == '')
			$errors[] = $lang['No subject after censoring'];
		else if (luna_strlen($subject) > 70)
			$errors[] = $lang['Too long subject'];
		else if ($luna_config['p_subject_all_caps'] == '0' && is_all_uppercase($subject) && !$luna_user['is_admmod'])
			$errors[] = $lang['All caps subject'];
	}

	// Clean up message from POST
	$message = luna_linebreaks(luna_trim($_POST['req_message']));

	// Here we use strlen() not luna_strlen() as we want to limit the post to FORUM_MAX_POSTSIZE bytes, not characters
	if (strlen($message) > FORUM_MAX_POSTSIZE)
		$errors[] = sprintf($lang['Too long message'], forum_number_format(FORUM_MAX_POSTSIZE));
	else if ($luna_config['p_message_all_caps'] == '0' && is_all_uppercase($message) && !$luna_user['is_admmod'])
		$errors[] = $lang['All caps message'];

	// Validate BBCode syntax
	if ($luna_config['p_message_bbcode'] == '1')
	{
		require FORUM_ROOT.'include/parser.php';
		$message = preparse_bbcode($message, $errors);
	}

	if (empty($errors))
	{
		if ($message == '')
			$errors[] = $lang['No message'];
		else if ($luna_config['o_censoring'] == '1')
		{
			// Censor message to see if that causes problems
			$censored_message = luna_trim(censor_words($message));

			if ($censored_message == '')
				$errors[] = $lang['No message after censoring'];
		}
	}

	$hide_smilies = isset($_POST['hide_smilies']) ? '1' : '0';
	$stick_topic = isset($_POST['stick_topic']) ? '1' : '0';
	if (!$is_admmod)
		$stick_topic = $cur_post['sticky'];
	
	// Replace four-byte characters (MySQL cannot handle them)
	$message = strip_bad_multibyte_chars($message);

	// Did everything go according to plan?
	if (empty($errors) && !isset($_POST['preview']))
	{
		$edited_sql = (!isset($_POST['silent']) || !$is_admmod) ? ', edited='.time().', edited_by=\''.$db->escape($luna_user['username']).'\'' : '';

		require FORUM_ROOT.'include/search_idx.php';

		if ($can_edit_subject)
		{
			// Update the topic and any redirect topics
			$db->query('UPDATE '.$db->prefix.'topics SET subject=\''.$db->escape($subject).'\', sticky='.$stick_topic.' WHERE id='.$cur_post['tid'].' OR moved_to='.$cur_post['tid']) or error('Unable to update topic', __FILE__, __LINE__, $db->error());

			// We changed the subject, so we need to take that into account when we update the search words
			update_search_index('edit', $id, $message, $subject);
		}
		else
			update_search_index('edit', $id, $message);

		// Update the post
		$db->query('UPDATE '.$db->prefix.'posts SET message=\''.$db->escape($message).'\', hide_smilies='.$hide_smilies.$edited_sql.' WHERE id='.$id) or error('Unable to update post', __FILE__, __LINE__, $db->error());

		redirect('viewtopic.php?pid='.$id.'#p'.$id, $lang['Edit redirect']);
	}
}



$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Edit post']);
$required_fields = array('req_subject' => $lang['Subject'], 'req_message' => $lang['Message']);
$focus_element = array('edit', 'req_message');
define('FORUM_ACTIVE_PAGE', 'index');
require FORUM_ROOT.'header.php';

$cur_index = 1;

?>
<div class="btn-group btn-breadcrumb">
    <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-home"></span></a>
    <a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><?php echo luna_htmlspecialchars($cur_post['forum_name']) ?></a>
    <a class="btn btn-primary" href="viewtopic.php?id=<?php echo $cur_post['tid'] ?>"><?php echo luna_htmlspecialchars($cur_post['subject']) ?></a>
    <a class="btn btn-primary" href="#"><?php echo $lang['Edit post'] ?></a>
</div>

<?php

// If there are errors, we display them
if (!empty($errors))
{

?>
<div class="panel panel-danger">
	<div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Post errors'] ?></h3>
    </div>
	<div class="panel-body">
		<p>
<?php

	foreach ($errors as $cur_error)
		echo "\t\t\t\t".$cur_error."\n";
?>
		</p>
	</div>
</div>

<?php

}
else if (isset($_POST['preview']))
{
	require_once FORUM_ROOT.'include/parser.php';
	$preview_message = parse_message($message, $hide_smilies);

?>
<div class="panel panel-default">
	<div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Post preview'] ?></h3>
    </div>
	<div class="panel-body">
		<?php echo $preview_message ?>
	</div>
</div>

<?php

}

?>
<form id="edit" method="post" action="edit.php?id=<?php echo $id ?>&amp;action=edit" onsubmit="return process_form(this)">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Edit post'] ?></h3>
        </div>
        <fieldset class="postfield">
            <input type="hidden" name="form_sent" value="1" />
<?php if ($can_edit_subject): ?>
            <input class="longinput form-control" type="text" name="req_subject" maxlength="70" tabindex="<?php echo $cur_index++ ?>" value="<?php echo luna_htmlspecialchars(isset($_POST['req_subject']) ? $_POST['req_subject'] : $cur_post['subject']) ?>" />
<?php endif; ?>
            <textarea class="form-control tinymce" name="req_message" rows="20" tabindex="<?php echo $cur_index++ ?>"><?php echo luna_htmlspecialchars(isset($_POST['req_message']) ? $message : $cur_post['message']) ?></textarea>
        </fieldset>
        <div class="panel-footer">
        	<div class="btn-group"><input type="submit" onclick="tinyMCE.triggerSave(false);" class="btn btn-primary" name="submit" value="<?php echo $lang['Submit'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="s" /> <input type="submit" onclick="tinyMCE.triggerSave(false);" class="btn btn-default" name="preview" value="<?php echo $lang['Preview'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="p" /></div> <a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a>
            <ul class="bblinks">
                <li><a class="label <?php echo ($luna_config['p_message_bbcode'] == '1') ? "label-success" : "label-danger"; ?>" href="help.php#bbcode" onclick="window.open(this.href); return false;"><?php echo $lang['BBCode'] ?></a></li>
                <li><a class="label <?php echo ($luna_config['p_message_bbcode'] == '1' && $luna_config['p_message_img_tag'] == '1') ? "label-success" : "label-danger"; ?>" href="help.php#img" onclick="window.open(this.href); return false;"><?php echo $lang['img tag'] ?></a></li>
                <li><a class="label <?php echo ($luna_config['o_smilies'] == '1') ? "label-success" : "label-danger"; ?>" href="help.php#smilies" onclick="window.open(this.href); return false;"><?php echo $lang['Smilies'] ?></a></li>
            </ul>
        </div>
    </div>
<?php

$checkboxes = array();
if ($can_edit_subject && $is_admmod)
{
	if (isset($_POST['stick_topic']) || $cur_post['sticky'] == '1')
		$checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="stick_topic" value="1" checked="checked" tabindex="'.($cur_index++).'" /> '.$lang['Stick topic'].'</label></div>';
	else
		$checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="stick_topic" value="1" tabindex="'.($cur_index++).'" /> '.$lang['Stick topic'].'</label></div>';
}

if ($luna_config['o_smilies'] == '1')
{
	if (isset($_POST['hide_smilies']) || $cur_post['hide_smilies'] == '1')
		$checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="hide_smilies" value="1" checked="checked" tabindex="'.($cur_index++).'" /> '.$lang['Hide smilies'].'</label></div>';
	else
		$checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="hide_smilies" value="1" tabindex="'.($cur_index++).'" /> '.$lang['Hide smilies'].'</label></div>';
}

if ($is_admmod)
{
	if ((isset($_POST['form_sent']) && isset($_POST['silent'])) || !isset($_POST['form_sent']))
		$checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="silent" value="1" tabindex="'.($cur_index++).'" checked="checked" /> '.$lang['Silent edit'].'</label></div>';
	else
		$checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="silent" value="1" tabindex="'.($cur_index++).'" /> '.$lang['Silent edit'].'</label></div>';
}

if (!empty($checkboxes))
{

?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Options'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
				<?php echo implode("\n\t\t\t\t\t\t\t", $checkboxes)."\n" ?>
            </fieldset>
        </div>
    </div>
<?php

	}

?>
</form>
<?php

require FORUM_ROOT.'footer.php';
