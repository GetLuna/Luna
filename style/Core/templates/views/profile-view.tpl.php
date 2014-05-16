<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

    $user_personality = array();

    $user_username = luna_htmlspecialchars($user['username']);
    $user_usertitle = get_title($user);
    $avatar_field = generate_avatar_markup($id);

    $user_title_field = get_title($user);
    $user_personality[] = '<b>'.$lang['Title'].':</b> '.(($luna_config['o_censoring'] == '1') ? censor_words($user_title_field) : $user_title_field);

    $user_personality[] = '<b>'.$lang['Posts table'].':</b> '.$posts_field = forum_number_format($user['num_posts']);

    if ($user['num_posts'] > 0)
        $user_personality[] = '<b>'.$lang['Last post'].':</b> '.$last_post;

    $user_activity[] = '<b>'.$lang['Registered table'].':</b> '.format_time($user['registered'], true);

    $user_personality[] = '<b>'.$lang['Registered'].':</b> '.format_time($user['registered'], true);

    $user_personality[] = '<b>'.$lang['Last visit info'].':</b> '.format_time($user['last_visit'], true);

    if ($user['realname'] != '')
        $user_personality[] = '<b>'.$lang['Realname'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['realname']) : $user['realname']);

    if ($user['location'] != '')
        $user_personality[] = '<b>'.$lang['Location'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['location']) : $user['location']);

    $posts_field = '';
    if ($luna_user['g_search'] == '1')
    {
        $quick_searches = array();
        if ($user['num_posts'] > 0)
        {
            $quick_searches[] = '<a class="btn btn-primary btn-sm" href="search.php?action=show_user_topics&amp;user_id='.$id.'">'.$lang['Show topics'].'</a>';
            $quick_searches[] = '<a class="btn btn-primary btn-sm" href="search.php?action=show_user_posts&amp;user_id='.$id.'">'.$lang['Show posts'].'</a>';
        }
        if ($luna_user['is_admmod'] && $luna_config['o_topic_subscriptions'] == '1')
            $quick_searches[] = '<a class="btn btn-primary btn-sm" href="search.php?action=show_subscriptions&amp;user_id='.$id.'">'.$lang['Show subscriptions'].'</a>';

        if (!empty($quick_searches))
            $posts_field .= implode('', $quick_searches);
    }

    if ($posts_field != '')
        $user_personality[] = '<br /><div class="btn-group">'.$posts_field.'</div>';

    if ($user['url'] != '') {
        $user_website = '<a class="btn btn-default btn-block" href="'.luna_htmlspecialchars($user['url']).'" rel="nofollow"><span class="glyphicon glyphicon-globe"></span> '.$lang['Website'].'</a>';
    } else {
        $user_website = '';
    }

    if ($user['email_setting'] == '0' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
        $email_field = '<a class="btn btn-default btn-block" href="mailto:'.luna_htmlspecialchars($user['email']).'"><span class="glyphicon glyphicon-send"></span> '.luna_htmlspecialchars($user['email']).'</a>';
    else if ($user['email_setting'] == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1')
        $email_field = '<a class="btn btn-default btn-block" href="misc.php?email='.$id.'"><span class="glyphicon glyphicon-send"></span> '.$lang['Send email'].'</a>';
    else
        $email_field = '';
    if ($email_field != '')
    {
        $email_field;
    }

    $user_messaging = array();

    if ($user['jabber'] != '')
        $user_messaging[] = '<b>'.$lang['Jabber'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['jabber']) : $user['jabber']);

    if ($user['icq'] != '')
        $user_messaging[] = '<b>'.$lang['ICQ'].':</b> '. $user['icq'];

    if ($user['msn'] != '')
        $user_messaging[] = '<b>'.$lang['MSN'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['msn']) : $user['msn']);

    if ($user['aim'] != '')
        $user_messaging[] = '<b>'.$lang['AOL'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['aim']) : $user['aim']);

    if ($user['yahoo'] != '')
        $user_messaging[] = '<b>'.$lang['Yahoo'].':</b> '.luna_htmlspecialchars(($luna_config['o_censoring'] == '1') ? censor_words($user['yahoo']) : $user['yahoo']);

    if (($luna_config['o_signatures'] == '1') && (isset($parsed_signature)))
        $user_signature = $parsed_signature;

    $user_activity = array();

?>

<div class="col-sm-3 profile-nav">
<?php
    generate_profile_menu('view');

    echo $email_field;
    echo $user_website;
?>
</div>
<div class="col-sm-9 col-profile">
    <div class="profile-card">
        <div class="profile-card-head">
            <div class="user-avatar thumbnail">
                <?php echo $avatar_field; ?>
            </div>
            <h2><?php echo $user_username; ?></h2>
            <h3><?php echo $user_usertitle; ?></h3>
        </div>
        <div class="profile-card-body">
            <?php echo implode("\n\t\t\t\t\t\t\t".'<br />', $user_personality)."\n" ?>
        </div>
    </div>
<?php if (!empty($user_messaging)): ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Contact']; ?></h3>
        </div>
        <div class="panel-body">
            <p><?php echo implode("\n\t\t\t\t\t\t\t".'<br />', $user_messaging)."\n" ?></p>
        </div>
    </div>
<?php
    endif;

    if ($luna_config['o_signatures'] == '1')
    {
        if (isset($parsed_signature))
        {
?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Signature']; ?></h3>
        </div>
        <div class="panel-body">
            <?php echo $user_signature ?>
        </div>
    </div>
<?php
        }
    }
?>
</div>