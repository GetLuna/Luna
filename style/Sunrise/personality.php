<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

    if ($luna_user['id'] != $id && (!$luna_user['is_admmod'] || ($luna_user['g_id'] != FORUM_ADMIN && ($luna_user['g_mod_edit_users'] == '0' || $user['g_id'] == FORUM_ADMIN || $user['g_moderator'] == '1'))))
        message($lang['Bad request'], false, '403 Forbidden');

    if ($luna_user['is_admmod']) {
        if ($luna_user['g_id'] == FORUM_ADMIN || $luna_user['g_mod_rename_users'] == '1')
            $username_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Username'].'</label><div class="col-sm-9"><input type="text" class="form-control" name="req_username" value="'.luna_htmlspecialchars($user['username']).'" maxlength="25" /></div></div>'."\n";
        else
            $username_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Username'].'</label><div class="col-sm-9">'.luna_htmlspecialchars($user['username']).'</div></div>'."\n";

        $email_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Email'].'</label><div class="col-sm-9"><div class="input-group"><input type="text" class="form-control" name="req_email" value="'.luna_htmlspecialchars($user['email']).'" maxlength="80" /><span class="input-group-btn"><a class="btn btn-primary" href="misc.php?email='.$id.'">'.$lang['Send email'].'</a></span></div></div></div>'."\n";
    } else {
        $username_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Username'].'</label><div class="col-sm-9"><input class="form-control" type="text"  value="'.luna_htmlspecialchars($user['username']).'" disabled="disabled" /></div></div>'."\n";

        if ($luna_config['o_regs_verify'] == '1')
            $email_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Email'].'</label><div class="col-sm-9"><div class="input-group"><input type="text" class="form-control" name="req_email" value="'.luna_htmlspecialchars($user['email']).'" maxlength="80" disabled /><span class="input-group-btn"><a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newmail">'.$lang['Change email'].'</a></span></div></div></div>';
        else
            $email_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Email'].'</label><div class="col-sm-9"><input type="text" class="form-control" name="req_email" value="'.$user['email'].'" maxlength="80" /></div></div>'."\n";
    }

    if ($luna_user['g_set_title'] == '1')
        $title_field = '<div class="form-group"><label class="col-sm-3 control-label">'.$lang['Title'].'<span class="help-block">'.$lang['Leave blank'].'</span></label><div class="col-sm-9"><input class="form-control" type="text" class="form-control" name="title" value="'.luna_htmlspecialchars($user['title']).'" maxlength="50" /></div></div>'."\n";

    if ($luna_config['o_avatars'] == '0' && $luna_config['o_signatures'] == '0')
        message($lang['Bad request'], false, '404 Not Found');

    $avatar_field = '<a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newavatar">'.$lang['Change avatar'].'</a>';

    $user_avatar = generate_avatar_markup($id);
    $avatar_set = check_avatar($id);
    if ($user_avatar && $avatar_set)
        $avatar_field .= ' <a class="btn btn-primary" href="me.php?action=delete_avatar&amp;id='.$id.'">'.$lang['Delete avatar'].'</a>';
    else
        $avatar_field = '<a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newavatar">'.$lang['Upload avatar'].'</a>';

    if ($user['signature'] != '')
        $signature_preview = $parsed_signature;
    else
        $signature_preview = '<p>'.$lang['No sig'].'</p>';

?>

<div class="col-sm-3 profile-nav">
<?php
    generate_me_menu('personality');
?>
</div>
<div class="col-sm-9 col-profile">
<h2 class="profile-h2"><?php echo $lang['Section personality'] ?></h2>
<form id="profile2" class="form-horizontal" method="post" action="me.php?section=personality&amp;id=<?php echo $id ?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Personal details legend'] ?><span class="pull-right"><input class="btn btn-primary" type="submit" name="update" value="<?php echo $lang['Save'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <input type="hidden" name="form_sent" value="1" />
                <?php echo $username_field ?>
                <?php if ($luna_user['id'] == $id || $luna_user['g_id'] == FORUM_ADMIN || ($user['g_moderator'] == '0' && $luna_user['g_mod_change_passwords'] == '1')): ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Password'] ?></label>
                    <div class="col-sm-9">
                        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newpass"><?php echo $lang['Change pass'] ?></a>
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
                    <label class="col-sm-3 control-label"><?php echo $lang['Microsoft'] ?></label>
                    <div class="col-sm-9">
                        <input id="microsoft" type="text" class="form-control" name="form[msn]" value="<?php echo luna_htmlspecialchars($user['msn']) ?>" maxlength="50" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Facebook'] ?></label>
                    <div class="col-sm-9">
                        <input id="facebook" type="text" class="form-control" name="form[facebook]" value="<?php echo luna_htmlspecialchars($user['facebook']) ?>" maxlength="50" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Twitter'] ?></label>
                    <div class="col-sm-9">
                        <input id="twitter" type="text" class="form-control" name="form[twitter]" value="<?php echo luna_htmlspecialchars($user['twitter']) ?>" maxlength="50" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Google+'] ?></label>
                    <div class="col-sm-9">
                        <input id="google" type="text" class="form-control" name="form[google]" value="<?php echo luna_htmlspecialchars($user['google']) ?>" maxlength="50" />
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Profile settings<span class="pull-right"><input class="btn btn-primary" type="submit" name="update" value="<?php echo $lang['Save'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
			<div class="form-group">
				<label class="col-sm-3 control-label">Profile color</label>
				<div class="col-sm-9">
					<div class="btn-group accent-group" data-toggle="buttons">
						<label class="btn btn-primary color-accent accent-blue<?php if ($luna_user['color'] == '#33b5e5') echo ' active' ?>">
							<input type="radio" name="form[color]" id="blue" value="#33b5e5"<?php if ($luna_user['color'] == '#33b5e5') echo ' checked' ?>>
						</label>
						<label class="btn btn-primary color-accent accent-purple<?php if ($luna_user['color'] == '#c58be2') echo ' active' ?>">
							<input type="radio" name="form[color]" id="purple" value="#c58be2"<?php if ($luna_user['color'] == '#c58be2') echo ' checked' ?>>
						</label>
						<label class="btn btn-primary color-accent accent-green<?php if ($luna_user['color'] == '#99cc00') echo ' active' ?>">
							<input type="radio" name="form[color]" id="green" value="#99cc00"<?php if ($luna_user['color'] == '#99cc00') echo ' checked' ?>>
						</label>
						<label class="btn btn-primary color-accent accent-yellow<?php if ($luna_user['color'] == '#ffcd21') echo ' active' ?>">
							<input type="radio" name="form[color]" id="yellow" value="#ffcd21"<?php if ($luna_user['color'] == '#ffcd21') echo ' checked' ?>>
						</label>
						<label class="btn btn-primary color-accent accent-red<?php if ($luna_user['color'] == '#ff4444') echo ' active' ?>">
							<input type="radio" name="form[color]" id="red" value="#ff4444"<?php if ($luna_user['color'] == '#ff4444') echo ' checked' ?>>
						</label>
						<label class="btn btn-primary color-accent accent-luna<?php if ($luna_user['color'] == '#0d4382') echo ' active' ?>">
							<input type="radio" name="form[color]" id="red" value="#0d4382"<?php if ($luna_user['color'] == '#0d4382') echo ' checked' ?>>
						</label>
						<label class="btn btn-primary color-accent accent-grey<?php if ($luna_user['color'] == '#cccccc') echo ' active' ?>">
							<input type="radio" name="form[color]" id="red" value="#cccccc"<?php if ($luna_user['color'] == '#cccccc') echo ' checked' ?>>
						</label>
					</div>
				</div>
			</div>
<?php if ($luna_config['o_avatars'] == '1'): ?>
			<hr />
            <fieldset id="profileavatar">
                <div class="form-group">
                    <label class="col-sm-3 control-label">Avatar<span class="help-block"><?php echo $lang['Avatar info'] ?></span></label>
                    <div class="col-sm-9">
						<?php if ($user_avatar): ?><div class="useravatar"><?php echo $user_avatar ?></div><?php endif; ?>
						<?php echo $avatar_field ?>
                    </div>
                </div>
            </fieldset>
<?php endif; if ($luna_config['o_signatures'] == '1'): ?>
			<hr />
            <fieldset>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Signature<span class="help-block"><?php echo $lang['Signature info'] ?></span></label>
                    <div class="col-sm-9">
						<textarea class="form-control" name="signature" rows="4"><?php echo luna_htmlspecialchars($user['signature']) ?></textarea>
						<span class="help-block"><?php printf($lang['Sig max size'], forum_number_format($luna_config['p_sig_length']), $luna_config['p_sig_lines']) ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Sig preview'] ?></label>
                    <div class="col-sm-9">
						<?php echo $signature_preview ?>
                    </div>
                </div>
            </fieldset>
<?php endif; ?>
        </div>
    </div>
</form>