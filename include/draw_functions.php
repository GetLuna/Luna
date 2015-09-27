<?php

/*
 * Copyright (C) 2013-2015 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

// Show errors that occured when there are errors
function draw_error_panel($errors) {
	global $cur_error;

	if (!empty($errors)) {
?>
	<div class="panel panel-danger">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Comment errors', 'luna') ?></h3>
		</div>
		<div class="panel-body">
<?php
	foreach ($errors as $cur_error)
		echo $cur_error;
?>
		</div>
	</div>
<?php
	}

}

// Show the preview panel
function draw_preview_panel($message) {
	global $hide_smilies, $message;

	if (!empty($message)) {
		require_once FORUM_ROOT.'include/parser.php';
		$preview_message = parse_message($message);
	
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e('Comment preview', 'luna') ?></h3>
	</div>
	<div class="panel-body">
		<?php echo $preview_message ?>
	</div>
</div>
<?php
	}
}

// Show the editor panel
function draw_editor($height) {
	global $orig_message, $quote, $fid, $is_admmod, $can_edit_subject, $cur_post, $message, $luna_config, $cur_index, $p_message;
	
	$pin_btn = $silence_btn = '';

	if (isset($_POST['stick_topic']) || $cur_post['sticky'] == '1') {
		$pin_status = ' checked';
		$pin_active = ' active';
	} else {
		$pin_status = '';
		$pin_active = '';
	}

	if ($fid && $is_admmod || $can_edit_subject && $is_admmod)
		$pin_btn = '<div class="btn-group" data-toggle="buttons" title="'.__('Pin thread', 'luna').'"><label class="btn btn-success'.$pin_active.'"><input type="checkbox" name="stick_topic" value="1" tabindex="-1"'.$pin_status.' /><span class="fa fa-fw fa-thumb-tack"></span></label></div>';

	if (FORUM_ACTIVE_PAGE == 'edit') {
		if ((isset($_POST['form_sent']) && isset($_POST['silent'])) || !isset($_POST['form_sent'])) {
			$silence_status = ' checked';
			$silence_active = ' active';
		}
	
		if ($is_admmod)
			$silence_btn = '<div class="btn-group" data-toggle="buttons" title="'.__('Mute edit', 'luna').'"><label class="btn btn-success'.$silence_active.'"><input type="checkbox" name="silent" value="1" tabindex="-1"'.$silence_status.' /><span class="fa fa-fw fa-microphone-slash"></span></label></div>';
	}

?>
<div class="panel panel-default panel-editor">
	<fieldset class="postfield editor">
		<input type="hidden" name="form_sent" value="1" />
		<div class="alert alert-warning hide-if-js" role="alert">
			<p><?php _e('The Editor Toolbar requires JavaScript to be enabled. BBCode will still work, though.', 'luna' ); ?></p>
		</div>
		<div class="btn-toolbar textarea-toolbar textarea-top hide-if-no-js">
			<?php echo $pin_btn ?>
			<?php echo $silence_btn ?>
			<div class="btn-group">
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','b');" title="<?php _e('Bold', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-bold fa-fw"></span></a>
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','u');" title="<?php _e('Underline', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-underline fa-fw"></span></a>
				<a class="btn btn-default btn-editor hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','i');" title="<?php _e('Italic', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-italic fa-fw"></span></a>
				<a class="btn btn-default btn-editor hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','s');" title="<?php _e('Strike', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-strikethrough fa-fw"></span></a>
			</div>
			<div class="btn-group">
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','h');" title="<?php _e('Heading', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-header fa-fw"></span></a>
				<a class="btn btn-default btn-editor hidden-md hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','sub');" title="<?php _e('Subscript', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-subscript fa-fw"></span></a>
				<a class="btn btn-default btn-editor hidden-md hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','sup');" title="<?php _e('Superscript', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-superscript fa-fw"></span></a>
			</div>
			<div class="btn-group">
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','quote');" title="<?php _e('Quote', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-quote-left fa-fw"></span></a>
				<a class="btn btn-default btn-editor hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('code','code');" title="<?php _e('Code', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-code fa-fw"></span></a>
				<a class="btn btn-default btn-editor hidden-md hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','c');" title="<?php _e('Inline code', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-file-code-o fa-fw"></span></a>
			</div>
			<div class="btn-group">
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','url');" title="<?php _e('URL', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-link fa-fw"></span></a>
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','img');" title="<?php _e('Image', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-image fa-fw"></span></a>
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','video');" title="<?php _e('Video', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-play-circle fa-fw"></span></a>
			</div>
			<div class="btn-group">
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('list', 'list');" title="<?php _e('List', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-list-ul fa-fw"></span></a>
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','*');" title="<?php _e('List item', 'luna'); ?>" tabindex="-1"><span class="fa fa-fw fa-asterisk fa-fw"></span></a>
			</div>
			<div class="btn-group">
<?php if ($luna_config['o_emoji'] == 1) { ?>
				<a class="btn btn-default btn-editor btn-emoji dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<span class="fa fa-fw text-emoji emoji-ed">&#x263a;</span>
				</a>
				<ul class="dropdown-menu dropdown-menu-right dropdown-emoji" role="menu">
					<li><a href="javascript:void(0);" title="<?php _e('Smile', 'luna'); ?>" onclick="AddTag('emoji', ':)');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x263a;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Neutral', 'luna'); ?>" onclick="AddTag('emoji', ':|');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f611;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Sad', 'luna'); ?>" onclick="AddTag('emoji', ':(');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f629;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Big smile', 'luna'); ?>" onclick="AddTag('emoji', ':D');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f604;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Yikes', 'luna'); ?>" onclick="AddTag('emoji', ':o');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f632;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Wink', 'luna'); ?>" onclick="AddTag('emoji', ';)');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f609;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Hmmm', 'luna'); ?>" onclick="AddTag('emoji', ':/');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f612;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Tongue', 'luna'); ?>" onclick="AddTag('emoji', ':P');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f60b;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Happy', 'luna'); ?>" onclick="AddTag('emoji', '^.^');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f600;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Angry', 'luna'); ?>" onclick="AddTag('emoji', ':@');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f620;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Roll eye', 'luna'); ?>" onclick="AddTag('emoji', '%)');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f606;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Cool', 'luna'); ?>" onclick="AddTag('emoji', 'B:');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f60e;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Happy cry', 'luna'); ?>" onclick="AddTag('emoji', ':hc:');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f605;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Angel', 'luna'); ?>" onclick="AddTag('emoji', '(a)');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f607;</span></a></li>
					<li><a href="javascript:void(0);" title="<?php _e('Oh yeah', 'luna'); ?>" onclick="AddTag('emoji', '^-^');"><span class="text-emoji emoji-ed emoji-ed-dropdown">&#x1f60f;</span></a></li>
				</ul>
<?php } else { ?>
				<a class="btn btn-default btn-editor emoticon-ed dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
					<img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/smile.png" alt="<?php _e('Smilies', 'luna') ?>" width="15" height="15" />
				</a>
				<ul class="dropdown-menu dropdown-menu-right dropdown-emoticon" role="menu">
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Smile', 'luna'); ?>" onclick="AddTag('emoji', ':)');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/smile.png" alt=":)" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Neutral', 'luna'); ?>" onclick="AddTag('emoji', ':|');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/neutral.png" alt=":|" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Sad', 'luna'); ?>" onclick="AddTag('emoji', ':(');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/sad.png" alt=":(" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Big smile', 'luna'); ?>" onclick="AddTag('emoji', ':D');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/big_smile.png" alt=":D" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Yikes', 'luna'); ?>" onclick="AddTag('emoji', ':o');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/yikes.png" alt=":o" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Wink', 'luna'); ?>" onclick="AddTag('emoji', ';)');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/wink.png" alt=";)" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Hmmm', 'luna'); ?>" onclick="AddTag('emoji', ':/');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/hmm.png" alt=":/" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Tongue', 'luna'); ?>" onclick="AddTag('emoji', ':P');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/tongue.png" alt=":P" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Happy', 'luna'); ?>" onclick="AddTag('emoji', '^.^');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/happy.png" alt="^.^" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Angry', 'luna'); ?>" onclick="AddTag('emoji', ':@');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/angry.png" alt=":@" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Roll eye', 'luna'); ?>" onclick="AddTag('emoji', '%)');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/roll.png" alt="%)" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Cool', 'luna'); ?>" onclick="AddTag('emoji', 'B:');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/cool.png" alt="B:" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Happy cry', 'luna'); ?>" onclick="AddTag('emoji', ':hc:');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/happycry.png" alt=":hc:" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Angel', 'luna'); ?>" onclick="AddTag('emoji', '(a)');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/angel.png" alt="(a)" width="15" height="15" /></a></li>
					<li><a class="emoticon-ed emoticon-ed-dropdown" href="javascript:void(0);" title="<?php _e('Oh yeah', 'luna'); ?>" onclick="AddTag('emoji', '^-^');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/ohyeah.png" alt="^-^" width="15" height="15" /></a></li>
				</ul>
<?php } ?>
			</div>
		</div>
		<textarea class="form-control textarea"  placeholder="<?php _e('Start typing...', 'luna') ?>" name="req_message" id="post_field" rows="<?php echo $height ?>" tabindex="<?php echo $cur_index++ ?>"><?php
			if (FORUM_ACTIVE_PAGE == 'post')
				echo isset($_POST['req_message']) ? luna_htmlspecialchars($orig_message) : (isset($quote) ? $quote : '');
			elseif (FORUM_ACTIVE_PAGE == 'edit')
				echo luna_htmlspecialchars(isset($_POST['req_message']) ? $message : $cur_post['message']);
			elseif (FORUM_ACTIVE_PAGE == 'new-inbox')
				echo luna_htmlspecialchars(isset($p_message) ? $p_message : '');
		?></textarea>
		<?php
			if (FORUM_ACTIVE_PAGE == 'edit')
				$action = 'edit-post';
			elseif (FORUM_ACTIVE_PAGE == 'new-inbox')
				$action = 'post-message';
			else
				$action = ($fid ? 'post-topic' : 'post-reply');
			LunaNonces::field($action);
		?>
		<div class="btn-toolbar textarea-toolbar textarea-bottom">
			<div class="btn-group pull-right">
				<button class="btn btn-with-text btn-default" type="submit" name="preview" accesskey="p" tabindex="<?php echo $cur_index++ ?>" onclick="window.onbeforeunload=null"><span class="fa fa-fw fa-eye"></span> <?php _e('Preview', 'luna') ?></button>
				<button class="btn btn-with-text btn-primary" type="submit" name="submit" accesskey="s" tabindex="<?php echo $cur_index++ ?>" onclick="window.onbeforeunload=null"><span class="fa fa-fw fa-plus"></span> <?php _e('Submit', 'luna') ?></button>
			</div>
		</div>
	</fieldset>
</div>
<script>
function AddTag(type, tag) {
   var Field = document.getElementById('post_field');
   var val = Field.value;
   var selected_txt = val.substring(Field.selectionStart, Field.selectionEnd);
   var before_txt = val.substring(0, Field.selectionStart);
   var after_txt = val.substring(Field.selectionEnd, val.length);
   if (type == 'inline')
	   Field.value = before_txt + '[' + tag + ']' + selected_txt + '[/' + tag + ']' + after_txt;
   else if (type == 'list')
	   Field.value = before_txt + '[list]' + "\r" + '[*]' + selected_txt + '[/*]' + "\r" + '[/list]' + after_txt;
   else if (type == 'code')
	   Field.value = before_txt + '[' + tag + ']' + "\r" + '[[language]]' + "\r" + selected_txt + "\r" + '[/' + tag + ']' + after_txt;
   else if (type == 'emoji')
	   Field.value = before_txt + ' ' + tag + ' ' + after_txt;

	document.getElementById('post_field').focus();
}
window.onbeforeunload = function() {
    if ( document.getElementById('post_field').value ) {
	// Don't translate this; we can't change the confirm text anyway.
	return 'Unsaved changes!';
    }
}
</script>
<?php
}

function draw_topics_list() {
	global $luna_user, $luna_config, $db, $sort_by, $start_from, $id, $db_type, $tracked_topics;
	
	// Retrieve a list of thread IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
	if ($luna_user['g_soft_delete_view'])
		$result = $db->query('SELECT id FROM '.$db->prefix.'topics WHERE forum_id='.$id.' ORDER BY sticky DESC, '.$sort_by.', id DESC LIMIT '.$start_from.', '.$luna_user['disp_topics']) or error('Unable to fetch topic IDs', __FILE__, __LINE__, $db->error());
	else
		$result = $db->query('SELECT id FROM '.$db->prefix.'topics WHERE soft = 0 AND forum_id='.$id.' ORDER BY sticky DESC, '.$sort_by.', id DESC LIMIT '.$start_from.', '.$luna_user['disp_topics']) or error('Unable to fetch topic IDs', __FILE__, __LINE__, $db->error());
	
	// If there are topics in this forum
	if ($db->num_rows($result)) {
		$topic_ids = array();
		for ($i = 0; $cur_topic_id = $db->result($result, $i); $i++)
			$topic_ids[] = $cur_topic_id;
	
		// Fetch list of threads to display on this page
		if ($luna_user['is_guest'] || $luna_config['o_has_posted'] == '0') {
			// When not showing a commented label
			if (!$luna_user['is_admmod'])
				$sql_addition = 'soft = 0 AND ';

			$sql = 'SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, last_poster_id, num_views, num_replies, closed, sticky, solved AS answer, moved_to, soft FROM '.$db->prefix.'topics WHERE '.$sql_addition.'id IN('.implode(',', $topic_ids).') ORDER BY sticky DESC, '.$sort_by.', id DESC';
		} else {
			// When showing a commented label
			if (!$luna_user['g_soft_delete_view'])
				$sql_addition = 't.soft = 0 AND ';

			$sql = 'SELECT p.poster_id AS has_posted, t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.last_poster_id, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, t.solved AS answer, t.soft FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id AND p.poster_id='.$luna_user['id'].' WHERE '.$sql_addition.'t.id IN('.implode(',', $topic_ids).') GROUP BY t.id'.($db_type == 'pgsql' ? ', t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, p.poster_id' : '').' ORDER BY t.sticky DESC, t.'.$sort_by.', t.id DESC';
		}
	
		$result = $db->query($sql) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
	
		$topic_count = 0;
		while ($cur_topic = $db->fetch_assoc($result)) {
	
			++$topic_count;
			$status_text = array();
			$item_status = ($topic_count % 2 == 0) ? 'roweven' : 'rowodd';
			$icon_type = 'icon';
			if (luna_strlen($cur_topic['subject']) > 53)
				$subject = utf8_substr($cur_topic['subject'], 0, 50).'...';
			else
				$subject = luna_htmlspecialchars($cur_topic['subject']);
			$last_post_date = '<a href="viewtopic.php?pid='.$cur_topic['last_post_id'].'#p'.$cur_topic['last_post_id'].'">'.format_time($cur_topic['last_post']).'</a>';

			if (is_null($cur_topic['moved_to'])) {
				$topic_id = $cur_topic['id'];

				if ($luna_user['g_view_users'] == '1' && $cur_topic['last_poster_id'] > '1')
					$last_poster = '<span class="byuser">'.__('by', 'luna').' <a href="profile.php?id='.$cur_topic['last_poster_id'].'">'.luna_htmlspecialchars($cur_topic['last_poster']).'</a></span>';
				else
					$last_poster = '<span class="byuser">'.__('by', 'luna').' '.luna_htmlspecialchars($cur_topic['last_poster']).'</span>';
			} else {
				$last_poster = '';
				$topic_id = $cur_topic['moved_to'];
			}
	
			if ($luna_config['o_censoring'] == '1')
				$cur_topic['subject'] = censor_words($cur_topic['subject']);
	
			if ($cur_topic['sticky'] == '1') {
				$item_status .= ' sticky-item';
				$status_text[] = '<span class="label label-warning"><span class="fa fa-fw fa-thumb-tack"></span></span>';
			}
	
			if (isset($cur_topic['answer'])) {
				$item_status .= ' solved-item';
				$status_text[] = '<span class="label label-success"><span class="fa fa-fw fa-check"></span></span>';
			}

			$url = 'viewtopic.php?id='.$topic_id;
			$by = '<span class="byuser">'.__('by', 'luna').' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
	
			if ($cur_topic['moved_to'] != 0) {
				$status_text[] = '<span class="label label-info"><span class="fa fa-fw fa-arrows-alt"></span></span>';
				$item_status .= ' moved-item';
			} elseif ($cur_topic['closed'] == '1') {
				$status_text[] = '<span class="label label-danger"><span class="fa fa-fw fa-lock"></span></span>';
				$item_status .= ' closed-item';
			}
	
			if (!$luna_user['is_guest'] && $luna_config['o_has_posted'] == '1') {
				if ($cur_topic['has_posted'] == $luna_user['id']) {
					$item_status .= ' posted-item';
				}
			}
	
			if (!$luna_user['is_guest'] && $cur_topic['last_post'] > $luna_user['last_visit'] && (!isset($tracked_topics['topics'][$cur_topic['id']]) || $tracked_topics['topics'][$cur_topic['id']] < $cur_topic['last_post']) && (!isset($tracked_topics['forums'][$id]) || $tracked_topics['forums'][$id] < $cur_topic['last_post']) && is_null($cur_topic['moved_to'])) {
				$item_status .= ' new-item';
				$icon_type = 'icon icon-new';
				$subject = '<strong>'.$subject.'</strong>';
				$subject_new_posts = '<span class="newtext">[ <a href="viewtopic.php?id='.$cur_topic['id'].'&amp;action=new" title="'.__('Go to the first new comment in the thread.', 'luna').'">'.__('New comments', 'luna').'</a> ]</span>';
			} else
				$subject_new_posts = null;

			$subject_status = implode(' ', $status_text);
	
			$num_pages_topic = ceil(($cur_topic['num_replies'] + 1) / $luna_user['disp_posts']);
	
			if ($num_pages_topic > 1)
				$subject_multipage = '<span class="inline-pagination"> '.simple_paginate($num_pages_topic, -1, 'viewtopic.php?id='.$cur_topic['id']).'</span>';
			else
				$subject_multipage = null;
	
			$replies_label = _n('reply', 'replies', $cur_topic['num_replies'], 'luna');
			$views_label = _n('view', 'views', $cur_topic['num_views'], 'luna');
	
			require get_view_path('topic.php');
	
		}
	
	} else {
		echo '<div class="forum-row row"><div class="col-xs-12"><h3 class="nothing">';
		printf(__('There are s in this forum yet, but you can <a href="post.php?fid=%s">start the first one</a>.', 'luna'), $id);
		echo '</h3></div></div>';
	}
	
}

function draw_forum_list($forum_object_name = 'forum.php', $use_cat = 0, $cat_object_name = 'category.php', $close_tags = '') {
	global $db, $luna_config, $luna_user, $id, $new_topics;
	
	// Print the categories and forums
	$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.forum_desc, f.parent_id, f.moderators, f.num_topics, f.num_posts, f.last_post, f.last_post_id, f.last_poster_id, f.color, u.username AS username, t.subject AS subject FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id LEFT JOIN '.$db->prefix.'users AS u ON f.last_poster_id=u.id LEFT JOIN '.$db->prefix.'topics AS t ON t.last_post_id=f.last_post_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE fp.read_forum IS NULL OR fp.read_forum=1 ORDER BY c.disp_position, c.id, f.disp_position', true) or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

	$cur_category = 0;
	$cat_count = 0;
	$forum_count = 0;
	while ($cur_forum = $db->fetch_assoc($result)) {
		if(!isset($cur_forum['parent_id']) || $cur_forum['parent_id'] == 0) {
			$moderators = '';
			
			if ($cur_forum['cid'] != $cur_category && $use_cat == 1) {
				if ($cur_category != 0)
					echo '</div></div>';

				++$cat_count;
				$forum_count = 0;

				require get_view_path($cat_object_name);

				$cur_category = $cur_forum['cid'];
			}
	
			++$forum_count;
			$item_status = ($forum_count % 2 == 0) ? 'roweven' : 'rowodd';
			$forum_field_new = '';
			$forum_desc = '';
			$icon_type = 'icon';
			$last_post = '';
		
			// Are there new comments since our last visit?
			if (isset($new_topics[$cur_forum['fid']])) {
				$item_status .= ' new-item';
				$forum_field_new = '<span class="newtext">[ <a href="search.php?action=show_new&amp;fid='.$cur_forum['fid'].'">'.__('New comments', 'luna').'</a> ]</span>';
				$icon_type = 'icon icon-new';
			}
		
			$forum_field = '<a href="viewforum.php?id='.$cur_forum['fid'].'">'.luna_htmlspecialchars($cur_forum['forum_name']).'</a>'.(!empty($forum_field_new) ? ' '.$forum_field_new : '');
		
			if ($cur_forum['forum_desc'] != '')
				$forum_desc = '<div class="forum-description">'.$cur_forum['forum_desc'].'</div>';
		
			$topics_label = __('topic', 'topics', $cur_forum['num_topics'], 'luna');
			$posts_label = __('post', 'posts', $cur_forum['num_posts'], 'luna');
			
			if ($id == $cur_forum['fid'])
				$item_status .= ' active';

			// If there is a last_post/last_poster
			if ($cur_forum['last_post'] != '') {
				if (luna_strlen($cur_forum['subject']) > 53)
					$cur_forum['subject'] = utf8_substr($cur_forum['subject'], 0, 50).'...';
		
					if ($luna_user['g_view_users'] == '1' && $cur_forum['last_poster_id'] > '1')
						$last_post = '<a href="viewtopic.php?pid='.$cur_forum['last_post_id'].'#p'.$cur_forum['last_post_id'].'">'.luna_htmlspecialchars($cur_forum['subject']).'</a><br /><span class="bytime  hidden-xs">'.format_time($cur_forum['last_post']).' </span><span class="byuser">'.__('by', 'luna').' <a href="profile.php?id='.$cur_forum['last_poster_id'].'">'.luna_htmlspecialchars($cur_forum['username']).'</a></span>';
					else
						$last_post = '<a href="viewtopic.php?pid='.$cur_forum['last_post_id'].'#p'.$cur_forum['last_post_id'].'">'.luna_htmlspecialchars($cur_forum['subject']).'</a><br /><span class="bytime  hidden-xs">'.format_time($cur_forum['last_post']).' </span><span class="byuser">'.__('by', 'luna').' '.luna_htmlspecialchars($cur_forum['username']).'</span>';
			} else
				$last_post = __('Never', 'luna');
		
			require get_view_path($forum_object_name);
		}
	}
			
	// Any need to close of a category?
	if ($use_cat == 1) {
		if ($cur_category > 0)
			echo $close_tags;
		else
			echo '<div class="no-board"><p>'.__('There are no forums in this board yet.', 'luna').'</p></div>';
	}
}

function draw_subforum_list($object_name = 'forum.php') {
	global $db, $luna_config, $luna_user, $id, $new_topics;
	
	$result = $db->query('SELECT parent_id FROM '.$db->prefix.'forums WHERE id='.$id) or error ('Unable to fetch information about the current forum', __FILE__, __LINE__, $db->error());
	$cur_parent = $db->fetch_assoc($result);
	
	if ($cur_parent['parent_id'] == '0')
		$subforum_parent_id = $id;
	else
		$subforum_parent_id = $cur_parent['parent_id'];
	
	// Print the categories and forums
	$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.forum_desc, f.parent_id, f.moderators, f.num_topics, f.num_posts, f.last_post, f.last_post_id, f.last_poster_id, f.color, u.username AS username, t.subject AS subject FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id LEFT JOIN '.$db->prefix.'users AS u ON f.last_poster_id=u.id LEFT JOIN '.$db->prefix.'topics AS t ON t.last_post_id=f.last_post_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND f.parent_id='.$subforum_parent_id.' ORDER BY c.disp_position, c.id, f.disp_position', true) or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

	$cur_category = 0;
	$cat_count = 0;
	$forum_count = 0;
	while ($cur_forum = $db->fetch_assoc($result)) {
		if ($cur_forum['parent_id'] != 0)
			$parent_id = $cur_forum['parent_id'];

		if($cur_forum['parent_id'] == $parent_id) {
			$moderators = '';
	
			++$forum_count;
			$item_status = ($forum_count % 2 == 0) ? 'roweven' : 'rowodd';
			$forum_field_new = '';
			$forum_desc = '';
			$icon_type = 'icon';
			$last_post = '';
		
			// Are there new comments since our last visit?
			if (isset($new_topics[$cur_forum['fid']])) {
				$item_status .= ' new-item';
				$forum_field_new = '<span class="newtext">[ <a href="search.php?action=show_new&amp;fid='.$cur_forum['fid'].'">'.__('New comments', 'luna').'</a> ]</span>';
				$icon_type = 'icon icon-new';
			}
		
			$forum_field = '<a href="viewforum.php?id='.$cur_forum['fid'].'">'.luna_htmlspecialchars($cur_forum['forum_name']).'</a>'.(!empty($forum_field_new) ? ' '.$forum_field_new : '');
		
			if ($cur_forum['forum_desc'] != '')
				$forum_desc = '<div class="forum-description">'.$cur_forum['forum_desc'].'</div>';
		
			$topics_label = __('topic', 'topics', $cur_forum['num_topics'], 'luna');
			$posts_label = __('post', 'posts', $cur_forum['num_posts'], 'luna');
			
			if ($id == $cur_forum['fid'])
				$item_status .= ' active';
		
			require get_view_path($object_name);
		}
	}
}

function draw_index_topics_list() {
	global $luna_user, $luna_config, $db, $start_from, $id, $sort_by, $start_from, $db_type, $cur_topic, $tracked_topics;
	
	// Retrieve a list of thread IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
	$result = $db->query('SELECT t.id, t.moved_to FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=t.forum_id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) AND t.moved_to IS NULL ORDER BY last_post DESC LIMIT 30') or error('Unable to fetch topic IDs', __FILE__, __LINE__, $db->error());
	
	// If there are topics in this forum
	if ($db->num_rows($result)) {
		$topic_ids = array();
		for ($i = 0; $cur_topic_id = $db->result($result, $i); $i++)
			$topic_ids[] = $cur_topic_id;

		// Fetch list of threads to display on this page
		$sql_soft = NULL;
		if ($luna_user['is_guest'] || $luna_config['o_has_posted'] == '0') {
			if (!$luna_user['g_soft_delete_view'])
				$sql_soft = 'soft = 0 AND ';

			$sql = 'SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, last_poster_id, num_views, num_replies, closed, sticky, moved_to, soft, solved AS answer, forum_id FROM '.$db->prefix.'topics WHERE '.$sql_soft.'id IN('.implode(',', $topic_ids).') ORDER BY last_post DESC';

		} else {
			if (!$luna_user['g_soft_delete_view'])
				$sql_soft = 't.soft = 0 AND ';

			$sql = 'SELECT p.poster_id AS has_posted, t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.last_poster_id, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, t.soft, t.solved AS answer, t.forum_id FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id AND p.poster_id='.$luna_user['id'].' WHERE '.$sql_soft.'t.id IN('.implode(',', $topic_ids).') GROUP BY t.id'.($db_type == 'pgsql' ? ', t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, p.poster_id' : '').' ORDER BY t.last_post DESC';
		}
	
		$result = $db->query($sql) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());

		// Load cached forums
		if (file_exists(FORUM_CACHE_DIR.'cache_forums.php'))
			include FORUM_CACHE_DIR.'cache_forums.php';
		
		if (!defined('FORUM_LIST_LOADED')) {
			if (!defined('FORUM_CACHE_FUNCTIONS_LOADED'))
				require FORUM_ROOT.'include/cache.php';
		
			generate_forum_cache();
			require FORUM_CACHE_DIR.'cache_forums.php';
		}
	
		$topic_count = 0;
		while ($cur_topic = $db->fetch_assoc($result)) {
			
			++$topic_count;
			$status_text = array();
			$item_status = ($topic_count % 2 == 0) ? 'roweven' : 'rowodd';
			$icon_type = 'icon';
			if (luna_strlen($cur_topic['subject']) > 53)
				$subject = utf8_substr($cur_topic['subject'], 0, 50).'...';
			else
				$subject = luna_htmlspecialchars($cur_topic['subject']);
			$last_post_date = '<a href="viewtopic.php?pid='.$cur_topic['last_post_id'].'#p'.$cur_topic['last_post_id'].'">'.format_time($cur_topic['last_post']).'</a>';

			if (is_null($cur_topic['moved_to'])) {
				$topic_id = $cur_topic['id'];

				if ($luna_user['g_view_users'] == '1' && $cur_topic['last_poster_id'] > '1')
					$last_poster = '<span class="byuser">'.__('by', 'luna').' <a href="profile.php?id='.$cur_topic['last_poster_id'].'">'.luna_htmlspecialchars($cur_topic['last_poster']).'</a></span>';
				else
					$last_poster = '<span class="byuser">'.__('by', 'luna').' '.luna_htmlspecialchars($cur_topic['last_poster']).'</span>';
				
				foreach ($luna_forums as $cur_forum) {
					if ($cur_topic['forum_id'] == $cur_forum['id']) {
						$forum_name = luna_htmlspecialchars($cur_forum['forum_name']);
						$forum_color = $cur_forum['color'];
					}
				}
				
				$forum_name = '<span class="byuser">'.__('in', 'luna').' <a class="label label-default" href="viewforum.php?id='.$cur_topic['forum_id'].'" style="background: '.$forum_color.';">'.$forum_name.'</a></span>';
			} else {
				$last_poster = '';
				$topic_id = $cur_topic['moved_to'];
			}
	
			if ($luna_config['o_censoring'] == '1')
				$cur_topic['subject'] = censor_words($cur_topic['subject']);
	
			if ($cur_topic['sticky'] == '1') {
				$item_status .= ' sticky-item';
				$status_text[] = '<span class="label label-warning"><span class="fa fa-fw fa-thumb-tack"></span></span>';
			}
	
			if (isset($cur_topic['answer'])) {
				$item_status .= ' solved-item';
				$status_text[] = '<span class="label label-success"><span class="fa fa-fw fa-check"></span></span>';
			}

			$url = 'viewtopic.php?id='.$topic_id;
			$by = '<span class="byuser">'.__('by', 'luna').' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
	
			if ($cur_topic['moved_to'] != 0) {
				$status_text[] = '<span class="label label-info"><span class="fa fa-fw fa-arrows-alt"></span></span>';
				$item_status .= ' moved-item';
			} elseif ($cur_topic['closed'] == '1') {
				$status_text[] = '<span class="label label-danger"><span class="fa fa-fw fa-lock"></span></span>';
				$item_status .= ' closed-item';
			}
	
			if (!$luna_user['is_guest'] && $luna_config['o_has_posted'] == '1') {
				if ($cur_topic['has_posted'] == $luna_user['id']) {
					$item_status .= ' posted-item';
				}
			}
	
			if (!$luna_user['is_guest'] && $cur_topic['last_post'] > $luna_user['last_visit'] && (!isset($tracked_topics['topics'][$cur_topic['id']]) || $tracked_topics['topics'][$cur_topic['id']] < $cur_topic['last_post']) && (!isset($tracked_topics['forums'][$id]) || $tracked_topics['forums'][$id] < $cur_topic['last_post']) && is_null($cur_topic['moved_to'])) {
				$item_status .= ' new-item';
				$icon_type = 'icon icon-new';
				$subject_new_posts = '<span class="newtext">[ <a href="viewtopic.php?id='.$cur_topic['id'].'&amp;action=new" title="'.__('Go to the first new comment in the thread.', 'luna').'">'.__('New comments', 'luna').'</a> ]</span>';
			} else
				$subject_new_posts = null;
	
			$subject_status = implode(' ', $status_text);
	
			$num_pages_topic = ceil(($cur_topic['num_replies'] + 1) / $luna_user['disp_posts']);
	
			if ($num_pages_topic > 1)
				$subject_multipage = '<span class="inline-pagination"> '.simple_paginate($num_pages_topic, -1, 'viewtopic.php?id='.$cur_topic['id']).'</span>';
			else
				$subject_multipage = null;
	
			$replies_label = _n('reply', 'replies', $cur_topic['num_replies'], 'luna');
			$views_label = _n('view', 'views', $cur_topic['num_views'], 'luna');
	
			require get_view_path('topic.php');
	
		}
	} else
		echo '<h3 class="nothing">'.__('The board is empty, select a forum and create a thread to begin.', 'luna').'</h3>';
}

function draw_comment_list() {
	global $db, $luna_config, $id, $post_ids, $is_admmod, $start_from, $post_count, $admin_ids, $luna_user, $cur_topic, $started_by;

	// Retrieve the comments (and their respective poster/online status)
	$result = $db->query('SELECT u.email, u.title, u.url, u.location, u.signature, u.email_setting, u.num_posts, u.registered, u.admin_note, p.id, p.poster AS username, p.poster_id, p.poster_ip, p.poster_email, p.message, p.hide_smilies, p.posted, p.edited, p.edited_by, p.marked, p.soft, g.g_id, g.g_user_title, o.user_id AS is_online FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'users AS u ON u.id=p.poster_id INNER JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id LEFT JOIN '.$db->prefix.'online AS o ON (o.user_id=u.id AND o.user_id!=1 AND o.idle=0) WHERE p.id IN ('.implode(',', $post_ids).') ORDER BY p.id', true) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	while ($cur_post = $db->fetch_assoc($result)) {
		$post_count++;
		$user_avatar = '';
		$user_info = array();
		$user_actions = array();
		$post_actions = array();
		$is_online = '';
		$signature = '';
	
		// If the commenter is a registered user
		if ($cur_post['poster_id'] > 1) {
			if ($luna_user['g_view_users'] == '1')
				$username = '<a href="profile.php?id='.$cur_post['poster_id'].'">'.luna_htmlspecialchars($cur_post['username']).'</a>';
			else
				$username = luna_htmlspecialchars($cur_post['username']);
	
			$user_title = get_title($cur_post);
	
			if ($luna_config['o_censoring'] == '1')
				$user_title = censor_words($user_title);
	
			// Format the online indicator, those are ment as CSS classes
			$is_online = ($cur_post['is_online'] == $cur_post['poster_id']) ? 'is-online' : 'is-offline';
	
			// We only show location, register date, post count and the contact links if "Show user info" is enabled
			if ($luna_config['o_show_user_info'] == '1') {
				if ($cur_post['location'] != '') {
					if ($luna_config['o_censoring'] == '1')
						$cur_post['location'] = censor_words($cur_post['location']);
	
					$user_info[] = '<dd><span>'.__('From:', 'luna').' '.luna_htmlspecialchars($cur_post['location']).'</span></dd>';
				}
	
				if ($luna_config['o_show_post_count'] == '1' || $luna_user['is_admmod'])
					$user_info[] = '<dd><span>'._n('Comment:', 'Comments:', $cur_post['num_posts'], 'luna').' '.forum_number_format($cur_post['num_posts']).'</span></dd>';
	
				// Now let's deal with the contact links (Email and URL)
				if ((($cur_post['email_setting'] == '0' && !$luna_user['is_guest']) || $luna_user['is_admmod']) && $luna_user['g_send_email'] == '1')
					$user_actions[] = '<a class="btn btn-primary btn-xs" href="mailto:'.luna_htmlspecialchars($cur_post['email']).'">'.__('Email', 'luna').'</a>';
				elseif ($cur_post['email_setting'] == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
					$user_actions[] = '<a class="btn btn-primary btn-xs" href="misc.php?email='.$cur_post['poster_id'].'">'.__('Email', 'luna').'</a>';
	
				if ($cur_post['url'] != '') {
					if ($luna_config['o_censoring'] == '1')
						$cur_post['url'] = censor_words($cur_post['url']);
	
					$user_actions[] = '<a class="btn btn-primary btn-xs" href="'.luna_htmlspecialchars($cur_post['url']).'" rel="nofollow">'.__('Website', 'luna').'</a>';
				}
	
	
				if ($luna_user['is_admmod']) {
					$user_actions[] = '<a class="btn btn-primary btn-xs" href="backstage/moderate.php?get_host='.$cur_post['id'].'" title="'.luna_htmlspecialchars($cur_post['poster_ip']).'">'.__('IP log', 'luna').'</a>';
				}
			}
	
	
			if ($luna_user['is_admmod']) {
				if ($cur_post['admin_note'] != '')
					$user_info[] = '<dd><span>'.__('Note:', 'luna').' <strong>'.luna_htmlspecialchars($cur_post['admin_note']).'</strong></span></dd>';
			}
		}
		// If the commenter is a guest (or a user that has been deleted)
		else {
			$username = luna_htmlspecialchars($cur_post['username']);
			$user_title = get_title($cur_post);
	
			if ($luna_user['is_admmod'])
				$user_info[] = '<dd><span><a href="backstage/moderate.php?get_host='.$cur_post['id'].'" title="'.luna_htmlspecialchars($cur_post['poster_ip']).'">'.__('IP log', 'luna').'</a></span></dd>';
	
			if ($luna_config['o_show_user_info'] == '1' && $cur_post['poster_email'] != '' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
				$user_actions[] = '<span class="email"><a href="mailto:'.luna_htmlspecialchars($cur_post['poster_email']).'">'.__('Email', 'luna').'</a></span>';
		}
	
		// Get us the avatar
		if ($luna_config['o_avatars'] == '1' && $luna_user['show_avatars'] != '0') {
			if (isset($user_avatar_cache[$cur_post['poster_id']]))
				$user_avatar = $user_avatar_cache[$cur_post['poster_id']];
			else
				$user_avatar = draw_user_avatar($cur_post['poster_id'], false, 'media-object media-avatar');
		}
	
		// Generation post action array (quote, edit, delete etc.)
		if (!$is_admmod) {
			if (!$luna_user['is_guest']) {
				if ($cur_post['marked'] == false) {
					$post_actions[] = '<a href="misc.php?report='.$cur_post['id'].'">'.__('Report', 'luna').'</a>';
				} else {
					$post_actions[] = '<a class="btn btn-danger btn-xs" disabled="disabled" href="misc.php?report='.$cur_post['id'].'">'.__('Report', 'luna').'</a>';
				}
			}
	
			if ($cur_topic['closed'] == 0) {
				if ($cur_post['poster_id'] == $luna_user['id']) {
					if ((($start_from + $post_count) == 1 && $luna_user['g_delete_topics'] == 1) || (($start_from + $post_count) > 1 && $luna_user['g_delete_posts'] == 1))
						$post_actions[] = '<a href="delete.php?id='.$cur_post['id'].'&action=delete">'.__('Delete', 'luna').'</a>';
					if ((($start_from + $post_count) == 1 && $luna_user['g_soft_delete_topics'] == 1) || (($start_from + $post_count) > 1 && $luna_user['g_soft_delete_posts'] == 1)) {
						if ($cur_post['soft'] == 0)
							$post_actions[] = '<a href="delete.php?id='.$cur_post['id'].'&action=soft">'.__('Soft delete', 'luna').'</a>';
						else
							$post_actions[] = '<a href="delete.php?id='.$cur_post['id'].'&action=reset">'.__('Soft reset', 'luna').'</a>';
					}
					if ($luna_user['g_edit_posts'] == 1)
						$post_actions[] = '<a href="edit.php?id='.$cur_post['id'].'">'.__('Edit', 'luna').'</a>';
				}
	
				if (($cur_topic['post_replies'] == 0 && $luna_user['g_post_replies'] == 1) || $cur_topic['post_replies'] == 1)
					$post_actions[] = '<a href="post.php?tid='.$id.'&amp;qid='.$cur_post['id'].'">'.__('Quote', 'luna').'</a>';

				if ($luna_user['username'] == $started_by) {
					if ($cur_post['id'] == $cur_topic['answer'])
						$post_actions[] = '<a href="misc.php?unanswer='.$cur_post['id'].'&amp;tid='.$id.'">'.__('Unsolved', 'luna').'</a>';
					else
						$post_actions[] = '<a href="misc.php?answer='.$cur_post['id'].'&amp;tid='.$id.'">'.__('Answer', 'luna').'</a>';
				}
			}
		} else {
			if ($cur_post['marked'] == false)
				$post_actions[] = '<a href="misc.php?report='.$cur_post['id'].'">'.__('Report', 'luna').'</a>';
			else
				$post_actions[] = '<a disabled="disabled" href="misc.php?report='.$cur_post['id'].'">'.__('Report', 'luna').'</a>';

			if ($luna_user['g_id'] == FORUM_ADMIN || !in_array($cur_post['poster_id'], $admin_ids)) {
				$post_actions[] = '<a href="delete.php?id='.$cur_post['id'].'&action=delete">'.__('Delete', 'luna').'</a>';
				if ($cur_post['soft'] == 0)
					$post_actions[] = '<a href="delete.php?id='.$cur_post['id'].'&action=soft">'.__('Soft delete', 'luna').'</a>';
				else
					$post_actions[] = '<a href="delete.php?id='.$cur_post['id'].'&action=reset">'.__('Soft reset', 'luna').'</a>';
				$post_actions[] = '<a href="edit.php?id='.$cur_post['id'].'">'.__('Edit', 'luna').'</a>';
			}
			$post_actions[] = '<a href="post.php?tid='.$id.'&amp;qid='.$cur_post['id'].'">'.__('Quote', 'luna').'</a>';
			
			if ($cur_post['id'] == $cur_topic['answer'])
				$post_actions[] = '<a href="misc.php?unanswer='.$cur_post['id'].'&amp;tid='.$id.'">'.__('Unsolved', 'luna').'</a>';
			else
				$post_actions[] = '<a href="misc.php?answer='.$cur_post['id'].'&amp;tid='.$id.'">'.__('Answer', 'luna').'</a>';
		}
	
		// Perform the main parsing of the message (BBCode, smilies, censor words etc)
		$cur_post['message'] = parse_message($cur_post['message']);
	
		// Do signature parsing/caching
		if ($luna_config['o_signatures'] == '1' && $cur_post['signature'] != '' && $luna_user['show_sig'] != '0') {
			if (isset($signature_cache[$cur_post['poster_id']]))
				$signature = $signature_cache[$cur_post['poster_id']];
			else {
				$signature = parse_signature($cur_post['signature']);
				$signature_cache[$cur_post['poster_id']] = $signature;
			}
		}
	
		require get_view_path('comment.php');
	}

}

function draw_response_list() {
	global $result, $db, $luna_config, $id, $post_ids, $is_admmod, $start_from, $post_count, $admin_ids, $luna_user, $inbox;

	while ($cur_post = $db->fetch_assoc($result)) {	
		$post_count++;
		$user_avatar = '';
		$user_info = array();
		$user_contacts = array();
		$post_actions = array();
		$is_online = '';
		$signature = '';
		
		// If the commenter is a registered user
		if ($cur_post['id']) {
			if ($luna_user['g_view_users'] == '1')
				$username = '<a href="profile.php?id='.$cur_post['sender_id'].'">'.luna_htmlspecialchars($cur_post['sender']).'</a>';
			else
				$username = luna_htmlspecialchars($cur_post['sender']);
				
			$user_title = get_title($cur_post);
	
			if ($luna_config['o_censoring'] == '1')
				$user_title = censor_words($user_title);
	
			// Format the online indicator
			$is_online = ($cur_post['is_online'] == $cur_post['sender_id']) ? '<strong>'.__('Online:', 'luna').'</strong>' : '<span>'.__('Offline', 'luna').'</span>';
	
			if ($luna_config['o_avatars'] == '1' && $luna_user['show_avatars'] != '0') {
				if (isset($user_avatar_cache[$cur_post['sender_id']]))
					$user_avatar = $user_avatar_cache[$cur_post['sender_id']];
				else
					$user_avatar = $user_avatar_cache[$cur_post['sender_id']] = generate_avatar_markup($cur_post['sender_id']);
			}
	
			// We only show location, register date, post count and the contact links if "Show user info" is enabled
			if ($luna_config['o_show_user_info'] == '1') {
				if ($cur_post['location'] != '') {
					if ($luna_config['o_censoring'] == '1')
						$cur_post['location'] = censor_words($cur_post['location']);
	
					$user_info[] = '<dd><span>'.__('From:', 'luna').' '.luna_htmlspecialchars($cur_post['location']).'</span></dd>';
				}
	
				$user_info[] = '<dd><span>'.__('Registered since', 'luna').' '.format_time($cur_post['registered'], true).'</span></dd>';
	
				if ($luna_config['o_show_post_count'] == '1' || $luna_user['is_admmod'])
					$user_info[] = '<dd><span>'.__('Comments:', 'luna').' '.forum_number_format($cur_post['num_posts']).'</span></dd>';
	
				// Now let's deal with the contact links (Email and URL)
				if ((($cur_post['email_setting'] == '0' && !$luna_user['is_guest']) || $luna_user['is_admmod']) && $luna_user['g_send_email'] == '1')
					$user_contacts[] = '<span class="email"><a href="mailto:'.$cur_post['email'].'">'.__('Email', 'luna').'</a></span>';
				elseif ($cur_post['email_setting'] == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
					$user_contacts[] = '<span class="email"><a href="misc.php?email='.$cur_post['sender_id'].'">'.__('Email', 'luna').'</a></span>';
					
				if ($luna_config['o_pms_enabled'] == '1' && !$luna_user['is_guest'] && $luna_user['g_pm'] == '1' && $luna_user['use_pm'] == '1' && $cur_post['use_pm'] == '1') {
					$pid = isset($cur_post['sender_id']) ? $cur_post['sender_id'] : $cur_post['sender_id'];
					$user_contacts[] = '<span class="email"><a href="new_inbox.php?uid='.$pid.'">'.__('PM', 'luna').'</a></span>';
				}
	
				if ($cur_post['url'] != '')
					$user_contacts[] = '<span class="website"><a href="'.luna_htmlspecialchars($cur_post['url']).'">'.__('Website', 'luna').'</a></span>';
					
			}
	
			if ($luna_user['is_admmod']) {
				$user_info[] = '<dd><span><a href="backstage/moderate.php?get_host='.$cur_post['sender_ip'].'" title="'.$cur_post['sender_ip'].'">'.__('IP log', 'luna').'</a></span></dd>';
	
				if ($cur_post['admin_note'] != '')
					$user_info[] = '<dd><span>'.__('Note:', 'luna').' <strong>'.luna_htmlspecialchars($cur_post['admin_note']).'</strong></span></dd>';
			}
		} else { // If the commenter is a guest (or a user that has been deleted)
			$username = luna_htmlspecialchars($cur_post['username']);
			$user_title = get_title($cur_post);
	
			if ($luna_user['is_admmod'])
				$user_info[] = '<dd><span><a href="backstage/moderate.php?get_host='.$cur_post['sender_id'].'" title="'.$cur_post['sender_ip'].'">'.__('IP log', 'luna').'</a></span></dd>';
	
			if ($luna_config['o_show_user_info'] == '1' && $cur_post['poster_email'] != '' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
				$user_contacts[] = '<span class="email"><a href="mailto:'.$cur_post['poster_email'].'">'.__('Email', 'luna').'</a></span>';
		}
		
		$username_quickreply = luna_htmlspecialchars($cur_post['username']);

		$post_actions[] = '<a href="new_inbox.php?reply='.$cur_post['shared_id'].'&amp;quote='.$cur_post['mid'].'">'.__('Quote', 'luna').'</a>';

		// Perform the main parsing of the message (BBCode, smilies, censor words etc)
		$cur_post['message'] = parse_message($cur_post['message']);
	
		// Do signature parsing/caching
		if ($luna_config['o_signatures'] == '1' && $cur_post['signature'] != '' && $luna_user['show_sig'] != '0') {
			if (isset($signature_cache[$cur_post['id']]))
				$signature = $signature_cache[$cur_post['id']];
			else {
				$signature = parse_signature($cur_post['signature']);
				$signature_cache[$cur_post['id']] = $signature;
			}
		}
	
		require get_view_path('comment.php');
	}
}

function draw_user_list() {
	global $db, $where_sql, $sort_query, $start_from;
	
	// Retrieve a list of user IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
	$result = $db->query('SELECT u.id FROM '.$db->prefix.'users AS u WHERE u.id>1 AND u.group_id!='.FORUM_UNVERIFIED.(!empty($where_sql) ? ' AND '.implode(' AND ', $where_sql) : '').' ORDER BY '.$sort_query.', u.id ASC LIMIT '.$start_from.', 50') or error('Unable to fetch user IDs', __FILE__, __LINE__, $db->error());
	
	if ($db->num_rows($result)) {
		$user_ids = array();
		for ($i = 0;$cur_user_id = $db->result($result, $i);$i++)
			$user_ids[] = $cur_user_id;
	
		// Grab the users
		$result = $db->query('SELECT u.id, u.username, u.title, u.num_posts, u.registered, g.g_id, g.g_user_title FROM '.$db->prefix.'users AS u LEFT JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id WHERE u.id IN('.implode(',', $user_ids).') ORDER BY '.$sort_query.', u.id ASC') or error('Unable to fetch user list', __FILE__, __LINE__, $db->error());
	
		while ($user_data = $db->fetch_assoc($result)) {
			$user_title_field = get_title($user_data);
			$user_avatar = draw_user_avatar($user_data['id'], true, 'media-object');
	
			require get_view_path('user.php');
	
		}
	} else
		echo '<p>'.__('Your search returned no hits.', 'luna').'</p>';
}

function draw_delete_form($id) {
	global $is_topic_post;

?>
		<form method="post" action="delete.php?id=<?php echo $id ?>">
			<p><?php echo ($is_topic_post) ? '<strong>'.__('This is the first comment in the thread, the whole thread will be permanently deleted.', 'luna').'</strong>' : '' ?><br /><?php _e('The comment you have chosen to delete is set out below for you to review before proceeding.', 'luna') ?></p>
			<div class="btn-toolbar">
				<a class="btn btn-default" href="viewtopic.php?pid=<?php echo $id ?>#p<?php echo $id ?>"><span class="fa fa-fw fa-chevron-left"></span> <?php _e('Cancel', 'luna') ?></a>
				<button type="submit" class="btn btn-danger" name="delete"><span class="fa fa-fw fa-trash"></span> <?php _e('Delete', 'luna') ?></button>
			</div>
		</form>
<?php
}

function draw_soft_delete_form($id) {
	global $is_topic_post;

?>
		<form method="post" action="delete.php?id=<?php echo $id ?>&action=soft">
			<p><?php echo ($is_topic_post) ? '<strong>'.__('This is the first comment in the thread, the whole thread will be permanently deleted.', 'luna').'</strong>' : '' ?><br /><?php _e('The comment you have chosen to delete is set out below for you to review before proceeding. Deleting this comment is not permanent. If you want to delete a comment permanently, please use delete instead.', 'luna') ?></p>
			<div class="btn-toolbar">
				<a class="btn btn-default" href="viewtopic.php?pid=<?php echo $id ?>#p<?php echo $id ?>"><span class="fa fa-fw fa-chevron-left"></span> <?php _e('Cancel', 'luna') ?></a>
				<button type="submit" class="btn btn-danger" name="soft_delete"><span class="fa fa-fw fa-trash"></span> <?php _e('Soft delete', 'luna') ?></button>
			</div>
		</form>
<?php
}

function draw_soft_reset_form($id) {
	global $is_topic_post;

?>
		<form method="post" action="delete.php?id=<?php echo $id ?>&action=reset">
			<p><?php _e('This comment has been soft deleted. We\'ll enable it again with a click on the button.', 'luna') ?></p>
			<div class="btn-toolbar">
				<a class="btn btn-default" href="viewtopic.php?pid=<?php echo $id ?>#p<?php echo $id ?>"><span class="fa fa-fw fa-chevron-left"></span> <?php _e('Cancel', 'luna') ?></a>
				<button type="submit" class="btn btn-primary" name="reset"><span class="fa fa-fw fa-undo"></span> <?php _e('Reset comment', 'luna') ?></button>
			</div>
		</form>
<?php
}

function draw_delete_title() {
	global $is_topic_post, $cur_post;

	printf($is_topic_post ? __('Thread started by %s - %s', 'luna') : __('Comment by %s - %s', 'luna'), '<strong>'.luna_htmlspecialchars($cur_post['poster']).'</strong>', format_time($cur_post['posted']));
}

function draw_rules_form() {
	global $luna_config;
?>

<form method="get" action="register.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('You must agree to the following in order to register', 'luna') ?></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="usercontent"><?php echo $luna_config['o_rules_message'] ?></div>
			</fieldset>
		</div>
		<div class="panel-footer">
			<div class="btn-group"><input type="submit" class="btn btn-primary" name="agree" value="<?php _e('Agree', 'luna') ?>" /></div>
		</div>
	</div>
</form>
<?php
}

function draw_search_results() {
	global $search_set, $cur_search, $luna_user, $luna_config, $topic_count, $cur_topic, $subject_status, $last_post_date, $tracked_topics, $start_from;

	foreach ($search_set as $cur_search) {
		$forum = '<a href="viewforum.php?id='.$cur_search['forum_id'].'">'.luna_htmlspecialchars($cur_search['forum_name']).'</a>';

		if ($luna_config['o_censoring'] == '1')
			$cur_search['subject'] = censor_words($cur_search['subject']);

		/* if ($show_as == 'posts') {
			require get_view_path('comment.php');
		} else { */
			++$topic_count;
			$status_text = array();
			$item_status = ($topic_count % 2 == 0) ? 'roweven' : 'rowodd';
			$icon_type = 'icon';
			
			$subject = '<a href="viewtopic.php?id='.$cur_search['tid'].'#p'.$cur_search['pid'].'">'.luna_htmlspecialchars($cur_search['subject']).'</a>';
			$by = '<span class="byuser">'.__('by', 'luna').' '.luna_htmlspecialchars($cur_search['poster']).'</span>';
			
			if ($cur_search['sticky'] == '1') {
				$item_status .= ' sticky-item';
				$status_text[] = '<span class="label label-warning"><span class="fa fa-fw fa-thumb-tack"></span></span>';
			}
			
			if ($cur_search['closed'] != '0') {
				$status_text[] = '<span class="label label-danger"><span class="fa fa-fw fa-lock"></span></span>';
				$item_status .= ' closed-item';
			}
			
			if (!$luna_user['is_guest'] && $cur_search['last_post'] > $luna_user['last_visit'] && (!isset($tracked_topics['topics'][$cur_search['tid']]) || $tracked_topics['topics'][$cur_search['tid']] < $cur_search['last_post']) && (!isset($tracked_topics['forums'][$cur_search['forum_id']]) || $tracked_topics['forums'][$cur_search['forum_id']] < $cur_search['last_post'])) {
				$item_status .= ' new-item';
				$icon_type = 'icon icon-new';
				$subject = '<strong>'.$subject.'</strong>';
				$subject_new_posts = '<span class="newtext">[ <a href="viewtopic.php?id='.$cur_search['tid'].'&amp;action=new" title="'.__('Go to the first new comment in the thread.', 'luna').'">'.__('New comments', 'luna').'</a> ]</span>';
			} else
				$subject_new_posts = null;
			
			// Insert the status text before the subject
			$subject = implode(' ', $status_text).' '.$subject;
			
			$num_pages_topic = ceil(($cur_search['num_replies'] + 1) / $luna_user['disp_posts']);
			
			if ($num_pages_topic > 1)
				$subject_multipage = '<span class="pagestext">'.simple_paginate($num_pages_topic, -1, 'viewtopic.php?id='.$cur_search['tid']).'</span>';
			else
				$subject_multipage = null;
			
			if ($cur_search['last_poster_id'] > '1' && $luna_user['g_view_users'] == '1')
				$last_poster = '<a href="viewtopic.php?pid='.$cur_search['last_post_id'].'#p'.$cur_search['last_post_id'].'">'.format_time($cur_search['last_post']).'</a> <span class="byuser">'.__('by', 'luna').'</span> <a href="profile.php?id='.$cur_search['last_poster_id'].'">'.luna_htmlspecialchars($cur_search['last_poster']).'</a>';
			else
				$last_poster = '<a href="viewtopic.php?pid='.$cur_search['last_post_id'].'#p'.$cur_search['last_post_id'].'">'.format_time($cur_search['last_post']).'</a> <span class="byuser">'.__('by', 'luna').'</span> '.luna_htmlspecialchars($cur_search['last_poster']);

			require get_view_path('search-topic.php');
		// }
	}

}

function draw_mail_form($recipient_id) {
	global $recipient_id, $redirect_url, $cur_index;
?>

<form id="email" method="post" action="misc.php?email=<?php echo $recipient_id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
	<input class="info-textfield form-control" placeholder="<?php _e('Subject', 'luna') ?>" type="text" name="req_subject" maxlength="70" tabindex="<?php echo $cur_index++ ?>" autofocus />
	<div class="panel panel-default panel-editor">
		<fieldset class="postfield">
			<input type="hidden" name="form_sent" value="1" />
			<input type="hidden" name="redirect_url" value="<?php echo luna_htmlspecialchars($redirect_url) ?>" />
			<textarea name="req_message" class="form-control textarea" rows="10" tabindex="<?php echo $cur_index++ ?>"></textarea>
			<div class="btn-toolbar textarea-toolbar textarea-bottom">
				<div class="btn-group pull-right">
					<button class="btn btn-with-text btn-primary" type="submit" name="submit" accesskey="s" tabindex="<?php echo $cur_index++ ?>"><span class="fa fa-fw fa-envelope-o"></span> <?php _e('Send', 'luna') ?></button>
				</div>
			</div>
		</fieldset>
	</div>
</form>
<?php
}

function draw_report_form($post_id) {
	global $post_id;
?>

<form class="form-horizontal" id="report" method="post" action="misc.php?report=<?php echo $post_id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Report', 'luna') ?></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<input type="hidden" name="form_sent" value="1" />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Tell us why you are reporting this', 'luna') ?></label>
					<div class="col-sm-9">
						<textarea class="form-control" name="req_reason" rows="5"></textarea>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-3"></div>
					<div class="col-sm-9">
						<a href="viewtopic.php?pid=<?php echo $post_id ?>#p<?php echo $post_id ?>" class="btn btn-default"><span class="fa fa-fw fa-chevron-left"></span> <?php _e('Cancel', 'luna') ?></a>
						<button type="submit" class="btn btn-primary" name="submit" accesskey="s"><span class="fa fa-fw fa-check"></span> <?php _e('Submit', 'luna') ?></button>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
<?php
}


function draw_search_forum_list() {
	global $db, $luna_config, $luna_user;

	$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) ORDER BY c.disp_position, c.id, f.disp_position', true) or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

	// We either show a list of forums of which multiple can be selected
	if ($luna_config['o_search_all_forums'] == '1' || $luna_user['is_admmod']) {
		echo "\t\t\t\t\t\t".'<div class="col-xs-4"><div class="conl multiselect"><b>'.__('Forum', 'luna').'</b>'."\n";
		echo "\t\t\t\t\t\t".'<br />'."\n";
		echo "\t\t\t\t\t\t".'<div>'."\n";
	
		$cur_category = 0;
		while ($cur_forum = $db->fetch_assoc($result)) {
			if ($cur_forum['cid'] != $cur_category) { // A new category since last iteration?
				if ($cur_category) {
					echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
					echo "\t\t\t\t\t\t\t".'</fieldset>'."\n";
				}
				echo "\t\t\t\t\t\t\t".'<fieldset><h3><span>'.luna_htmlspecialchars($cur_forum['cat_name']).'</span></h3>'."\n";
				echo "\t\t\t\t\t\t\t\t".'<div>';
				$cur_category = $cur_forum['cid'];
			}
			echo "\t\t\t\t\t\t\t\t".'<input type="checkbox" name="forums[]" id="forum-'.$cur_forum['fid'].'" value="'.$cur_forum['fid'].'" /> '.luna_htmlspecialchars($cur_forum['forum_name']).'<br />'."\n";
		}
	
		if ($cur_category) {
			echo "\t\t\t\t\t\t\t\t".'</div>'."\n";
			echo "\t\t\t\t\t\t\t".'</fieldset>'."\n";
		}
	
		echo "\t\t\t\t\t\t".'</div>'."\n";
		echo "\t\t\t\t\t\t".'</div></div>'."\n";
	}
	// ... or a simple select list for one forum only
	else {
		echo "\t\t\t\t\t\t".'<div class="col-xs-4"><label class="conl">'.__('Forum', 'luna')."\n";
		echo "\t\t\t\t\t\t".'<br />'."\n";
		echo "\t\t\t\t\t\t".'<select id="forum" name="forum">'."\n";
	
		$cur_category = 0;
		while ($cur_forum = $db->fetch_assoc($result)) {
			if ($cur_forum['cid'] != $cur_category) { // A new category since last iteration?
				if ($cur_category)
					echo "\t\t\t\t\t\t\t".'</optgroup>'."\n";
	
				echo "\t\t\t\t\t\t\t".'<optgroup label="'.luna_htmlspecialchars($cur_forum['cat_name']).'">'."\n";
				$cur_category = $cur_forum['cid'];
			}
	
			echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_forum['fid'].'">'.($cur_forum['parent_forum_id'] == 0 ? '' : '&nbsp;&nbsp;&nbsp;').luna_htmlspecialchars($cur_forum['forum_name']).'</option>'."\n";
		}
	
		echo "\t\t\t\t\t\t\t".'</optgroup>'."\n";
		echo "\t\t\t\t\t\t".'</select>'."\n";
		echo "\t\t\t\t\t\t".'<br /></label></div>'."\n";
	}
}

function draw_mark_read($class, $page) {
	global $luna_user, $id;
	
	if (!empty($class))
		$classes = ' class="'.$class.'"';
		
	if ($page == 'index')
		$url = 'misc.php?action=markread';
	elseif ($page == 'forumview')
		$url = 'misc.php?action=markforumread&amp;fid='.$id;

	if (!$luna_user['is_guest'])
		echo '<a'.$classes.' href="'.$url.'">'.__('Mark as read', 'luna').'</a>';
}

function draw_wall_error($description, $action = NULL, $title = NULL) {
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php _e('Luna', 'luna') ?></title>
		<link rel="stylesheet" type="text/css" href="include/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="include/css/system.css" />
	</head>
	<body class="wall">
		<h1><?php if ($title != NULL) { echo $title; } else { echo 'Luna'; } ?></h1>
		<p class="lead"><?php echo $description; ?></p>
		<?php if ($action != NULL) { ?><p><?php echo $action; ?></p><?php } ?>
	</body>
</html>
<?php
}

function draw_user_nav_menu() {
	global $luna_user;

	$items = get_user_nav_menu_items();

	require get_view_path('user-navmenu.php');
}
