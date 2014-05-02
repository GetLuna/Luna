<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

                ++$post_count;
                $icon_type = 'icon';

                if (!$luna_user['is_guest'] && $cur_search['last_post'] > $luna_user['last_visit'] && (!isset($tracked_topics['topics'][$cur_search['tid']]) || $tracked_topics['topics'][$cur_search['tid']] < $cur_search['last_post']) && (!isset($tracked_topics['forums'][$cur_search['forum_id']]) || $tracked_topics['forums'][$cur_search['forum_id']] < $cur_search['last_post']))
                {
                    $item_status = 'inew';
                    $icon_type = 'icon icon-new';
                    $icon_text = $lang['New icon'];
                }
                else
                {
                    $item_status = '';
                    $icon_text = '<!-- -->';
                }

                if ($luna_config['o_censoring'] == '1')
                    $cur_search['message'] = censor_words($cur_search['message']);

                $message = parse_message($cur_search['message'], $cur_search['hide_smilies']);
                $pposter = luna_htmlspecialchars($cur_search['pposter']);

                if ($cur_search['poster_id'] > 1)
                {
                    if ($luna_user['g_view_users'] == '1')
                        $pposter = '<strong><a href="profile.php?id='.$cur_search['poster_id'].'">'.$pposter.'</a></strong>';
                    else
                        $pposter = '<strong>'.$pposter.'</strong>';
                }
?>

<div class="blockpost<?php echo ($post_count % 2 == 0) ? ' roweven' : ' rowodd' ?><?php if ($cur_search['pid'] == $cur_search['first_post_id']) echo ' firstpost' ?><?php if ($post_count == 1) echo ' blockpost1' ?><?php if ($item_status != '') echo ' '.$item_status ?>">
    <table class="table">
        <tr>
            <td class="col-lg-2 user-data">
                <?php echo $pposter ?><br />
                <?php if ($cur_search['pid'] == $cur_search['first_post_id']) : ?>
                    <?php echo $lang['Replies'].' '.forum_number_format($cur_search['num_replies']) ?>
                <?php endif; ?>
            </td>
            <td class="col-lg-10">
                <?php echo $message."\n" ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="postfooter" style="padding-bottom: 0;">
                <div class="btn-group btn-breadcrumb">
                    <?php echo $forum ?>
                    <a class="btn btn-primary" href="viewtopic.php?id=<?php echo $cur_search['tid'] ?>"><?php echo luna_htmlspecialchars($cur_search['subject']) ?></a>
                    <a class="btn btn-primary" href="viewtopic.php?pid=<?php echo $cur_search['pid'].'#p'.$cur_search['pid'] ?>"><?php if ($cur_search['pid'] != $cur_search['first_post_id']) echo $lang['Re'].' ' ?><?php echo format_time($cur_search['pposted']) ?></a>
                </div>
                <p class="pull-right">
                    <a class="btn btn-small btn-primary" href="viewtopic.php?id=<?php echo $cur_search['tid'] ?>"><?php echo $lang['Go to topic'] ?></a>
                    <a class="btn btn-small btn-primary" href="viewtopic.php?pid=<?php echo $cur_search['pid'].'#p'.$cur_search['pid'] ?>"><?php echo $lang['Go to post'] ?></a>
                </p>
            </td>
        </tr>
    </table>
</div>