<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

if ($luna_user['g_view_users'] == '1')
    $stats['newest_user'] = '<a href="profile.php?id='.$stats['last_user']['id'].'">'.luna_htmlspecialchars($stats['last_user']['username']).'</a>';
else
    $stats['newest_user'] = luna_htmlspecialchars($stats['last_user']['username']);


if ($luna_config['o_show_index_stats'] == 1) {
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Board stats'] ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-2 col-sm-4 col-xs-6"><span><?php printf($lang['No of users'], '<strong>'.forum_number_format($stats['total_users']).'</strong>') ?></span></div>
            <div class="col-md-2 col-sm-4 col-xs-6"><span><?php printf($lang['No of topics'], '<strong>'.forum_number_format($stats['total_topics']).'</strong>') ?></span></div>
            <div class="col-md-2 col-sm-4 col-xs-6"><span><?php printf($lang['No of post'], '<strong>'.forum_number_format($stats['total_posts']).'</strong>') ?></span></div>
            <div class="col-md-2 col-sm-4 col-xs-6"><span><?php printf($lang['Newest user'], $stats['newest_user']) ?></span></div>
<?php

if ($luna_config['o_users_online'] == '1')
{
    // Fetch users online info and generate strings for output
    $num_guests = 0;
    $users = array();
    $result = $db->query('SELECT user_id, ident FROM '.$db->prefix.'online WHERE idle=0 ORDER BY ident', true) or error('Unable to fetch online list', __FILE__, __LINE__, $db->error());

    while ($luna_user_online = $db->fetch_assoc($result))
    {
        if ($luna_user_online['user_id'] > 1)
        {
            if ($luna_user['g_view_users'] == '1')
                $users[] = "\n\t\t\t\t".'<a href="profile.php?id='.$luna_user_online['user_id'].'">'.luna_htmlspecialchars($luna_user_online['ident']).'</a>';
            else
                $users[] = "\n\t\t\t\t".luna_htmlspecialchars($luna_user_online['ident']);
        }
        else
            ++$num_guests;
    }

    $num_users = count($users);
    echo "\t\t\t\t".'<div class="col-md-2 col-sm-4 col-xs-6"><span>'.sprintf($lang['Users online'], '<strong>'.forum_number_format($num_users).'</strong>').'</span></div>'."\n\t\t\t\t".'<div class="col-md-2 col-sm-4 col-xs-6"><span>'.sprintf($lang['Guests online'], '<strong>'.forum_number_format($num_guests).'</strong>').'</span></div>'."\n\t\t\t\n";
    ?>
        </div>
        <div class="row">
    <?php
    if ($num_users > 0)
        echo "\t\t\t\n\t\t\t\t".'<span class="users-online"><strong>'.$lang['Online'].' </strong>'."\t\t\t\t".implode(', ', $users)."\n\t\t\t\n".'</span>';
}

?>
        </div>
    </div>
</div>
<?php
}

$footer_style = 'index';
require FORUM_ROOT.'footer.php';