<?php

/**
 * Copyright (C) 2013-2014 ModernBB
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3
 */

if (isset($_GET['action']))
	define('FORUM_QUIET_VISIT', 1);

define('FORUM_ROOT', dirname(__FILE__).'/');
require FORUM_ROOT.'include/common.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;


if ($action == 'rules')
{
	if ($luna_config['o_rules'] == '0' || ($luna_user['is_guest'] && $luna_user['g_read_board'] == '0' && $luna_config['o_regs_allow'] == '0'))
		message($lang['Bad request'], false, '404 Not Found');

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Forum rules']);
	define('FORUM_ACTIVE_PAGE', 'rules');
	require FORUM_ROOT.'header.php';

?>
<div class="panel panel-default">
    <div id="rules-block" class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Forum rules'] ?></h3>
    </div>
    <div class="panel-body">
        <div class="usercontent"><?php echo $luna_config['o_rules_message'] ?></div>
    </div>
</div>
<?php

	require FORUM_ROOT.'footer.php';
}


else if ($action == 'markread')
{
	if ($luna_user['is_guest'])
		message($lang['No permission'], false, '403 Forbidden');

	$db->query('UPDATE '.$db->prefix.'users SET last_visit='.$luna_user['logged'].' WHERE id='.$luna_user['id']) or error('Unable to update user last visit data', __FILE__, __LINE__, $db->error());

	// Reset tracked topics
	set_tracked_topics(null);

	redirect('index.php');
}


// Mark the topics/posts in a forum as read?
else if ($action == 'markforumread')
{
	if ($luna_user['is_guest'])
		message($lang['No permission'], false, '403 Forbidden');

	$fid = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
	if ($fid < 1)
		message($lang['Bad request'], false, '404 Not Found');

	$tracked_topics = get_tracked_topics();
	$tracked_topics['forums'][$fid] = time();
	set_tracked_topics($tracked_topics);

	redirect('viewforum.php?id='.$fid);
}


else if (isset($_GET['email']))
{
	if ($luna_user['is_guest'] || $luna_user['g_send_email'] == '0')
		message($lang['No permission'], false, '403 Forbidden');

	$recipient_id = intval($_GET['email']);
	if ($recipient_id < 2)
		message($lang['Bad request'], false, '404 Not Found');

	$result = $db->query('SELECT username, email, email_setting FROM '.$db->prefix.'users WHERE id='.$recipient_id) or error('Unable to fetch user info', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message($lang['Bad request'], false, '404 Not Found');

	list($recipient, $recipient_email, $email_setting) = $db->fetch_row($result);

	if ($email_setting == 2 && !$luna_user['is_admmod'])
		message($lang['Form email disabled']);


	if (isset($_POST['form_sent']))
	{
		confirm_referrer('misc.php');

		// Clean up message and subject from POST
		$subject = luna_trim($_POST['req_subject']);
		$message = luna_trim($_POST['req_message']);

		if ($subject == '')
			message($lang['No email subject']);
		else if ($message == '')
			message($lang['No email message']);
		// Here we use strlen() not luna_strlen() as we want to limit the post to FORUM_MAX_POSTSIZE bytes, not characters
		else if (strlen($message) > FORUM_MAX_POSTSIZE)
			message($lang['Too long email message']);

		if ($luna_user['last_email_sent'] != '' && (time() - $luna_user['last_email_sent']) < $luna_user['g_email_flood'] && (time() - $luna_user['last_email_sent']) >= 0)
			message(sprintf($lang['Email flood'], $luna_user['g_email_flood'], $luna_user['g_email_flood'] - (time() - $luna_user['last_email_sent'])));

		// Load the "form email" template
		$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/'.$luna_user['language'].'/mail_templates/form_email.tpl'));

		// The first row contains the subject
		$first_crlf = strpos($mail_tpl, "\n");
		$mail_subject = luna_trim(substr($mail_tpl, 8, $first_crlf-8));
		$mail_message = luna_trim(substr($mail_tpl, $first_crlf));

		$mail_subject = str_replace('<mail_subject>', $subject, $mail_subject);
		$mail_message = str_replace('<sender>', $luna_user['username'], $mail_message);
		$mail_message = str_replace('<board_title>', $luna_config['o_board_title'], $mail_message);
		$mail_message = str_replace('<mail_message>', $message, $mail_message);
		$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

		require_once FORUM_ROOT.'include/email.php';

		luna_mail($recipient_email, $mail_subject, $mail_message, $luna_user['email'], $luna_user['username']);

		$db->query('UPDATE '.$db->prefix.'users SET last_email_sent='.time().' WHERE id='.$luna_user['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());

		redirect(luna_htmlspecialchars($_POST['redirect_url']));
	}


	// Try to determine if the data in HTTP_REFERER is valid (if not, we redirect to the user's profile after the email is sent)
	if (!empty($_SERVER['HTTP_REFERER']))
	{
		$referrer = parse_url($_SERVER['HTTP_REFERER']);
		// Remove www subdomain if it exists
		if (strpos($referrer['host'], 'www.') === 0)
			$referrer['host'] = substr($referrer['host'], 4);

		// Make sure the path component exists
		if (!isset($referrer['path']))
			$referrer['path'] = '';

		$valid = parse_url(get_base_url());
		// Remove www subdomain if it exists
		if (strpos($valid['host'], 'www.') === 0)
			$valid['host'] = substr($valid['host'], 4);

		// Make sure the path component exists
		if (!isset($valid['path']))
			$valid['path'] = '';

		if ($referrer['host'] == $valid['host'] && preg_match('%^'.preg_quote($valid['path'], '%').'/(.*?)\.php%i', $referrer['path']))
			$redirect_url = $_SERVER['HTTP_REFERER'];
	}

	if (!isset($redirect_url))
		$redirect_url = 'profile.php?id='.$recipient_id;
	else if (preg_match('%viewtopic\.php\?pid=(\d+)$%', $redirect_url, $matches))
		$redirect_url .= '#p'.$matches[1];

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Send email to'].' '.luna_htmlspecialchars($recipient));
	$required_fields = array('req_subject' => $lang['Subject'], 'req_message' => $lang['Message']);
	$focus_element = array('email', 'req_subject');
	define('FORUM_ACTIVE_PAGE', 'index');
	require FORUM_ROOT.'header.php';

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Send email to'] ?> <?php echo luna_htmlspecialchars($recipient) ?></h3>
    </div>
    <form id="email" method="post" action="misc.php?email=<?php echo $recipient_id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
        <fieldset class="postfield">
            <input type="hidden" name="form_sent" value="1" />
            <input type="hidden" name="redirect_url" value="<?php echo luna_htmlspecialchars($redirect_url) ?>" />
            <label class="required hidden"><?php echo $lang['Subject'] ?></label>
            <input class="form-control" placeholder="<?php echo $lang['Subject'] ?>" type="text" name="req_subject" maxlength="70" tabindex="1" />
            <label class="required hidden"><?php echo $lang['Message'] ?></label>
            <textarea name="req_message" class="form-control" rows="10" tabindex="2"></textarea>
        </fieldset>
        <div class="panel-footer">
            <div class="btn-group"><input type="submit" class="btn btn-primary" name="submit" value="<?php echo $lang['Submit'] ?>" tabindex="3" accesskey="s" /><a href="javascript:history.go(-1)" class="btn btn-link"><?php echo $lang['Go back'] ?></a></div>
        </div>
    </form>
</div>
<?php

	require FORUM_ROOT.'footer.php';
}


else if (isset($_GET['report']))
{
	if ($luna_user['is_guest'])
		message($lang['No permission'], false, '403 Forbidden');

	$post_id = intval($_GET['report']);
	if ($post_id < 1)
		message($lang['Bad request'], false, '404 Not Found');

	if (isset($_POST['form_sent']))
	{
		confirm_referrer('misc.php');

		// Clean up reason from POST
		$reason = luna_linebreaks(luna_trim($_POST['req_reason']));
		if ($reason == '')
			message($lang['No reason']);
		else if (strlen($reason) > 65535) // TEXT field can only hold 65535 bytes
			message($lang['Reason too long']);

		if ($luna_user['last_report_sent'] != '' && (time() - $luna_user['last_report_sent']) < $luna_user['g_report_flood'] && (time() - $luna_user['last_report_sent']) >= 0)
			message(sprintf($lang['Report flood'], $luna_user['g_report_flood'], $luna_user['g_report_flood'] - (time() - $luna_user['last_report_sent'])));

		// Get the topic ID
		$result = $db->query('SELECT topic_id FROM '.$db->prefix.'posts WHERE id='.$post_id) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang['Bad request'], false, '404 Not Found');

		$topic_id = $db->result($result);

		// Get the subject and forum ID
		$result = $db->query('SELECT subject, forum_id FROM '.$db->prefix.'topics WHERE id='.$topic_id) or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang['Bad request'], false, '404 Not Found');

		list($subject, $forum_id) = $db->fetch_row($result);
		define('MARKED', '1');

		// Should we use the internal report handling?
		if ($luna_config['o_report_method'] == '0' || $luna_config['o_report_method'] == '2')
			$db->query('INSERT INTO '.$db->prefix.'reports (post_id, topic_id, forum_id, reported_by, created, message) VALUES('.$post_id.', '.$topic_id.', '.$forum_id.', '.$luna_user['id'].', '.time().', \''.$db->escape($reason).'\')' ) or error('Unable to create report', __FILE__, __LINE__, $db->error());
			$db->query('UPDATE '.$db->prefix.'posts SET marked = 1 WHERE id='.$post_id) or error('Unable to create report', __FILE__, __LINE__, $db->error());

		// Should we email the report?
		if ($luna_config['o_report_method'] == '1' || $luna_config['o_report_method'] == '2')
		{
			// We send it to the complete mailing-list in one swoop
			if ($luna_config['o_mailing_list'] != '')
			{
				// Load the "new report" template
				$mail_tpl = trim(file_get_contents(FORUM_ROOT.'lang/'.$luna_user['language'].'/mail_templates/new_report.tpl'));

				// The first row contains the subject
				$first_crlf = strpos($mail_tpl, "\n");
				$mail_subject = trim(substr($mail_tpl, 8, $first_crlf-8));
				$mail_message = trim(substr($mail_tpl, $first_crlf));

				$mail_subject = str_replace('<forum_id>', $forum_id, $mail_subject);
				$mail_subject = str_replace('<topic_subject>', $subject, $mail_subject);
				$mail_message = str_replace('<username>', $luna_user['username'], $mail_message);
				$mail_message = str_replace('<post_url>', get_base_url().'/viewtopic.php?pid='.$post_id.'#p'.$post_id, $mail_message);
				$mail_message = str_replace('<reason>', $reason, $mail_message);
				$mail_message = str_replace('<board_mailer>', $luna_config['o_board_title'], $mail_message);

				require FORUM_ROOT.'include/email.php';

				luna_mail($luna_config['o_mailing_list'], $mail_subject, $mail_message);
			}
		}

		$db->query('UPDATE '.$db->prefix.'users SET last_report_sent='.time().' WHERE id='.$luna_user['id']) or error('Unable to update user', __FILE__, __LINE__, $db->error());

		redirect('viewforum.php?id='.$forum_id);
	}

	// Fetch some info about the post, the topic and the forum
	$result = $db->query('SELECT f.id AS fid, f.forum_name, t.id AS tid, t.subject FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'topics AS t ON t.id=p.topic_id INNER JOIN '.$db->prefix.'forums AS f ON f.id=t.forum_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND p.id='.$post_id) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	if (!$db->num_rows($result))
		message($lang['Bad request'], false, '404 Not Found');

	$cur_post = $db->fetch_assoc($result);

	if ($luna_config['o_censoring'] == '1')
		$cur_post['subject'] = censor_words($cur_post['subject']);

	$page_title = array(luna_htmlspecialchars($luna_config['o_board_title']), $lang['Report post']);
	$required_fields = array('req_reason' => $lang['Reason']);
	$focus_element = array('report', 'req_reason');
	define('FORUM_ACTIVE_PAGE', 'index');
	require FORUM_ROOT.'header.php';

?>
<div class="btn-group btn-breadcrumb">
    <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-home"></span></a>
    <a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_post['fid'] ?>"><?php echo luna_htmlspecialchars($cur_post['forum_name']) ?></a>
    <a class="btn btn-primary" href="viewtopic.php?pid=<?php echo $id ?>#p<?php echo $id ?>"><?php echo luna_htmlspecialchars($cur_post['subject']) ?></a>
    <a class="btn btn-primary" href="#"><?php echo $lang['Report post'] ?></a>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Reason desc'] ?></h3>
    </div>
	<form id="report" method="post" action="misc.php?report=<?php echo $post_id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
		<fieldset>
			<input type="hidden" name="form_sent" value="1" />
			<textarea class="form-control" name="req_reason" rows="5"></textarea>
		</fieldset>
		<div class="panel-footer">
			<input type="submit" class="btn btn-primary" name="submit" value="<?php echo $lang['Submit'] ?>" accesskey="s" /><a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a>
		</div>
	</form>
</div>
<?php

	require FORUM_ROOT.'footer.php';
}


else if ($action == 'subscribe')
{
	if ($luna_user['is_guest'])
		message($lang['No permission'], false, '403 Forbidden');

	$topic_id = isset($_GET['tid']) ? intval($_GET['tid']) : 0;
	$forum_id = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
	if ($topic_id < 1 && $forum_id < 1)
		message($lang['Bad request'], false, '404 Not Found');

	if ($topic_id)
	{
		if ($luna_config['o_topic_subscriptions'] != '1')
			message($lang['No permission'], false, '403 Forbidden');

		// Make sure the user can view the topic
		$result = $db->query('SELECT 1 FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=t.forum_id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.id='.$topic_id.' AND t.moved_to IS NULL') or error('Unable to fetch topic info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang['Bad request'], false, '404 Not Found');

		$result = $db->query('SELECT 1 FROM '.$db->prefix.'topic_subscriptions WHERE user_id='.$luna_user['id'].' AND topic_id='.$topic_id) or error('Unable to fetch subscription info', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result))
			message($lang['Is subscribed']);

		$db->query('INSERT INTO '.$db->prefix.'topic_subscriptions (user_id, topic_id) VALUES('.$luna_user['id'].' ,'.$topic_id.')') or error('Unable to add subscription', __FILE__, __LINE__, $db->error());

		redirect('viewtopic.php?id='.$topic_id);
	}

	if ($forum_id)
	{
		if ($luna_config['o_forum_subscriptions'] != '1')
			message($lang['No permission'], false, '403 Forbidden');

		// Make sure the user can view the forum
		$result = $db->query('SELECT 1 FROM '.$db->prefix.'forums AS f LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.id='.$forum_id) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang['Bad request'], false, '404 Not Found');

		$result = $db->query('SELECT 1 FROM '.$db->prefix.'forum_subscriptions WHERE user_id='.$luna_user['id'].' AND forum_id='.$forum_id) or error('Unable to fetch subscription info', __FILE__, __LINE__, $db->error());
		if ($db->num_rows($result))
			message($lang['Already subscribed forum']);

		$db->query('INSERT INTO '.$db->prefix.'forum_subscriptions (user_id, forum_id) VALUES('.$luna_user['id'].' ,'.$forum_id.')') or error('Unable to add subscription', __FILE__, __LINE__, $db->error());

		redirect('viewforum.php?id='.$forum_id);
	}
}


else if ($action == 'unsubscribe')
{
	if ($luna_user['is_guest'])
		message($lang['No permission'], false, '403 Forbidden');

	$topic_id = isset($_GET['tid']) ? intval($_GET['tid']) : 0;
	$forum_id = isset($_GET['fid']) ? intval($_GET['fid']) : 0;
	if ($topic_id < 1 && $forum_id < 1)
		message($lang['Bad request'], false, '404 Not Found');

	if ($topic_id)
	{
		if ($luna_config['o_topic_subscriptions'] != '1')
			message($lang['No permission'], false, '403 Forbidden');

		$result = $db->query('SELECT 1 FROM '.$db->prefix.'topic_subscriptions WHERE user_id='.$luna_user['id'].' AND topic_id='.$topic_id) or error('Unable to fetch subscription info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang['Not subscribed topic']);

		$db->query('DELETE FROM '.$db->prefix.'topic_subscriptions WHERE user_id='.$luna_user['id'].' AND topic_id='.$topic_id) or error('Unable to remove subscription', __FILE__, __LINE__, $db->error());

		redirect('viewtopic.php?id='.$topic_id);
	}

	if ($forum_id)
	{
		if ($luna_config['o_forum_subscriptions'] != '1')
			message($lang['No permission'], false, '403 Forbidden');

		$result = $db->query('SELECT 1 FROM '.$db->prefix.'forum_subscriptions WHERE user_id='.$luna_user['id'].' AND forum_id='.$forum_id) or error('Unable to fetch subscription info', __FILE__, __LINE__, $db->error());
		if (!$db->num_rows($result))
			message($lang['Not subscribed forum']);

		$db->query('DELETE FROM '.$db->prefix.'forum_subscriptions WHERE user_id='.$luna_user['id'].' AND forum_id='.$forum_id) or error('Unable to remove subscription', __FILE__, __LINE__, $db->error());

		redirect('viewforum.php?id='.$forum_id);
	}
}


else
	message($lang['Bad request'], false, '404 Not Found');
