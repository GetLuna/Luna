<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="row row-nav-fix">
    <div class="col-sm-6">
        <div class="btn-group btn-breadcrumb">
            <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-home"></span></a>
            <a class="btn btn-primary" href="viewforum.php?id=<?php echo $fid ?>"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></a>
            <a class="btn btn-primary" href="#"><?php echo $lang['Moderate'] ?></a>
        </div>
    </div>
    <div class="col-sm-6">
        <ul class="pagination">
            <?php echo $paging_links ?>
        </ul>
    </div>
</div>

<form method="post" action="moderate.php?fid=<?php echo $fid ?>">

<div class="forum-box">
    <div class="row forum-header">
        <div class="col-xs-6"><?php echo $lang['Topic'] ?></div>
        <div class="col-xs-1 hidden-xs"><p class="text-center"><?php echo $lang['Replies forum'] ?></p></div>
        <?php if ($luna_config['o_topic_views'] == '1'): ?>
            <div class="col-xs-1 hidden-xs"><p class="text-center"><?php echo $lang['Views'] ?></p></div>
        <?php endif; ?>
        <div class="col-xs-3 hidden-xs"><?php echo $lang['Last post'] ?></div>
        <div class="col-xs-1"><p class="text-center"><?php echo $lang['Select'] ?></p></div>
    </div>
<?php


// Retrieve a list of topic IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
$result = $db->query('SELECT id FROM '.$db->prefix.'topics WHERE forum_id='.$fid.' ORDER BY sticky DESC, '.$sort_by.', id DESC LIMIT '.$start_from.', '.$luna_user['disp_topics']) or error('Unable to fetch topic IDs', __FILE__, __LINE__, $db->error());

// If there are topics in this forum
if ($db->num_rows($result))
{
    $topic_ids = array();
    for ($i = 0;$cur_topic_id = $db->result($result, $i);$i++)
        $topic_ids[] = $cur_topic_id;

    // Select topics
    $result = $db->query('SELECT id, poster, subject, posted, last_post, last_post_id, last_poster, last_poster_id, num_views, num_replies, closed, sticky, moved_to FROM '.$db->prefix.'topics WHERE id IN('.implode(',', $topic_ids).') ORDER BY sticky DESC, '.$sort_by.', id DESC') or error('Unable to fetch topic list for forum', __FILE__, __LINE__, $db->error());

    $button_status = '';
    $topic_count = 0;
    while ($cur_topic = $db->fetch_assoc($result))
    {

        ++$topic_count;
        $status_text = array();
        $item_status = ($topic_count % 2 == 0) ? 'roweven' : 'rowodd';
        $icon_type = 'icon';

        if (is_null($cur_topic['moved_to']))
        {
            $last_post = '<a href="viewtopic.php?pid='.$cur_topic['last_post_id'].'#p'.$cur_topic['last_post_id'].'">'.format_time($cur_topic['last_post']).'</a> <span class="byuser">'.$lang['by'].' <a href="profile.php?id='.$cur_topic['last_poster_id'].'">'.luna_htmlspecialchars($cur_topic['last_poster']).'</a></span>';
            $ghost_topic = false;
        }
        else
        {
            $last_post = '- - -';
            $ghost_topic = true;
        }

        if ($luna_config['o_censoring'] == '1')
            $cur_topic['subject'] = censor_words($cur_topic['subject']);

        if ($cur_topic['sticky'] == '1')
        {
            $item_status .= ' isticky';
            $status_text[] = '<span class="label label-success">'.$lang['Sticky'].'</span>';
        }

        if ($cur_topic['moved_to'] != 0)
        {
            $subject = '<a href="viewtopic.php?id='.$cur_topic['moved_to'].'">'.luna_htmlspecialchars($cur_topic['subject']).'</a> <span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
            $status_text[] = '<span class="label label-info">'.$lang['Moved'].'</span>';
            $item_status .= ' imoved';
        }
        else if ($cur_topic['closed'] == '0')
            $subject = '<a href="viewtopic.php?id='.$cur_topic['id'].'">'.luna_htmlspecialchars($cur_topic['subject']).'</a> <span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
        else
        {
            $subject = '<a href="viewtopic.php?id='.$cur_topic['id'].'">'.luna_htmlspecialchars($cur_topic['subject']).'</a> <span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_topic['poster']).'</span>';
            $status_text[] = '<span class="label label-danger">'.$lang['Closed'].'</span>';
            $item_status .= ' iclosed';
        }

        if (!$ghost_topic && $cur_topic['last_post'] > $luna_user['last_visit'] && (!isset($tracked_topics['topics'][$cur_topic['id']]) || $tracked_topics['topics'][$cur_topic['id']] < $cur_topic['last_post']) && (!isset($tracked_topics['forums'][$fid]) || $tracked_topics['forums'][$fid] < $cur_topic['last_post']))
        {
            $item_status .= ' inew';
            $icon_type = 'icon icon-new';
            $subject = '<strong>'.$subject.'</strong>';
            $subject_new_posts = '<span class="newtext">[ <a href="viewtopic.php?id='.$cur_topic['id'].'&amp;action=new" title="'.$lang['New posts info'].'">'.$lang['New posts'].'</a> ]</span>';
        }
        else
            $subject_new_posts = null;

        // Insert the status text before the subject
        $subject = implode(' ', $status_text).' '.$subject;

        $num_pages_topic = ceil(($cur_topic['num_replies'] + 1) / $luna_user['disp_posts']);

        if ($num_pages_topic > 1)
            $subject_multipage = '<span class="inline-pagination"> '.simple_paginate($num_pages_topic, -1, 'viewtopic.php?id='.$cur_topic['id']).'</span>';
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
        <div class="col-xs-6">
            <div class="<?php echo $icon_type ?>"><div class="nosize"><?php echo forum_number_format($topic_count + $start_from) ?></div></div>
            <div class="tclcon">
                <div>
                    <?php echo $subject."\n" ?>
                </div>
            </div>
        </div>
                    <div class="col-xs-1 hidden-xs"><p class="text-center"><?php echo (!$ghost_topic) ? forum_number_format($cur_topic['num_replies']) : '-' ?></p></div>
<?php if ($luna_config['o_topic_views'] == '1'): ?>                 <div class="col-xs-1 hidden-xs"><p class="text-center"><?php echo (!$ghost_topic) ? forum_number_format($cur_topic['num_views']) : '-' ?></p></div>
<?php endif; ?>                 <div class="col-xs-3 hidden-xs"><?php echo $last_post ?></div>
                    <div class="col-xs-1"><p class="text-center"><input type="checkbox" name="topics[<?php echo $cur_topic['id'] ?>]" value="1" /></p></div>
    </div>
<?php

    }
}
else
{
    $colspan = ($luna_config['o_topic_views'] == '1') ? 5 : 4;
    $button_status = ' disabled="disabled"';
    echo "\t\t\t\t\t".'<tr><td class="tcl" colspan="'.$colspan.'">'.$lang['Empty forum'].'</td></tr>'."\n";
}

?>
</div>


<div class="row row-nav-fix">
    <div class="col-sm-6">
        <div class="btn-group btn-breadcrumb">
            <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-home"></span></a>
            <a class="btn btn-primary" href="viewforum.php?id=<?php echo $fid ?>"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></a>
            <a class="btn btn-primary" href="#"><?php echo $lang['Moderate'] ?></a>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="btn-group">
            <input type="submit" class="btn btn-primary" name="move_topics" value="<?php echo $lang['Move'] ?>"<?php echo $button_status ?> />
            <input type="submit" class="btn btn-primary" name="delete_topics" value="<?php echo $lang['Delete'] ?>"<?php echo $button_status ?> />
            <input type="submit" class="btn btn-primary" name="merge_topics" value="<?php echo $lang['Merge'] ?>"<?php echo $button_status ?> />
        </div>
        <div class="btn-group">
            <input type="submit" class="btn btn-primary" name="open" value="<?php echo $lang['Open'] ?>"<?php echo $button_status ?> />
            <input type="submit" class="btn btn-primary" name="close" value="<?php echo $lang['Close'] ?>"<?php echo $button_status ?> />
        </div>
        <ul class="pagination">
            <?php echo $paging_links ?>
        </ul>
    </div>
</div>
</form>

<?php

    require FORUM_ROOT.'footer.php';
