<?php

/*
 * Copyright (C) 2013-2015 Luna
 * License: http://opensource.org/licenses/MIT MIT
 */

// Show errors that occured when there are errors
function draw_error_panel($errors) {
	global $lang, $cur_error;

	if (!empty($errors)) {
?>
	<div class="panel panel-danger">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Post errors'] ?></h3>
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
	global $lang, $hide_smilies, $message;

	if (!empty($message)) {
		require_once FORUM_ROOT.'include/parser.php';
		$preview_message = parse_message($message);
	
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
}

// Show the editor panel
function draw_editor($height) {
	global $lang, $orig_message, $quote, $fid, $is_admmod, $can_edit_subject, $cur_post, $message, $luna_config;
	
	$pin_btn = $silence_btn = '';

	if (isset($_POST['stick_topic']) || $cur_post['sticky'] == '1') {
		$pin_status = ' checked';
		$pin_active = ' active';
	} else {
		$pin_status = '';
		$pin_active = '';
	}

	if ($fid && $is_admmod || $can_edit_subject && $is_admmod)
		$pin_btn = '<div class="btn-group" data-toggle="buttons"><label class="btn btn-success'.$pin_active.'"><input type="checkbox" name="stick_topic" value="1"'.$pin_status.' /><span class="fa fa-fw fa-thumb-tack"></span></label></div>';

	if (FORUM_ACTIVE_PAGE == 'edit') {
		if ((isset($_POST['form_sent']) && isset($_POST['silent'])) || !isset($_POST['form_sent'])) {
			$silence_status = ' checked';
			$silence_active = ' active';
		}
	
		if ($is_admmod)
			$silence_btn = '<div class="btn-group" data-toggle="buttons"><label class="btn btn-success'.$silence_active.'"><input type="checkbox" name="silent" value="1"'.$silence_status.' /><span class="fa fa-fw fa-microphone-slash"></span></label></div>';
	}

?>
<div class="panel panel-default panel-editor">
	<fieldset class="postfield">
		<input type="hidden" name="form_sent" value="1" />
		<div class="btn-toolbar textarea-toolbar textarea-top">
			<?php echo $pin_btn ?>
			<?php echo $silence_btn ?>
			<div class="btn-group">
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','b');" title="<?php echo $lang['Bold']; ?>"><span class="fa fa-fw fa-bold fa-fw"></span></a>
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','u');" title="<?php echo $lang['Underline']; ?>"><span class="fa fa-fw fa-underline fa-fw"></span></a>
				<a class="btn btn-default btn-editor hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','i');" title="<?php echo $lang['Italic']; ?>"><span class="fa fa-fw fa-italic fa-fw"></span></a>
				<a class="btn btn-default btn-editor hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','s');" title="<?php echo $lang['Strike']; ?>"><span class="fa fa-fw fa-strikethrough fa-fw"></span></a>
			</div>
			<div class="btn-group">
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','h');" title="<?php echo $lang['Heading']; ?>"><span class="fa fa-fw fa-header fa-fw"></span></a>
				<a class="btn btn-default btn-editor hidden-md hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','sub');" title="<?php echo $lang['Subscript']; ?>"><span class="fa fa-fw fa-subscript fa-fw"></span></a>
				<a class="btn btn-default btn-editor hidden-md hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','sup');" title="<?php echo $lang['Superscript']; ?>"><span class="fa fa-fw fa-superscript fa-fw"></span></a>
			</div>
			<div class="btn-group">
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','quote');" title="<?php echo $lang['Quote']; ?>"><span class="fa fa-fw fa-quote-left fa-fw"></span></a>
				<a class="btn btn-default btn-editor hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('code','code');" title="<?php echo $lang['Code']; ?>"><span class="fa fa-fw fa-code fa-fw"></span></a>
				<a class="btn btn-default btn-editor hidden-md hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','c');" title="<?php echo $lang['Inline code']; ?>"><span class="fa fa-fw fa-file-code-o fa-fw"></span></a>
			</div>
			<div class="btn-group">
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','url');" title="<?php echo $lang['URL']; ?>"><span class="fa fa-fw fa-link fa-fw"></span></a>
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','img');" title="<?php echo $lang['Image']; ?>"><span class="fa fa-fw fa-image fa-fw"></span></a>
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','video');" title="<?php echo $lang['Video']; ?>"><span class="fa fa-fw fa-play-circle fa-fw"></span></a>
			</div>
			<div class="btn-group">
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('list', 'list');" title="<?php echo $lang['List']; ?>"><span class="fa fa-fw fa-list-ul fa-fw"></span></a>
				<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','*');" title="<?php echo $lang['List item']; ?>"><span class="fa fa-fw fa-asterisk fa-fw"></span></a>
			</div>
			<div class="btn-group">
<?php if ($luna_config['o_emoji'] == 1) { ?>
				<div class="btn-group">
					<a class="btn btn-default btn-editor btn-emoji dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<span class="fa fa-fw text-emoji emoji-ed">&#x263a;</span>
					</a>
					<ul class="dropdown-menu dropdown-menu-right dropdown-emoji" role="menu">
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', ':)');"><span class="text-emoji emoji-ed">&#x263a;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', ':|');"><span class="text-emoji emoji-ed">&#x1f611;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', ':(');"><span class="text-emoji emoji-ed">&#x1f629;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', ':D');"><span class="text-emoji emoji-ed">&#x1f604;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', ':o');"><span class="text-emoji emoji-ed">&#x1f632;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', ';)');"><span class="text-emoji emoji-ed">&#x1f609;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', ':/');"><span class="text-emoji emoji-ed">&#x1f612;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', ':P');"><span class="text-emoji emoji-ed">&#x1f60b;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', '^.^');"><span class="text-emoji emoji-ed">&#x1f600;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', ':@');"><span class="text-emoji emoji-ed">&#x1f620;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', '%)');"><span class="text-emoji emoji-ed">&#x1f606;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', 'B:');"><span class="text-emoji emoji-ed">&#x1f60e;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', ':hc:');"><span class="text-emoji emoji-ed">&#x1f605;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', '(a)');"><span class="text-emoji emoji-ed">&#x1f607;</span></a></li>
						<li><a href="javascript:void(0);" onclick="AddTag('emoji', '^-^');"><span class="text-emoji emoji-ed">&#x1f60f;</span></a></li>
					</ul>
				</div>
<?php } else { ?>
				<div class="btn-group">
					<a class="btn btn-default btn-editor emoticon-ed dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/smile.png" width="15" height="15" />
					</a>
					<ul class="dropdown-menu dropdown-menu-right dropdown-emoticon" role="menu">
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', ':)');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/smile.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', ':|');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/neutral.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', ':(');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/sad.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', ':D');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/big_smile.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', ':o');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/yikes.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', ';)');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/wink.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', ':/');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/hmm.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', ':P');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/tongue.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', '^.^');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/happy.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', ':@');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/angry.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', '%)');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/roll.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', 'B:');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/cool.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', ':hc:');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/happycry.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', '(a)');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/angel.png" width="15" height="15" /></a></li>
						<li><a class="emoticon-ed" href="javascript:void(0);" onclick="AddTag('emoji', '^-^');"><img src="<?php echo luna_htmlspecialchars(get_base_url(true)) ?>/img/smilies/ohyeah.png" width="15" height="15" /></a></li>
					</ul>
				</div>
<?php } ?>
			</div>
		</div>
		<textarea class="form-control textarea"  placeholder="<?php echo $lang['Start typing'] ?>" name="req_message" id="post_field" rows="<?php echo $height ?>"><?php
			if (FORUM_ACTIVE_PAGE == 'post')
				echo isset($_POST['req_message']) ? luna_htmlspecialchars($orig_message) : (isset($quote) ? $quote : '');
			elseif (FORUM_ACTIVE_PAGE == 'edit')
				echo luna_htmlspecialchars(isset($_POST['req_message']) ? $message : $cur_post['message']);
?></textarea>
		<div class="btn-toolbar textarea-toolbar textarea-bottom">
			<div class="btn-group pull-right">
				<button class="btn btn-with-text btn-default" type="submit" name="preview" accesskey="p"><span class="fa fa-fw fa-eye"></span> <?php echo $lang['Preview'] ?></button>
				<button class="btn btn-with-text btn-primary" type="submit" name="submit" accesskey="s"><span class="fa fa-fw fa-plus"></span> <?php echo $lang['Submit'] ?></button>
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
	   Field.value = before_txt + tag + after_txt;
}
</script>
<?php
}

function draw_topics_list() {
	global $luna_user, $luna_config, $db, $sort_by, $start_from, $id, $lang, $db_type, $tracked_topics;
	
	// Retrieve a list of topic IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
	if ($luna_user['g_soft_delete_view'])
		$result = $db->query('SELECT id FROM '.$db->prefix.'topics WHERE forum_id='.$id.' ORDER BY sticky DESC, '.$sort_by.', id DESC LIMIT '.$start_from.', '.$luna_user['disp_topics']) or error('Unable to fetch topic IDs', __FILE__, __LINE__, $db->error());
	else
		$result = $db->query('SELECT id FROM '.$db->prefix.'topics WHERE soft = 0 AND forum_id='.$id.' ORDER BY sticky DESC, '.$sort_by.', id DESC LIMIT '.$start_from.', '.$luna_user['disp_topics']) or error('Unable to fetch topic IDs', __FILE__, __LINE__, $db->error());
	
	// If there are topics in this forum
	if ($db->num_rows($result)) {
		$topic_ids = array();
		for ($i = 0; $cur_topic_id = $db->result($result, $i); $i++)
			$topic_ids[] = $cur_topic_id;
	
		// Fetch list of topics to display on this page
		if ($luna_user['is_guest'] || $luna_config['o_has_posted'] == '0') {
			// When not showing a posted label
			if ($luna_user['is_admmod'])
				$sql = 'SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, last_poster_id, num_views, num_replies, closed, sticky, moved_to, soft FROM '.$db->prefix.'topics WHERE id IN('.implode(',', $topic_ids).') ORDER BY sticky DESC, '.$sort_by.', id DESC';
			else
				$sql = 'SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, last_poster_id, num_views, num_replies, closed, sticky, moved_to, soft FROM '.$db->prefix.'topics WHERE SOFT = 0 AND id IN('.implode(',', $topic_ids).') ORDER BY sticky DESC, '.$sort_by.', id DESC';
		} else {
			// When showing a posted label
			if ($luna_user['g_soft_delete_view'])
				$sql = 'SELECT p.poster_id AS has_posted, t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.last_poster_id, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, t.soft FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id AND p.poster_id='.$luna_user['id'].' WHERE t.id IN('.implode(',', $topic_ids).') GROUP BY t.id'.($db_type == 'pgsql' ? ', t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, p.poster_id' : '').' ORDER BY t.sticky DESC, t.'.$sort_by.', t.id DESC';
			else
				$sql = 'SELECT p.poster_id AS has_posted, t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.last_poster_id, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, t.soft FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id AND p.poster_id='.$luna_user['id'].' WHERE soft = 0 AND t.id IN('.implode(',', $topic_ids).') GROUP BY t.id'.($db_type == 'pgsql' ? ', t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, p.poster_id' : '').' ORDER BY t.sticky DESC, t.'.$sort_by.', t.id DESC';
		}
	
		$result = $db->query($sql) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
	
		$topic_count = 0;
		while ($cur_topic = $db->fetch_assoc($result)) {
	
			++$topic_count;
			$status_text = array();
			$item_status = ($topic_count % 2 == 0) ? 'roweven' : 'rowodd';
			$icon_type = 'icon';
			$subject = luna_htmlspecialchars($cur_topic['subject']);
			$last_post_date = '<a href="viewtopic.php?pid='.$cur_topic['last_post_id'].'#p'.$cur_topic['last_post_id'].'">'.format_time($cur_topic['last_post']).'</a>';
	
			if (is_null($cur_topic['moved_to']))
				if ($luna_user['g_view_users'] == '1' && $cur_topic['last_poster_id'] > '1')
					$last_poster = '<span class="byuser">'.$lang['by'].' <a href="profile.php?id='.$cur_topic['last_poster_id'].'">'.luna_htmlspecialchars($cur_topic['last_poster']).'</a></span>';
				else
					$last_poster = '<span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['last_poster']).'</span>';
			else
				$last_poster = '';
	
			if ($luna_config['o_censoring'] == '1')
				$cur_topic['subject'] = censor_words($cur_topic['subject']);
	
			if ($cur_topic['sticky'] == '1') {
				$item_status .= ' sticky-item';
				$status_text[] = '<span class="label label-success">'.$lang['Sticky'].'</span>';
			}
	
			if ($cur_topic['moved_to'] != 0) {
				$url = 'viewtopic.php?id='.$cur_topic['moved_to'];
				$by = '<span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
				$status_text[] = '<span class="label label-info">'.$lang['Moved'].'</span>';
				$item_status .= ' moved-item';
			} elseif ($cur_topic['closed'] == '0') {
				$url = 'viewtopic.php?id='.$cur_topic['id'];
				$by = '<span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
			} else {
				$url = 'viewtopic.php?id='.$cur_topic['id'];
				$by = '<span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
				$status_text[] = '<span class="label label-danger">'.$lang['Closed'].'</span>';
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
				$subject_new_posts = '<span class="newtext">[ <a href="viewtopic.php?id='.$cur_topic['id'].'&amp;action=new" title="'.$lang['New posts info'].'">'.$lang['New posts'].'</a> ]</span>';
			} else
				$subject_new_posts = null;

			$subject_status = implode(' ', $status_text);
	
			$num_pages_topic = ceil(($cur_topic['num_replies'] + 1) / $luna_user['disp_posts']);
	
			if ($num_pages_topic > 1)
				$subject_multipage = '<span class="inline-pagination"> '.simple_paginate($num_pages_topic, -1, 'viewtopic.php?id='.$cur_topic['id']).'</span>';
			else
				$subject_multipage = null;
	
			if (forum_number_format($cur_topic['num_replies']) == '1') {
				$replies_label = $lang['reply'];
			} else {
				$replies_label = $lang['replies'];
			}
	
			if (forum_number_format($cur_topic['num_views']) == '1') {
				$views_label = $lang['view'];
			} else {
				$views_label = $lang['views'];
			}
	
			require get_view_path('topic.php');
	
		}
	
	} else {
		'<h3 class="nothing">'.printf($lang['No threads'], $id).'</h3>';
	}
	
}

function draw_forum_list($page, $forum_object_name = 'forum.php', $use_cat = 0, $cat_object_name = 'category.php', $close_tags = '') {
	global $lang, $db, $luna_config, $luna_user, $id, $new_topics;
	
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
		
			// Are there new posts since our last visit?
			if (isset($new_topics[$cur_forum['fid']])) {
				$item_status .= ' new-item';
				$forum_field_new = '<span class="newtext">[ <a href="search.php?action=show_new&amp;fid='.$cur_forum['fid'].'">'.$lang['New posts'].'</a> ]</span>';
				$icon_type = 'icon icon-new';
			}
		
			$forum_field = '<a href="viewforum.php?id='.$cur_forum['fid'].'">'.luna_htmlspecialchars($cur_forum['forum_name']).'</a>'.(!empty($forum_field_new) ? ' '.$forum_field_new : '');
		
			if ($cur_forum['forum_desc'] != '')
				$forum_desc = '<div class="forum-description">'.luna_htmlspecialchars($cur_forum['forum_desc']).'</div>';
		
			if (forum_number_format($cur_forum['num_posts']) == '1')
				$topics_label = $lang['topic'];
			else
				$topics_label = $lang['topics'];
		
			if (forum_number_format($cur_forum['num_topics']) == '1')
				$posts_label = $lang['post'];
			else
				$posts_label = $lang['posts'];
			
			if ($id == $cur_forum['fid'])
				$item_status .= ' active';

			// If there is a last_post/last_poster
			if ($cur_forum['last_post'] != '') {
				if (luna_strlen($cur_forum['subject']) > 43)
					$cur_forum['subject'] = utf8_substr($cur_forum['subject'], 0, 40).'...';
		
					if ($luna_user['g_view_users'] == '1' && $cur_forum['last_poster_id'] > '1')
						$last_post = '<a href="viewtopic.php?pid='.$cur_forum['last_post_id'].'#p'.$cur_forum['last_post_id'].'">'.luna_htmlspecialchars($cur_forum['subject']).'</a><br /><span class="bytime  hidden-xs">'.format_time($cur_forum['last_post']).' </span><span class="byuser">'.$lang['by'].' <a href="profile.php?id='.$cur_forum['last_poster_id'].'">'.luna_htmlspecialchars($cur_forum['username']).'</a></span>';
					else
						$last_post = '<a href="viewtopic.php?pid='.$cur_forum['last_post_id'].'#p'.$cur_forum['last_post_id'].'">'.luna_htmlspecialchars($cur_forum['subject']).'</a><br /><span class="bytime  hidden-xs">'.format_time($cur_forum['last_post']).' </span><span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_forum['username']).'</span>';
			} else
				$last_post = $lang['Never'];
		
			require get_view_path($forum_object_name);
		}
	}
			
	// Any need to close of a category?
	if ($use_cat == 1) {
		if ($cur_category > 0)
			echo $close_tags;
		else
			echo '<div class="no-board"><p>'.$lang['Empty board'].'</p></div>';
	}
}

function draw_subforum_list($page, $object_name = 'forum.php') {
	global $lang, $db, $luna_config, $luna_user, $id, $new_topics;
	
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
		
			// Are there new posts since our last visit?
			if (isset($new_topics[$cur_forum['fid']])) {
				$item_status .= ' new-item';
				$forum_field_new = '<span class="newtext">[ <a href="search.php?action=show_new&amp;fid='.$cur_forum['fid'].'">'.$lang['New posts'].'</a> ]</span>';
				$icon_type = 'icon icon-new';
			}
		
			$forum_field = '<a href="viewforum.php?id='.$cur_forum['fid'].'">'.luna_htmlspecialchars($cur_forum['forum_name']).'</a>'.(!empty($forum_field_new) ? ' '.$forum_field_new : '');
		
			if ($cur_forum['forum_desc'] != '')
				$forum_desc = '<div class="forum-description">'.luna_htmlspecialchars($cur_forum['forum_desc']).'</div>';
		
			if (forum_number_format($cur_forum['num_posts']) == '1')
				$topics_label = $lang['topic'];
			else
				$topics_label = $lang['topics'];
		
			if (forum_number_format($cur_forum['num_topics']) == '1')
				$posts_label = $lang['post'];
			else
				$posts_label = $lang['posts'];
			
			if ($id == $cur_forum['fid'])
				$item_status .= ' active';
		
			require get_view_path($object_name);
		}
	}
}

function draw_section_info($current_id) {
	global $lang, $result, $db, $luna_config, $cur_section;

	if ($current_id != 0) {
		$result = $db->query('SELECT * FROM '.$db->prefix.'forums where id = '.$current_id) or error('Unable to fetch forum info', __FILE__, __LINE__, $db->error());
		
		if (!$db->num_rows($result))
			message($lang['Bad request'], false, '404 Not Found');
		
		$cur_section = $db->fetch_assoc($result);

		$section_head = '1';
	} else
		$section_head = '2';

	require get_view_path('section-info.php');
}

function draw_index_topics_list($section_id) {
	global $luna_user, $luna_config, $db, $start_from, $id, $lang, $sort_by, $start_from, $db_type, $cur_topic, $tracked_topics;
	
	// Retrieve a list of topic IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
	if ($section_id != 0)
		$result = $db->query('SELECT id FROM '.$db->prefix.'topics WHERE forum_id='.$section_id.' ORDER BY sticky DESC, '.$sort_by.', id DESC LIMIT '.$start_from.', '.$luna_user['disp_topics']) or error('Unable to fetch topic IDs', __FILE__, __LINE__, $db->error());
	else
		$result = $db->query('SELECT id FROM '.$db->prefix.'topics WHERE moved_to IS NULL ORDER BY id DESC LIMIT 30') or error('Unable to fetch topic IDs', __FILE__, __LINE__, $db->error());
	
	// If there are topics in this forum
	if ($db->num_rows($result)) {
		$topic_ids = array();
		for ($i = 0; $cur_topic_id = $db->result($result, $i); $i++)
			$topic_ids[] = $cur_topic_id;

		// Fetch list of topics to display on this page
		if ($luna_user['is_guest'] || $luna_config['o_has_posted'] == '0') {
			if ($luna_user['g_soft_delete_view']) {
				// When not showing a posted label
				if ($section_id != 0)
					$sql = 'SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, last_poster_id, num_views, num_replies, closed, sticky, moved_to, soft FROM '.$db->prefix.'topics WHERE id IN('.implode(',', $topic_ids).') ORDER BY sticky DESC, '.$sort_by.', id DESC';
				else
					$sql = 'SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, last_poster_id, num_views, num_replies, closed, sticky, moved_to, soft FROM '.$db->prefix.'topics WHERE id IN('.implode(',', $topic_ids).') ORDER BY last_post DESC';
			} else {
				// When not showing a posted label
				if ($section_id != 0)
					$sql = 'SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, last_poster_id, num_views, num_replies, closed, sticky, moved_to, soft FROM '.$db->prefix.'topics WHERE soft = 0 AND id IN('.implode(',', $topic_ids).') ORDER BY sticky DESC, '.$sort_by.', id DESC';
				else
					$sql = 'SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, last_poster_id, num_views, num_replies, closed, sticky, moved_to, soft FROM '.$db->prefix.'topics WHERE soft = 0 AND id IN('.implode(',', $topic_ids).') ORDER BY last_post DESC';
			}
		} else {
			if ($luna_user['g_soft_delete_view']) {
				// When showing a posted label
				if ($section_id != 0)
					$sql = 'SELECT p.poster_id AS has_posted, t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.last_poster_id, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, t.soft FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id AND p.poster_id='.$luna_user['id'].' WHERE t.id IN('.implode(',', $topic_ids).') GROUP BY t.id'.($db_type == 'pgsql' ? ', t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, p.poster_id' : '').' ORDER BY t.sticky DESC, t.'.$sort_by.', t.id DESC';
				else
					$sql = 'SELECT p.poster_id AS has_posted, t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.last_poster_id, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, t.soft FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id AND p.poster_id='.$luna_user['id'].' WHERE t.id IN('.implode(',', $topic_ids).') GROUP BY t.id'.($db_type == 'pgsql' ? ', t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, p.poster_id' : '').' ORDER BY t.last_post DESC';
			} else {
				// When showing a posted label
				if ($section_id != 0)
					$sql = 'SELECT p.poster_id AS has_posted, t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.last_poster_id, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, t.soft FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id AND p.poster_id='.$luna_user['id'].' WHERE t.soft = 0 AND t.id IN('.implode(',', $topic_ids).') GROUP BY t.id'.($db_type == 'pgsql' ? ', t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, p.poster_id' : '').' ORDER BY t.sticky DESC, t.'.$sort_by.', t.id DESC';
				else
					$sql = 'SELECT p.poster_id AS has_posted, t.id, t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.last_poster_id, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, t.soft FROM '.$db->prefix.'topics AS t LEFT JOIN '.$db->prefix.'posts AS p ON t.id=p.topic_id AND p.poster_id='.$luna_user['id'].' WHERE t.soft = 0 AND t.id IN('.implode(',', $topic_ids).') GROUP BY t.id'.($db_type == 'pgsql' ? ', t.subject, t.poster, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_views, t.num_replies, t.closed, t.sticky, t.moved_to, p.poster_id' : '').' ORDER BY t.last_post DESC';
			}
		}
	
		$result = $db->query($sql) or error('Unable to fetch topic list', __FILE__, __LINE__, $db->error());
	
		$topic_count = 0;
		while ($cur_topic = $db->fetch_assoc($result)) {
	
			++$topic_count;
			$status_text = array();
			$item_status = ($topic_count % 2 == 0) ? 'roweven' : 'rowodd';
			$icon_type = 'icon';
			$subject = luna_htmlspecialchars($cur_topic['subject']);
			$last_post_date = '<a href="viewtopic.php?pid='.$cur_topic['last_post_id'].'#p'.$cur_topic['last_post_id'].'">'.format_time($cur_topic['last_post']).'</a>';

			if (is_null($cur_topic['moved_to']))
				if ($luna_user['g_view_users'] == '1' && $cur_topic['last_poster_id'] > '1')
					$last_poster = '<span class="byuser">'.$lang['by'].' <a href="profile.php?id='.$cur_topic['last_poster_id'].'">'.luna_htmlspecialchars($cur_topic['last_poster']).'</a></span>';
				else
					$last_poster = '<span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['last_poster']).'</span>';
			else
				$last_poster = '';
	
			if ($luna_config['o_censoring'] == '1')
				$cur_topic['subject'] = censor_words($cur_topic['subject']);
	
			if ($cur_topic['sticky'] == '1') {
				$item_status .= ' sticky-item';
				$status_text[] = '<span class="label label-success">'.$lang['Sticky'].'</span>';
			}
	
			if ($cur_topic['moved_to'] != 0) {
				$url = 'viewtopic.php?id='.$cur_topic['moved_to'];
				$by = '<span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
				$status_text[] = '<span class="label label-info">'.$lang['Moved'].'</span>';
				$item_status .= ' moved-item';
			} elseif ($cur_topic['closed'] == '0') {
				$url = 'viewtopic.php?id='.$cur_topic['id'];
				$by = '<span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
			} else {
				$by = '<span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
				$status_text[] = '<span class="label label-danger">'.$lang['Closed'].'</span>';
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
				$subject_new_posts = '<span class="newtext">[ <a href="viewtopic.php?id='.$cur_topic['id'].'&amp;action=new" title="'.$lang['New posts info'].'">'.$lang['New posts'].'</a> ]</span>';
			} else
				$subject_new_posts = null;
	
			$subject_status = implode(' ', $status_text);
	
			$num_pages_topic = ceil(($cur_topic['num_replies'] + 1) / $luna_user['disp_posts']);
	
			if ($num_pages_topic > 1)
				$subject_multipage = '<span class="inline-pagination"> '.simple_paginate($num_pages_topic, -1, 'viewtopic.php?id='.$cur_topic['id']).'</span>';
			else
				$subject_multipage = null;
	
			if (forum_number_format($cur_topic['num_replies']) == '1') {
				$replies_label = $lang['reply'];
			} else {
				$replies_label = $lang['replies'];
			}
	
			if (forum_number_format($cur_topic['num_views']) == '1') {
				$views_label = $lang['view'];
			} else {
				$views_label = $lang['views'];
			}
	
			require get_view_path('topic.php');
	
		}
	
	} elseif ($section_id != 0) {
		'<h3 class="nothing">'.printf($lang['No threads'], $id).'</h3>';
	} else {
		echo '<h3 class="nothing">'.$lang['No threads board'].'</h3>';
	}
	
}

function draw_topic_list() {
	global $lang, $result, $db, $luna_config, $id, $post_ids, $is_admmod, $start_from, $post_count, $admin_ids, $luna_user, $cur_topic;

	// Retrieve the posts (and their respective poster/online status)
	$result = $db->query('SELECT u.email, u.title, u.url, u.location, u.signature, u.email_setting, u.num_posts, u.registered, u.admin_note, p.id, p.poster AS username, p.poster_id, p.poster_ip, p.poster_email, p.message, p.hide_smilies, p.posted, p.edited, p.edited_by, p.marked, p.soft, g.g_id, g.g_user_title, o.user_id AS is_online FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'users AS u ON u.id=p.poster_id INNER JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id LEFT JOIN '.$db->prefix.'online AS o ON (o.user_id=u.id AND o.user_id!=1 AND o.idle=0) WHERE p.id IN ('.implode(',', $post_ids).') ORDER BY p.id', true) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());
	while ($cur_post = $db->fetch_assoc($result)) {
		$post_count++;
		$user_avatar = '';
		$user_info = array();
		$user_actions = array();
		$post_actions = array();
		$is_online = '';
		$signature = '';
	
		// If the poster is a registered user
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
	
					$user_info[] = '<dd><span>'.$lang['From'].' '.luna_htmlspecialchars($cur_post['location']).'</span></dd>';
				}
	
				if ($luna_config['o_show_post_count'] == '1' || $luna_user['is_admmod'])
					$user_info[] = '<dd><span>'.$lang['Posts'].' '.forum_number_format($cur_post['num_posts']).'</span></dd>';
	
				// Now let's deal with the contact links (Email and URL)
				if ((($cur_post['email_setting'] == '0' && !$luna_user['is_guest']) || $luna_user['is_admmod']) && $luna_user['g_send_email'] == '1')
					$user_actions[] = '<a class="btn btn-primary btn-xs" href="mailto:'.luna_htmlspecialchars($cur_post['email']).'">'.$lang['Email'].'</a>';
				elseif ($cur_post['email_setting'] == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
					$user_actions[] = '<a class="btn btn-primary btn-xs" href="misc.php?email='.$cur_post['poster_id'].'">'.$lang['Email'].'</a>';
	
				if ($cur_post['url'] != '') {
					if ($luna_config['o_censoring'] == '1')
						$cur_post['url'] = censor_words($cur_post['url']);
	
					$user_actions[] = '<a class="btn btn-primary btn-xs" href="'.luna_htmlspecialchars($cur_post['url']).'" rel="nofollow">'.$lang['Website'].'</a>';
				}
	
	
				if ($luna_user['is_admmod']) {
					$user_actions[] = '<a class="btn btn-primary btn-xs" href="backstage/moderate.php?get_host='.$cur_post['id'].'" title="'.luna_htmlspecialchars($cur_post['poster_ip']).'">'.$lang['IP address logged'].'</a>';
				}
			}
	
	
			if ($luna_user['is_admmod']) {
				if ($cur_post['admin_note'] != '')
					$user_info[] = '<dd><span>'.$lang['Note'].' <strong>'.luna_htmlspecialchars($cur_post['admin_note']).'</strong></span></dd>';
			}
		}
		// If the poster is a guest (or a user that has been deleted)
		else {
			$username = luna_htmlspecialchars($cur_post['username']);
			$user_title = get_title($cur_post);
	
			if ($luna_user['is_admmod'])
				$user_info[] = '<dd><span><a href="backstage/moderate.php?get_host='.$cur_post['id'].'" title="'.luna_htmlspecialchars($cur_post['poster_ip']).'">'.$lang['IP address logged'].'</a></span></dd>';
	
			if ($luna_config['o_show_user_info'] == '1' && $cur_post['poster_email'] != '' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
				$user_actions[] = '<span class="email"><a href="mailto:'.luna_htmlspecialchars($cur_post['poster_email']).'">'.$lang['Email'].'</a></span>';
		}
	
		// Get us the avatar
		if ($luna_config['o_avatars'] == '1' && $luna_user['show_avatars'] != '0') {
			if (isset($user_avatar_cache[$cur_post['poster_id']]))
				$user_avatar = $user_avatar_cache[$cur_post['poster_id']];
			else
				$user_avatar = draw_user_avatar($cur_post['poster_id'], 'thread-avatar');
		}
	
		// Generation post action array (quote, edit, delete etc.)
		if (!$is_admmod) {
			if (!$luna_user['is_guest']) {
				if ($cur_post['marked'] == false) {
					$post_actions[] = '<a href="misc.php?report='.$cur_post['id'].'">'.$lang['Report'].'</a>';
				} else {
					$post_actions[] = '<a class="btn btn-danger btn-xs" disabled="disabled" href="misc.php?report='.$cur_post['id'].'">'.$lang['Report'].'</a>';
				}
			}
	
			if ($cur_topic['closed'] == 0) {
				if ($cur_post['poster_id'] == $luna_user['id']) {
					if ((($start_from + $post_count) == 1 && $luna_user['g_delete_topics'] == 0) || (($start_from + $post_count) > 1 && $luna_user['g_delete_posts'] == 1))
						$post_actions[] = '<a href="delete.php?id='.$cur_post['id'].'&action=delete">'.$lang['Delete'].'</a>';
					if ((($start_from + $post_count) == 1 && $luna_user['g_soft_delete_topics'] == 0) || (($start_from + $post_count) > 1 && $luna_user['g_soft_delete_posts'] == 1)) {
						if ($cur_post['soft'] == 0)
							$post_actions[] = '<a href="delete.php?id='.$cur_post['id'].'&action=soft">Soft delete</a>';
						else
							$post_actions[] = '<a href="delete.php?id='.$cur_post['id'].'&action=reset">Soft reset</a>';
					}
					if ($luna_user['g_edit_posts'] == 1)
						$post_actions[] = '<a href="edit.php?id='.$cur_post['id'].'">'.$lang['Edit'].'</a>';
				}
	
				if (($cur_topic['post_replies'] == 0 && $luna_user['g_post_replies'] == 1) || $cur_topic['post_replies'] == 1)
					$post_actions[] = '<a href="post.php?tid='.$id.'&amp;qid='.$cur_post['id'].'">'.$lang['Quote'].'</a>';
			}
		} else {
			if ($cur_post['marked'] == false) {
				$post_actions[] = '<a href="misc.php?report='.$cur_post['id'].'">'.$lang['Report'].'</a>';
			} else {
				$post_actions[] = '<a class="reported" disabled="disabled" href="misc.php?report='.$cur_post['id'].'">'.$lang['Report'].'</a>';
			}
			if ($luna_user['g_id'] == FORUM_ADMIN || !in_array($cur_post['poster_id'], $admin_ids)) {
				$post_actions[] = '<a href="delete.php?id='.$cur_post['id'].'&action=delete">'.$lang['Delete'].'</a>';
				if ($cur_post['soft'] == 0)
					$post_actions[] = '<a href="delete.php?id='.$cur_post['id'].'&action=soft">Soft delete</a>';
				else
					$post_actions[] = '<a href="delete.php?id='.$cur_post['id'].'&action=reset">Soft reset</a>';
				$post_actions[] = '<a href="edit.php?id='.$cur_post['id'].'">'.$lang['Edit'].'</a>';
			}
			$post_actions[] = '<a href="post.php?tid='.$id.'&amp;qid='.$cur_post['id'].'">'.$lang['Quote'].'</a>';
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
	global $lang, $result, $db, $luna_config, $id, $post_ids, $is_admmod, $start_from, $post_count, $admin_ids, $luna_user, $inbox;

	while ($cur_post = $db->fetch_assoc($result)) {	
		$post_count++;
		$user_avatar = '';
		$user_info = array();
		$user_contacts = array();
		$post_actions = array();
		$is_online = '';
		$signature = '';
		
		// If the poster is a registered user
		if ($cur_post['id']) {
			if ($luna_user['g_view_users'] == '1')
				$username = '<a href="profile.php?id='.$cur_post['sender_id'].'">'.luna_htmlspecialchars($cur_post['sender']).'</a>';
			else
				$username = luna_htmlspecialchars($cur_post['sender']);
				
			$user_title = get_title($cur_post);
	
			if ($luna_config['o_censoring'] == '1')
				$user_title = censor_words($user_title);
	
			// Format the online indicator
			$is_online = ($cur_post['is_online'] == $cur_post['sender_id']) ? '<strong>'.$lang['Online'].'</strong>' : '<span>'.$lang['Offline'].'</span>';
	
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
	
					$user_info[] = '<dd><span>'.$lang['From'].' '.luna_htmlspecialchars($cur_post['location']).'</span></dd>';
				}
	
				$user_info[] = '<dd><span>'.$lang['Registered'].' '.format_time($cur_post['registered'], true).'</span></dd>';
	
				if ($luna_config['o_show_post_count'] == '1' || $luna_user['is_admmod'])
					$user_info[] = '<dd><span>'.$lang['Posts'].' '.forum_number_format($cur_post['num_posts']).'</span></dd>';
	
				// Now let's deal with the contact links (Email and URL)
				if ((($cur_post['email_setting'] == '0' && !$luna_user['is_guest']) || $luna_user['is_admmod']) && $luna_user['g_send_email'] == '1')
					$user_contacts[] = '<span class="email"><a href="mailto:'.$cur_post['email'].'">'.$lang['Email'].'</a></span>';
				elseif ($cur_post['email_setting'] == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
					$user_contacts[] = '<span class="email"><a href="misc.php?email='.$cur_post['sender_id'].'">'.$lang['Email'].'</a></span>';
					
				if ($luna_config['o_pms_enabled'] == '1' && !$luna_user['is_guest'] && $luna_user['g_pm'] == '1' && $luna_user['use_pm'] == '1' && $cur_post['use_pm'] == '1') {
					$pid = isset($cur_post['sender_id']) ? $cur_post['sender_id'] : $cur_post['sender_id'];
					$user_contacts[] = '<span class="email"><a href="new_inbox.php?uid='.$pid.'">'.$lang['PM'].'</a></span>';
				}
	
				if ($cur_post['url'] != '')
					$user_contacts[] = '<span class="website"><a href="'.luna_htmlspecialchars($cur_post['url']).'">'.$lang['Website'].'</a></span>';
					
			}
	
			if ($luna_user['is_admmod']) {
				$user_info[] = '<dd><span><a href="backstage/moderate.php?get_host='.$cur_post['sender_ip'].'" title="'.$cur_post['sender_ip'].'">'.$lang['IP address logged'].'</a></span></dd>';
	
				if ($cur_post['admin_note'] != '')
					$user_info[] = '<dd><span>'.$lang['Note'].' <strong>'.luna_htmlspecialchars($cur_post['admin_note']).'</strong></span></dd>';
			}
		} else { // If the poster is a guest (or a user that has been deleted)
			$username = luna_htmlspecialchars($cur_post['username']);
			$user_title = get_title($cur_post);
	
			if ($luna_user['is_admmod'])
				$user_info[] = '<dd><span><a href="backstage/moderate.php?get_host='.$cur_post['sender_id'].'" title="'.$cur_post['sender_ip'].'">'.$lang['IP address logged'].'</a></span></dd>';
	
			if ($luna_config['o_show_user_info'] == '1' && $cur_post['poster_email'] != '' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
				$user_contacts[] = '<span class="email"><a href="mailto:'.$cur_post['poster_email'].'">'.$lang['Email'].'</a></span>';
		}
		
		$username_quickreply = luna_htmlspecialchars($cur_post['username']);

		// Generation post action array (reply, delete etc.)
		if ($luna_user['id'] == $cur_post['sender_id'] || $luna_user['is_admmod']) {
			$post_actions[] = '<a href="viewinbox.php?action=delete&amp;mid='.$cur_post['mid'].'&amp;tid='.$cur_post['shared_id'].'">'.$lang['Delete'].'</a>';
			$post_actions[] = '<a href="new_inbox.php?edit='.$cur_post['mid'].'&amp;tid='.$cur_post['shared_id'].'">'.$lang['Edit'].'</a>';
		}

		$post_actions[] = '<a href="new_inbox.php?reply='.$cur_post['shared_id'].'&amp;quote='.$cur_post['mid'].'">'.$lang['Quote'].'</a>';

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
	global $db, $lang, $where_sql, $sort_query, $start_from;
	
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
			$user_avatar = draw_user_avatar($user_data['id'], 'media-object');
	
			require get_view_path('user.php');
	
		}
	} else
		echo '<p>'.$lang['No hits'].'</p>';
}

function draw_delete_form($id) {
	global $is_topic_post, $lang;

?>
		<form method="post" action="delete.php?id=<?php echo $id ?>">
			<p><?php echo ($is_topic_post) ? '<strong>'.$lang['Topic warning'].'</strong>' : '' ?><br /><?php echo $lang['Delete info'] ?></p>
			<input type="submit" class="btn btn-danger" name="delete" value="<?php echo $lang['Delete'] ?>" />
		</form>
<?php
}

function draw_soft_delete_form($id) {
	global $is_topic_post, $lang;

?>
		<form method="post" action="delete.php?id=<?php echo $id ?>&action=soft">
			<p><?php echo ($is_topic_post) ? '<strong>'.$lang['Topic warning'].'</strong>' : '' ?><br /><?php echo $lang['Soft delete info'] ?></p>
			<input type="submit" class="btn btn-danger" name="soft_delete" value="Soft delete" />
		</form>
<?php
}

function draw_soft_reset_form($id) {
	global $is_topic_post, $lang;

?>
		<form method="post" action="delete.php?id=<?php echo $id ?>&action=reset">
			<p><?php echo $lang['Revert soft delete'] ?></p>
			<input type="submit" class="btn btn-primary" name="reset" value="Reset post" />
		</form>
<?php
}

function draw_delete_title() {
	global $is_topic_post, $lang, $cur_post;

	printf($is_topic_post ? $lang['Topic by'] : $lang['Reply by'], '<strong>'.luna_htmlspecialchars($cur_post['poster']).'</strong>', format_time($cur_post['posted']));
}

function draw_registration_form() {
	global $lang, $luna_config;
?>

<form class="form-horizontal" id="register" method="post" action="register.php?action=register" onsubmit="this.register.disabled=true;if(process_form(this)){return true;}else{this.register.disabled=false;return false;}">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Register legend'] ?><span class="pull-right"><input type="submit" class="btn btn-primary" name="register" value="<?php echo $lang['Register'] ?>" /></span></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<input type="hidden" name="form_sent" value="1" />
				<label class="required hidden"><?php echo $lang['If human'] ?><input type="text" class="form-control" name="req_username" value="" maxlength="25" /></label>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Username'] ?><span class="help-block"><?php echo $lang['Username legend'] ?></span></label>
					<div class="col-sm-9">
						<input type="text" class="form-control" name="req_user" value="<?php if (isset($_POST['req_user'])) echo luna_htmlspecialchars($_POST['req_user']); ?>" maxlength="25" />
					</div>
				</div>
<?php if ($luna_config['o_regs_verify'] == '0'): ?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Password'] ?><span class="help-block"><?php echo $lang['Pass info'] ?></span></label>
					<div class="col-sm-9">
						<div class="row">
							<div class="col-sm-6">
								<input id="password" type="password" class="form-control" name="req_password1" value="<?php if (isset($_POST['req_password1'])) echo luna_htmlspecialchars($_POST['req_password1']); ?>" />
							</div>
							<div class="col-sm-6">
								<input type="password" class="form-control" name="req_password2" value="<?php if (isset($_POST['req_password2'])) echo luna_htmlspecialchars($_POST['req_password2']); ?>" />
							</div>
						</div>
					</div>
				</div>
<?php endif; ?>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php echo $lang['Email'] ?><?php if ($luna_config['o_regs_verify'] == '1'): ?><span class="help-block"><?php echo $lang['Email help info'] ?></span><?php endif; ?></label>
					<div class="col-sm-9">
						<?php if ($luna_config['o_regs_verify'] == '1'): ?>
						<div class="row">
							<div class="col-sm-6">
						<?php endif; ?>
								<input type="text" class="form-control" name="req_email1" value="<?php if (isset($_POST['req_email1'])) echo luna_htmlspecialchars($_POST['req_email1']); ?>" maxlength="80" />
						<?php if ($luna_config['o_regs_verify'] == '1'): ?>
							</div>
							<div class="col-sm-6">
								<input type="text" class="form-control" name="req_email2" value="<?php if (isset($_POST['req_email2'])) echo luna_htmlspecialchars($_POST['req_email2']); ?>" maxlength="80" />
							</div>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>
<?php
}

function draw_rules_form() {
	global $lang, $luna_config;
?>

<form method="get" action="register.php">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Rules legend'] ?></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<div class="usercontent"><?php echo $luna_config['o_rules_message'] ?></div>
			</fieldset>
		</div>
		<div class="panel-footer">
			<div class="btn-group"><input type="submit" class="btn btn-primary" name="agree" value="<?php echo $lang['Agree'] ?>" /></div>
		</div>
	</div>
</form>
<?php
}

function draw_search_results() {
	global $search_set, $cur_search, $luna_user, $luna_config, $topic_count, $lang, $cur_topic, $subject_status, $last_post_date, $tracked_topics, $start_from;

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
			
			$subject = '<a href="viewtopic.php?id='.$cur_search['tid'].'">'.luna_htmlspecialchars($cur_search['subject']).'</a>';
			$by = '<span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_search['poster']).'</span>';
			
			if ($cur_search['sticky'] == '1') {
				$item_status .= ' sticky-item';
				$status_text[] = '<span class="label label-success">'.$lang['Sticky'].'</span>';
			}
			
			if ($cur_search['closed'] != '0') {
				$status_text[] = '<span class="label label-danger">'.$lang['Closed'].'</span>';
				$item_status .= ' closed-item';
			}
			
			if (!$luna_user['is_guest'] && $cur_search['last_post'] > $luna_user['last_visit'] && (!isset($tracked_topics['topics'][$cur_search['tid']]) || $tracked_topics['topics'][$cur_search['tid']] < $cur_search['last_post']) && (!isset($tracked_topics['forums'][$cur_search['forum_id']]) || $tracked_topics['forums'][$cur_search['forum_id']] < $cur_search['last_post'])) {
				$item_status .= ' new-item';
				$icon_type = 'icon icon-new';
				$subject = '<strong>'.$subject.'</strong>';
				$subject_new_posts = '<span class="newtext">[ <a href="viewtopic.php?id='.$cur_search['tid'].'&amp;action=new" title="'.$lang['New posts info'].'">'.$lang['New posts'].'</a> ]</span>';
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
				$last_poster = '<a href="viewtopic.php?pid='.$cur_search['last_post_id'].'#p'.$cur_search['last_post_id'].'">'.format_time($cur_search['last_post']).'</a> <span class="byuser">'.$lang['by'].' <a href="profile.php?id='.$cur_search['last_poster_id'].'">'.luna_htmlspecialchars($cur_search['last_poster']).'</a>';
			else
				$last_poster = '<a href="viewtopic.php?pid='.$cur_search['last_post_id'].'#p'.$cur_search['last_post_id'].'">'.format_time($cur_search['last_post']).'</a> <span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_search['last_poster']);

			require get_view_path('search-topic.php');
		// }
	}

}

function draw_mail_form($recipient_id) {
	global $lang, $recipient_id;
?>

<form id="email" method="post" action="misc.php?email=<?php echo $recipient_id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
	<input class="info-textfield form-control" placeholder="<?php echo $lang['Subject'] ?>" type="text" name="req_subject" maxlength="70" tabindex="1" />
	<div class="panel panel-default panel-editor">
		<fieldset class="postfield">
			<input type="hidden" name="form_sent" value="1" />
			<input type="hidden" name="redirect_url" value="<?php echo luna_htmlspecialchars($redirect_url) ?>" />
			<textarea name="req_message" class="form-control textarea" rows="10" tabindex="2"></textarea>
		</fieldset>
		<div class="panel-footer">
			<div class="btn-group"><input type="submit" class="btn btn-primary" name="submit" value="Send" tabindex="3" accesskey="s" /></div>
		</div>
	</div>
</form>
<?php
}

function draw_report_form($post_id) {
	global $lang, $post_id;
?>

<form id="report" method="post" action="misc.php?report=<?php echo $post_id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo $lang['Report reason'] ?></h3>
		</div>
		<fieldset>
			<input type="hidden" name="form_sent" value="1" />
			<textarea class="form-control textarea" name="req_reason" rows="5"></textarea>
		</fieldset>
		<div class="panel-footer">
			<input type="submit" class="btn btn-primary" name="submit" value="<?php echo $lang['Submit'] ?>" accesskey="s" />
		</div>
	</div>
</form>
<?php
}


function draw_search_forum_list() {
	global $db, $luna_config, $luna_user, $lang;

	$result = $db->query('SELECT c.id AS cid, c.cat_name, f.id AS fid, f.forum_name FROM '.$db->prefix.'categories AS c INNER JOIN '.$db->prefix.'forums AS f ON c.id=f.cat_id LEFT JOIN '.$db->prefix.'forum_perms AS fp ON (fp.forum_id=f.id AND fp.group_id='.$luna_user['g_id'].') WHERE (fp.read_forum IS NULL OR fp.read_forum=1) ORDER BY c.disp_position, c.id, f.disp_position', true) or error('Unable to fetch category/forum list', __FILE__, __LINE__, $db->error());

	// We either show a list of forums of which multiple can be selected
	if ($luna_config['o_search_all_forums'] == '1' || $luna_user['is_admmod']) {
		echo "\t\t\t\t\t\t".'<div class="col-xs-4"><div class="conl multiselect"><b>'.$lang['Forum'].'</b>'."\n";
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
		echo "\t\t\t\t\t\t".'<div class="col-xs-4"><label class="conl">'.$lang['Forum']."\n";
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
	global $lang, $luna_user, $id;
	
	if (!empty($class))
		$classes = ' class="'.$class.'"';
		
	if ($page == 'index')
		$url = 'misc.php?action=markread';
	elseif ($page == 'forumview')
		$url = 'misc.php?action=markforumread&amp;fid='.$id;

	if (!$luna_user['is_guest'])
		echo '<a'.$classes.' href="'.$url.'">'.$lang['Mark as read'].'</a>';
}