<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

++$topic_count;
$status_text = array();
$item_status = ($topic_count % 2 == 0) ? 'roweven' : 'rowodd';
$icon_type = 'icon';

$subject = '<a href="viewtopic.php?id='.$cur_search['tid'].'">'.luna_htmlspecialchars($cur_search['subject']).'</a> <span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_search['poster']).'</span>';

if ($cur_search['sticky'] == '1')
{
    $item_status .= ' isticky';
    $status_text[] = '<span class="label label-success">'.$lang['Sticky'].'</span>';
}

if ($cur_search['closed'] != '0')
{
    $status_text[] = '<span class="label label-danger">'.$lang['Closed'].'</span>';
    $item_status .= ' iclosed';
}

if (!$luna_user['is_guest'] && $cur_search['last_post'] > $luna_user['last_visit'] && (!isset($tracked_topics['topics'][$cur_search['tid']]) || $tracked_topics['topics'][$cur_search['tid']] < $cur_search['last_post']) && (!isset($tracked_topics['forums'][$cur_search['forum_id']]) || $tracked_topics['forums'][$cur_search['forum_id']] < $cur_search['last_post']))
{
    $item_status .= ' inew';
    $icon_type = 'icon icon-new';
    $subject = '<strong>'.$subject.'</strong>';
    $subject_new_posts = '<span class="newtext">[ <a href="viewtopic.php?id='.$cur_search['tid'].'&amp;action=new" title="'.$lang['New posts info'].'">'.$lang['New posts'].'</a> ]</span>';
}
else
    $subject_new_posts = null;

// Insert the status text before the subject
$subject = implode(' ', $status_text).' '.$subject;

$num_pages_topic = ceil(($cur_search['num_replies'] + 1) / $luna_user['disp_posts']);

if ($num_pages_topic > 1)
    $subject_multipage = '<span class="pagestext">'.simple_paginate($num_pages_topic, -1, 'viewtopic.php?id='.$cur_search['tid']).'</span>';
else
    $subject_multipage = null;

// Should we show the "New posts" and/or the multipage links?
if (!empty($subject_new_posts) || !empty($subject_multipage))
{
    $subject .= !empty($subject_new_posts) ? ' '.$subject_new_posts : '';
    $subject .= !empty($subject_multipage) ? ' '.$subject_multipage : '';
}

?>
        <div class="row topic-row <?php echo $item_status ?>">
            <div class="col-md-6 col-sm-6 col-xs-7">
                <div class="<?php echo $icon_type ?>"><div class="nosize"><?php echo forum_number_format($topic_count + $start_from) ?></div></div>
                <div class="tclcon">
					<?php echo $subject."\n" ?>
                </div>
            </div>
            <div class="col-md-2 hidden-sm hidden-xs"><?php echo $forum ?></div>
            <div class="col-md-1 col-sm-2 hidden-xs"><p class="text-center"><?php echo forum_number_format($cur_search['num_replies']) ?></p></div>
            <div class="col-md-3 col-sm-4 col-xs-5"><?php 
			if ($cur_search['last_poster_id'] > '1')
				echo '<a href="viewtopic.php?pid='.$cur_search['last_post_id'].'#p'.$cur_search['last_post_id'].'">'.format_time($cur_search['last_post']).'</a> <span class="byuser">'.$lang['by'].' <a href="profile.php?id='.$cur_search['last_poster_id'].'">'.luna_htmlspecialchars($cur_search['last_poster']).'</a>';
			else
				echo '<a href="viewtopic.php?pid='.$cur_search['last_post_id'].'#p'.$cur_search['last_post_id'].'">'.format_time($cur_search['last_post']).'</a> <span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_search['last_poster']);
			
			?></span></div>
        </div>
