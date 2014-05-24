<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

$cur_index = 1;

?>

<?php echo $form."\n" ?>
    <div class="panel panel-border panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $action ?></h3>
        </div>
        <fieldset class="postfield">
            <input type="hidden" name="form_sent" value="1" />
<?php

if ($luna_user['is_guest'])
{
    $email_label = ($luna_config['p_force_guest_email'] == '1') ? '<strong>'.$lang['Email'].'</strong>' : $lang['Email'];
    $email_form_name = ($luna_config['p_force_guest_email'] == '1') ? 'req_email' : 'email';

?>
            <label class="required hidden"><?php echo $lang['Guest name'] ?></label><input class="form-control" type="text" placeholder="<?php echo $lang['Guest name'] ?>" name="req_username" value="<?php if (isset($_POST['req_username'])) echo luna_htmlspecialchars($username); ?>" maxlength="25" tabindex="<?php echo $cur_index++ ?>" />
            <label class="conl<?php echo ($luna_config['p_force_guest_email'] == '1') ? ' required' : '' ?> hidden"><?php echo $email_label ?></label><input class="form-control" type="text" placeholder="<?php echo $lang['Email'] ?>" name="<?php echo $email_form_name ?>" value="<?php if (isset($_POST[$email_form_name])) echo luna_htmlspecialchars($email); ?>" maxlength="80" tabindex="<?php echo $cur_index++ ?>" />
<?php

}

if ($fid): ?>
            <label class="required hidden"><?php echo $lang['Subject'] ?></label><input class="longinput form-control" placeholder="<?php echo $lang['Subject'] ?>" type="text" name="req_subject" value="<?php if (isset($_POST['req_subject'])) echo luna_htmlspecialchars($subject); ?>" maxlength="70" tabindex="<?php echo $cur_index++ ?>" />
<?php endif; ?>
            <textarea class="form-control tinymce"  placeholder="Start typing..." name="req_message" rows="20" tabindex="<?php echo $cur_index++ ?>"><?php echo isset($_POST['req_message']) ? luna_htmlspecialchars($orig_message) : (isset($quote) ? $quote : ''); ?></textarea>
        </fieldset>
        <div class="panel-footer">
            <div class="btn-group"><input class="btn btn-primary" onclick="tinyMCE.triggerSave(false);" type="submit" name="submit" value="<?php echo $lang['Submit'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="s" /><input class="btn btn-default" onclick="tinyMCE.triggerSave(false);" type="submit" name="preview" value="<?php echo $lang['Preview'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="p" /> <a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a></div>
            <ul class="bblinks">
                <li><span class="label <?php echo ($luna_config['p_message_bbcode'] == '1') ? "label-success" : "label-danger"; ?>"><?php echo $lang['BBCode'] ?></span></li>
                <li><span class="label <?php echo ($luna_config['p_message_bbcode'] == '1' && $luna_config['p_message_img_tag'] == '1') ? "label-success" : "label-danger"; ?>"><?php echo $lang['img tag'] ?></a></li>
                <li><span class="label <?php echo ($luna_config['o_smilies'] == '1') ? "label-success" : "label-danger"; ?>"><?php echo $lang['Smilies'] ?></span></li>
            </ul>
        </div>
    </div>
<?php

$checkboxes = array();
if ($fid && $is_admmod)
    $checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="stick_topic" value="1" tabindex="'.($cur_index++).'"'.(isset($_POST['stick_topic']) ? ' checked="checked"' : '').' /> '.$lang['Stick topic'].'</label></div>';

if (!$luna_user['is_guest'])
{
    if ($luna_config['o_smilies'] == '1')
        $checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="hide_smilies" value="1" tabindex="'.($cur_index++).'"'.(isset($_POST['hide_smilies']) ? ' checked="checked"' : '').' /> '.$lang['Hide smilies'].'</label></div>';

    if ($luna_config['o_topic_subscriptions'] == '1')
    {
        $subscr_checked = false;

        // If it's a preview
        if (isset($_POST['preview']))
            $subscr_checked = isset($_POST['subscribe']) ? true : false;
        // If auto subscribed
        else if ($luna_user['auto_notify'])
            $subscr_checked = true;
        // If already subscribed to the topic
        else if ($is_subscribed)
            $subscr_checked = true;

        $checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="subscribe" value="1" tabindex="'.($cur_index++).'"'.($subscr_checked ? ' checked="checked"' : '').' /> '.($is_subscribed ? $lang['Stay subscribed'] : $lang['Subscribe topic']).'</label></div>';
    }
}
else if ($luna_config['o_smilies'] == '1')
    $checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="hide_smilies" value="1" tabindex="'.($cur_index++).'"'.(isset($_POST['hide_smilies']) ? ' checked="checked"' : '').' /> '.$lang['Hide smilies'].'</label></div>';

if (!empty($checkboxes))
{

?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Options'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <?php echo implode($checkboxes) ?>
            </fieldset>
        </div>
    </div>
</form>
<?php

}