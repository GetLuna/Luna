<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

$jumbo_style = 'style="background:'.$cur_forum['color'].';"';

?>
</div>
<div class="jumbotron<?php echo $item_status ?>"<?php echo $jumbo_style ?>>
	<div class="container">
		<h2>Moderating <?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></h2><span class="pull-right"><?php echo $paging_links ?></span>
	</div>
</div>
<div class="container">

	<form method="post" action="moderate.php?fid=<?php echo $fid ?>">

<?php


// Retrieve a list of topic IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
$result = $db->query('SELECT id FROM '.$db->prefix.'topics WHERE forum_id='.$fid.' ORDER BY sticky DESC, '.$sort_by.', id DESC LIMIT '.$start_from.', '.$luna_user['disp_topics']) or error('Unable to fetch topic IDs', __FILE__, __LINE__, $db->error());

// If there are topics in this forum
if ($db->num_rows($result)) {
    $topic_ids = array();
    for ($i = 0;$cur_topic_id = $db->result($result, $i);$i++)
        $topic_ids[] = $cur_topic_id;

    // Select topics
    $result = $db->query('SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, last_poster_id, num_views, num_replies, closed, sticky, moved_to FROM '.$db->prefix.'topics WHERE id IN('.implode(',', $topic_ids).') ORDER BY sticky DESC, '.$sort_by.', id DESC') or error('Unable to fetch topic list for forum', __FILE__, __LINE__, $db->error());

    $button_status = '';
    $topic_count = 0;
    while ($cur_topic = $db->fetch_assoc($result)) {

        ++$topic_count;
        $status_text = array();
        $item_status = ($topic_count % 2 == 0) ? 'roweven' : 'rowodd';
        $icon_type = 'icon';

        if (is_null($cur_topic['moved_to'])) {
            $last_post = '<a href="viewtopic.php?pid='.$cur_topic['last_post_id'].'#p'.$cur_topic['last_post_id'].'">'.format_time($cur_topic['last_post']).'</a> <span class="byuser">'.$lang['by'].' <a href="me.php?id='.$cur_topic['last_poster_id'].'">'.luna_htmlspecialchars($cur_topic['last_poster']).'</a></span>';
            $ghost_topic = false;
        } else {
            $last_post = '- - -';
            $ghost_topic = true;
        }

        if ($luna_config['o_censoring'] == '1')
            $cur_topic['subject'] = censor_words($cur_topic['subject']);

        if ($cur_topic['sticky'] == '1') {
            $item_status .= ' isticky';
            $status_text[] = '<span class="label label-success">'.$lang['Sticky'].'</span>';
        }

        if ($cur_topic['moved_to'] != 0) {
            $subject = '<a href="viewtopic.php?id='.$cur_topic['moved_to'].'">'.luna_htmlspecialchars($cur_topic['subject']).'</a> <span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
            $status_text[] = '<span class="label label-info">'.$lang['Moved'].'</span>';
            $item_status .= ' imoved';
        } else if ($cur_topic['closed'] == '0')
            $subject = '<a href="viewtopic.php?id='.$cur_topic['id'].'">'.luna_htmlspecialchars($cur_topic['subject']).'</a> <span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
        else {
            $subject = '<a href="viewtopic.php?id='.$cur_topic['id'].'">'.luna_htmlspecialchars($cur_topic['subject']).'</a> <span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
            $status_text[] = '<span class="label label-danger">'.$lang['Closed'].'</span>';
            $item_status .= ' iclosed';
        }

        if (!$ghost_topic && $cur_topic['last_post'] > $luna_user['last_visit'] && (!isset($tracked_topics['topics'][$cur_topic['id']]) || $tracked_topics['topics'][$cur_topic['id']] < $cur_topic['last_post']) && (!isset($tracked_topics['forums'][$fid]) || $tracked_topics['forums'][$fid] < $cur_topic['last_post'])) {
            $item_status .= ' inew';
            $icon_type = 'icon icon-new';
            $subject = '<strong>'.$subject.'</strong>';
            $subject_new_posts = '<span class="newtext">[ <a href="viewtopic.php?id='.$cur_topic['id'].'&amp;action=new" title="'.$lang['New posts info'].'">'.$lang['New posts'].'</a> ]</span>';
        } else
            $subject_new_posts = null;

        // Insert the status text before the subject
        $subject = implode(' ', $status_text).' '.$subject;

        $num_pages_topic = ceil(($cur_topic['num_replies'] + 1) / $luna_user['disp_posts']);

        if ($num_pages_topic > 1)
            $subject_multipage = '<span class="inline-pagination"> '.simple_paginate($num_pages_topic, -1, 'viewtopic.php?id='.$cur_topic['id']).'</span>';
        else
            $subject_multipage = null;

        // Should we show the "New posts" and/or the multipage links?
        if (!empty($subject_new_posts) || !empty($subject_multipage)) {
            $subject .= !empty($subject_new_posts) ? ' '.$subject_new_posts : '';
            $subject .= !empty($subject_multipage) ? ' '.$subject_multipage : '';
        }

?>
    <div class="topic-entry-list">
<?php require get_view_path('topic.php'); ?>
	</div>
<?php

    }
} else {
    $colspan = ($luna_config['o_topic_views'] == '1') ? 5 : 4;
    $button_status = ' disabled="disabled"';
    echo "\t\t\t\t\t".'<tr><td class="tcl" colspan="'.$colspan.'">'.$lang['Empty forum'].'</td></tr>'."\n";
}

?>	
		<div class="pull-right">
			<div class="btn-group">
				<input type="submit" class="btn btn-primary" name="move_topics" value="<?php echo $lang['Move'] ?>"<?php echo $button_status ?> />
				<input type="submit" class="btn btn-primary" name="delete_topics" value="<?php echo $lang['Delete'] ?>"<?php echo $button_status ?> />
				<input type="submit" class="btn btn-primary" name="merge_topics" value="<?php echo $lang['Merge'] ?>"<?php echo $button_status ?> />
			</div>
			<div class="btn-group">
				<input type="submit" class="btn btn-primary" name="open" value="<?php echo $lang['Open'] ?>"<?php echo $button_status ?> />
				<input type="submit" class="btn btn-primary" name="close" value="<?php echo $lang['Close'] ?>"<?php echo $button_status ?> />
			</div>
		</div>
	</form>

<?php

    require load_page('footer.php');
