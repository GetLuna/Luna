<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="row row-nav-fix">
    <div class="col-sm-6">
        <div class="btn-group btn-breadcrumb">
            <a class="btn btn-primary" href="index.php"><span class="fa fa-home"></span></a>
            <a class="btn btn-primary" href="viewforum.php?id=<?php echo $fid ?>"><?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
            <a class="btn btn-primary" href="viewtopic.php?id=<?php echo $tid ?>"><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></a>
            <a class="btn btn-primary" href="#"><?php echo $lang['Moderate'] ?></a>
        </div>
    </div>
    <div class="col-sm-6">
		<?php echo $paging_links ?>
    </div>
</div>

<form method="post" action="moderate.php?fid=<?php echo $fid ?>&amp;tid=<?php echo $tid ?>">
<?php

    require FORUM_ROOT.'include/parser.php';

    $post_count = 0; // Keep track of post numbers

    // Retrieve a list of post IDs, LIMIT is (really) expensive so we only fetch the IDs here then later fetch the remaining data
    $result = $db->query('SELECT id FROM '.$db->prefix.'posts WHERE topic_id='.$tid.' ORDER BY id LIMIT '.$start_from.','.$luna_user['disp_posts']) or error('Unable to fetch post IDs', __FILE__, __LINE__, $db->error());

    $post_ids = array();
    for ($i = 0;$cur_post_id = $db->result($result, $i);$i++)
        $post_ids[] = $cur_post_id;

    // Retrieve the posts (and their respective poster)
    $result = $db->query('SELECT u.title, u.num_posts, g.g_id, g.g_user_title, p.id, p.poster, p.poster_id, p.message, p.hide_smilies, p.posted, p.edited, p.edited_by, o.user_id AS is_online FROM '.$db->prefix.'posts AS p INNER JOIN '.$db->prefix.'users AS u ON u.id=p.poster_id INNER JOIN '.$db->prefix.'groups AS g ON g.g_id=u.group_id LEFT JOIN '.$db->prefix.'online AS o ON (o.user_id=u.id AND o.user_id!=1 AND o.idle=0) WHERE p.id IN ('.implode(',', $post_ids).') ORDER BY p.id', true) or error('Unable to fetch post info', __FILE__, __LINE__, $db->error());

    while ($cur_post = $db->fetch_assoc($result)) {
        $post_count++;

        // If the poster is a registered user
        if ($cur_post['poster_id'] > 1) {
            if ($luna_user['g_view_users'] == '1')
                $poster = '<a href="me.php?id='.$cur_post['poster_id'].'">'.luna_htmlspecialchars($cur_post['poster']).'</a>';
            else
                $poster = luna_htmlspecialchars($cur_post['poster']);

            // get_title() requires that an element 'username' be present in the array
            $cur_post['username'] = $cur_post['poster'];
            $user_title = get_title($cur_post);

            if ($luna_config['o_censoring'] == '1')
                $user_title = censor_words($user_title);
        }
        // If the poster is a guest (or a user that has been deleted)
        else {
            $poster = luna_htmlspecialchars($cur_post['poster']);
            $user_title = $lang['Guest'];
        }

        // Format the online indicator, those are ment as CSS classes
        $is_online = ($cur_post['is_online'] == $cur_post['poster_id']) ? 'is-online' : 'is-offline';

        // Perform the main parsing of the message (BBCode, smilies, censor words etc)
        $cur_post['message'] = parse_message($cur_post['message']);

?>
<div id="p<?php echo $cur_post['id'] ?>" class="blockpost<?php if($cur_post['id'] == $cur_topic['first_post_id']) echo ' firstpost' ?><?php echo ($post_count % 2 == 0) ? ' roweven' : ' rowodd' ?><?php if ($post_count == 1) echo ' blockpost1' ?>">
    <table class="table postview">
        <tr>
            <td class="col-lg-2 user-data">
                <dd class="usertitle <?php echo $is_online; ?>"><strong><?php echo $poster ?></strong></dd><?php echo $user_title ?>
            </td>
            <td class="col-lg-10 post-content">
                <span class="time-nr pull-right">#<?php echo ($start_from + $post_count) ?> &middot; <a href="viewtopic.php?pid=<?php echo $cur_post['id'].'#p'.$cur_post['id'] ?>"><?php echo format_time($cur_post['posted']) ?></a></span>
                <div class="postmsg">
                    <?php echo $cur_post['message']."\n" ?>
                    <?php if ($cur_post['edited'] != '') echo "\t\t\t\t\t\t".'<p class="postedit"><em>'.$lang['Last edit'].' '.luna_htmlspecialchars($cur_post['edited_by']).' ('.format_time($cur_post['edited']).')</em></p>'."\n"; ?>
                </div>
            </td>
        </tr>
        <?php if (!$luna_user['is_guest']) { ?>
        <tr>
            <td colspan="2" class="postfooter" style="padding-bottom: 0;">
                <?php echo ($cur_post['id'] != $cur_topic['first_post_id']) ? '<div class="checkbox pull-right" style="margin-top: 0;"><label><input type="checkbox" name="posts['.$cur_post['id'].']" value="1" /> '.$lang['Select'].'</label></div>' : '<p>'.$lang['Cannot select first'].'</p>' ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php

    }

?>

<div class="row row-nav-fix">
    <div class="col-sm-6">
        <div class="btn-group btn-breadcrumb">
            <a class="btn btn-primary" href="index.php"><span class="fa fa-home"></span></a>
            <a class="btn btn-primary" href="viewforum.php?id=<?php echo $fid ?>"><?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
            <a class="btn btn-primary" href="viewtopic.php?id=<?php echo $tid ?>"><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></a>
            <a class="btn btn-primary" href="#"><?php echo $lang['Moderate'] ?></a>
        </div>
    </div>
    <div class="col-sm-6">
		<?php echo $paging_links ?>
		<div class="btn-group"><input type="submit" class="btn btn-primary" name="split_posts" value="<?php echo $lang['Split'] ?>"<?php echo $button_status ?> /><input type="submit" class="btn btn-primary" name="delete_posts" value="<?php echo $lang['Delete'] ?>"<?php echo $button_status ?> /></div>
    </div>
</div>
</form>

<?php

    require load_page('footer.php');