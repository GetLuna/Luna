<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

    if ($luna_user['id'] != $id && (!$luna_user['is_admmod'] || ($luna_user['g_id'] != FORUM_ADMIN && ($luna_user['g_mod_edit_users'] == '0' || $user['g_id'] == FORUM_ADMIN || $user['g_moderator'] == '1'))))
        message($lang['Bad request'], false, '403 Forbidden');

    if ($luna_user['is_admmod'])
    {
        if ($luna_user['g_id'] == FORUM_ADMIN || $luna_user['g_mod_rename_users'] == '1')
            $username_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Username'].'</label><div class="col-sm-9"><input type="text" class="form-control" name="req_username" value="'.luna_htmlspecialchars($user['username']).'" maxlength="25" /></div></div>'."\n";
        else
            $username_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Username'].'</label><div class="col-sm-9">'.luna_htmlspecialchars($user['username']).'</div></div>'."\n";

        $email_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Email'].'</label><div class="col-sm-9"><div class="input-group"><input type="text" class="form-control" name="req_email" value="'.luna_htmlspecialchars($user['email']).'" maxlength="80" /><span class="input-group-btn"><a class="btn btn-primary" href="misc.php?email='.$id.'">'.$lang['Send email'].'</a></span></div></div></div>'."\n";
    }
    else
    {
        $username_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Username'].'</label><div class="col-sm-9"><input class="form-control" type="text"  value="'.luna_htmlspecialchars($user['username']).'" disabled="disabled" /></div></div>'."\n";

        if ($luna_config['o_regs_verify'] == '1')
            $email_field = '<p>'.sprintf($lang['Email info'], luna_htmlspecialchars($user['email']).' &middot; <a href="profile.php?action=change_email&amp;id='.$id.'">'.$lang['Change email'].'</a>').'</p>'."\n";
        else
            $email_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Email'].'</label><div class="col-sm-9"><input type="text" class="form-control" name="req_email" value="'.$user['email'].'" maxlength="80" /></div></div>'."\n";
    }

    if ($luna_user['g_set_title'] == '1')
        $title_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Title'].'<span class="help-block">'.$lang['Leave blank'].'</span></label><div class="col-sm-9"><input class="form-control" type="text" class="form-control" name="title" value="'.luna_htmlspecialchars($user['title']).'" maxlength="50" /></div></div>'."\n";

    if ($luna_config['o_avatars'] == '0' && $luna_config['o_signatures'] == '0')
        message($lang['Bad request'], false, '404 Not Found');

    $avatar_field = '<a class="btn btn-primary" href="profile.php?action=upload_avatar&amp;id='.$id.'">'.$lang['Change avatar'].'</a>';

    $user_avatar = generate_avatar_markup($id);
    if ($user_avatar)
        $avatar_field .= ' <a class="btn btn-primary" href="profile.php?action=delete_avatar&amp;id='.$id.'">'.$lang['Delete avatar'].'</a>';
    else
        $avatar_field = '<a class="btn btn-primary" href="profile.php?action=upload_avatar&amp;id='.$id.'">'.$lang['Upload avatar'].'</a>';

    if ($user['signature'] != '')
        $signature_preview = '<p>'.$lang['Sig preview'].'</p>'."\n\t\t\t\t\t\t\t".'<div class="postsignature postmsg">'."\n\t\t\t\t\t\t\t\t".'<hr />'."\n\t\t\t\t\t\t\t\t".$parsed_signature."\n\t\t\t\t\t\t\t".'</div>'."\n";
    else
        $signature_preview = '<p>'.$lang['No sig'].'</p>'."\n";

?>

<div class="col-sm-3 profile-nav">
<?php
    generate_profile_menu('personality');
?>
</div>
<div class="col-sm-9 col-profile">
<h2 class="profile-h2"><?php echo $lang['Section personality'] ?></h2>
<form id="profile2" class="form-horizontal" method="post" action="profile.php?section=personality&amp;id=<?php echo $id ?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Personal details legend'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="update" value="<?php echo $lang['Submit'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <input type="hidden" name="form_sent" value="1" />
                <?php echo $username_field ?>
                <?php if ($luna_user['id'] == $id || $luna_user['g_id'] == FORUM_ADMIN || ($user['g_moderator'] == '0' && $luna_user['g_mod_change_passwords'] == '1')): ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Password'] ?></label>
                    <div class="col-sm-9">
                        <a class="btn btn-primary" href="profile.php?action=change_pass&amp;id=<?php echo $id ?>"><?php echo $lang['Change pass'] ?></a>
                    </div>
                </div>
                <?php endif; ?>
                <?php echo $email_field ?>
                <hr />
                <input type="hidden" name="form_sent" value="1" />
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Realname'] ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="form[realname]" value="<?php echo luna_htmlspecialchars($user['realname']) ?>" maxlength="40" />
                    </div>
                </div>
                <?php if (isset($title_field)): ?>
                    <?php echo $title_field ?>
                <?php endif; ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Location'] ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="form[location]" value="<?php echo luna_htmlspecialchars($user['location']) ?>" maxlength="30" />
                    </div>
                </div>
                <hr />
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Website'] ?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="form[url]" value="<?php echo luna_htmlspecialchars($user['url']) ?>" maxlength="80" />
                    </div>
                </div>
                <hr />
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Jabber'] ?></label>
                    <div class="col-sm-9">
                        <input id="jabber" type="text" class="form-control" name="form[jabber]" value="<?php echo luna_htmlspecialchars($user['jabber']) ?>" maxlength="75" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['ICQ'] ?></label>
                    <div class="col-sm-9">
                        <input id="icq" type="text" class="form-control" name="form[icq]" value="<?php echo $user['icq'] ?>" maxlength="12" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['MSN'] ?></label>
                    <div class="col-sm-9">
                        <input id="msn" type="text" class="form-control" name="form[msn]" value="<?php echo luna_htmlspecialchars($user['msn']) ?>" maxlength="50" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['AOL'] ?></label>
                    <div class="col-sm-9">
                        <input id="aim" type="text" class="form-control" name="form[aim]" value="<?php echo luna_htmlspecialchars($user['aim']) ?>" maxlength="30" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Yahoo'] ?></label>
                    <div class="col-sm-9">
                        <input id="yahoo" type="text" class="form-control" name="form[yahoo]" value="<?php echo luna_htmlspecialchars($user['yahoo']) ?>" maxlength="30" />
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
<?php if ($luna_config['o_avatars'] == '1'): ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Avatar legend'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset id="profileavatar">
<?php if ($user_avatar): ?>
                <div class="useravatar"><?php echo $user_avatar ?></div>
<?php endif; ?>
                <p><?php echo $lang['Avatar info'] ?></p>
                <p><?php echo $avatar_field ?></p>
            </fieldset>
        </div>
    </div>
<?php endif; if ($luna_config['o_signatures'] == '1'): ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Signature legend'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="update" value="<?php echo $lang['Submit'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <p><?php echo $lang['Signature info'] ?></p>
                <label><?php printf($lang['Sig max size'], forum_number_format($luna_config['p_sig_length']), $luna_config['p_sig_lines']) ?></label>
                <textarea class="form-control" name="signature" rows="4"><?php echo luna_htmlspecialchars($user['signature']) ?></textarea>
                <ul class="bblinks">
                    <li><span class="label <?php echo ($luna_config['p_sig_bbcode'] == '1') ? "label-success" : "label-danger"; ?>"><?php echo $lang['BBCode'] ?></span></li>
                    <li><span class="label <?php echo ($luna_config['p_sig_bbcode'] == '1' && $luna_config['p_sig_img_tag'] == '1') ? "label-success" : "label-danger"; ?>"><?php echo $lang['img tag'] ?></span></li>
                    <li><span class="label <?php echo ($luna_config['o_smilies_sig'] == '1') ? "label-success" : "label-danger"; ?>"><?php echo $lang['Smilies'] ?></span></li>
                </ul>
                <?php echo $signature_preview ?>
            </fieldset>
        </div>
    </div>
<?php endif; ?>
</form>