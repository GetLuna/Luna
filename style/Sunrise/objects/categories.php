<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

$cur_category = 0;
$cat_count = 0;
$forum_count = 0;
while ($cur_forum = $db->fetch_assoc($result))
{
    $moderators = '';

    if ($cur_forum['cid'] != $cur_category) // A new category since last iteration?
    {
        if ($cur_category != 0)
            echo "\t\t".'</div>'."\n".'</div>'."\n\n";

        ++$cat_count;
        $forum_count = 0;

?>
<div class="row"><div class="col-xs-12"><h3><?php echo luna_htmlspecialchars($cur_forum['cat_name']) ?></h3></div></div>
<div class="row">
<?php

        $cur_category = $cur_forum['cid'];
    }

    ++$forum_count;
    $item_status = ($forum_count % 2 == 0) ? 'roweven' : 'rowodd';
    $forum_field_new = '';
    $icon_type = 'icon';

    // Are there new posts since our last visit?
    if (isset($new_topics[$cur_forum['fid']]))
    {
        $item_status .= ' inew';
        $forum_field_new = '<span class="newtext">[ <a href="search.php?action=show_new&amp;fid='.$cur_forum['fid'].'">'.$lang['New posts'].'</a> ]</span>';
        $icon_type = 'icon icon-new';
    }

	$forum_field = '<a href="viewforum.php?id='.$cur_forum['fid'].'">'.luna_htmlspecialchars($cur_forum['forum_name']).'</a>'.(!empty($forum_field_new) ? ' '.$forum_field_new : '');
	$num_topics = $cur_forum['num_topics'];
	$num_posts = $cur_forum['num_posts'];

    if ($cur_forum['forum_desc'] != '')
        $forum_field .= "\n\t\t\t\t\t\t\t\t".'<div class="forumdesc hidden-xs">'.$cur_forum['forum_desc'].'</div>';

    // If there is a last_post/last_poster
    if ($cur_forum['last_post'] != '')
    {
        if (luna_strlen($cur_forum['last_topic']) > 43)
            $cur_forum['last_topic'] = utf8_substr($cur_forum['last_topic'], 0, 40).'...';

			if ($luna_user['g_view_users'] == '1' && $cur_forum['last_poster_id'] > '1')
                $last_post = '<a href="viewtopic.php?pid='.$cur_forum['last_post_id'].'#p'.$cur_forum['last_post_id'].'">'.luna_htmlspecialchars($cur_forum['last_topic']).'</a><br /><span class="bytime  hidden-xs">'.format_time($cur_forum['last_post']).' </span><span class="byuser">'.$lang['by'].' <a href="profile.php?id='.$cur_forum['last_poster_id'].'">'.luna_htmlspecialchars($cur_forum['last_poster']).'</a></span>';
            else
                $last_post = '<a href="viewtopic.php?pid='.$cur_forum['last_post_id'].'#p'.$cur_forum['last_post_id'].'">'.luna_htmlspecialchars($cur_forum['last_topic']).'</a><br /><span class="bytime  hidden-xs">'.format_time($cur_forum['last_post']).' </span><span class="byuser">'.$lang['by'].' '.luna_htmlspecialchars($cur_forum['last_poster']).'</span>';
    }
    else
        $last_post = $lang['Never'];

    if ($cur_forum['moderators'] != '' && $luna_config['o_moderated_by'] == '1')
    {
        $mods_array = unserialize($cur_forum['moderators']);
        $moderators = array();

        foreach ($mods_array as $mod_username => $mod_id)
        {
            if ($luna_user['g_view_users'] == '1')
                $moderators[] = '<a href="profile.php?id='.$mod_id.'">'.luna_htmlspecialchars($mod_username).'</a>';
            else
                $moderators[] = luna_htmlspecialchars($mod_username);
        }

        $moderators = "\t\t\t\t\t\t\t\t".'<p class="modlist">'.$lang['Moderated by'].' '.implode(', ', $moderators).'</p>'."\n";
    }

    if (forum_number_format($num_topics) == '1') {
        $topics_label = $lang['topic'];
    } else {
        $topics_label = $lang['topics'];
    }

    if (forum_number_format($num_topics) == '1') {
        $posts_label = $lang['post'];
    } else {
        $posts_label = $lang['posts'];
    }


?>
<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></h3>
		</div>
		<div class="panel-body">
			<?php echo $cur_forum['forum_desc'] ?>
			<hr />
			<?php echo $last_post."\n".$moderators ?>
		</div>
		<div class="panel-footer">
			<b><?php echo forum_number_format($num_topics) ?></b> <?php echo $topics_label ?> and <b><?php echo forum_number_format($num_posts) ?></b> <?php echo $posts_label ?>
		</div>
	</div>
</div>
<?php

}

// Did we output any categories and forums?
if ($cur_category > 0)
    echo "\t\t\t".'</div>'."\n\n";
else
    echo '<div id="idx0"><p>'.$lang['Empty board'].'</p></div>';
