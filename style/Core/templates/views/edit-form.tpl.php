<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<form id="edit" method="post" action="edit.php?id=<?php echo $id ?>&amp;action=edit" onsubmit="return process_form(this)">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Edit post'] ?></h3>
        </div>
        <fieldset class="postfield">
            <input type="hidden" name="form_sent" value="1" />
<?php if ($can_edit_subject): ?>
            <input class="longinput form-control" type="text" name="req_subject" maxlength="70" tabindex="<?php echo $cur_index++ ?>" value="<?php echo luna_htmlspecialchars(isset($_POST['req_subject']) ? $_POST['req_subject'] : $cur_post['subject']) ?>" />
<?php endif; ?>
            <textarea class="form-control" name="req_message" rows="20" tabindex="<?php echo $cur_index++ ?>"><?php echo luna_htmlspecialchars(isset($_POST['req_message']) ? $message : $cur_post['message']) ?></textarea>
        </fieldset>
        <div class="panel-footer">
            <div class="btn-group"><input type="submit" class="btn btn-primary" name="submit" value="<?php echo $lang['Submit'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="s" /> <input type="submit" class="btn btn-default" name="preview" value="<?php echo $lang['Preview'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="p" /></div> <a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a>
            <ul class="bblinks">
				<li><?php echo ($luna_config['p_message_bbcode'] == '1') 
					? '<a class="label label-success" href="help.php#bbcode" onclick="window.open(this.href); return false;">'.$lang['BBCode'].'</a>' 
					: '<span class="label label-danger">'.$lang['BBCode'].'</span>'; ?>
				</li>
				<li><?php echo ($luna_config['p_message_bbcode'] == '1' && $luna_config['p_message_img_tag'] == '1')
					? '<a class="label label-success" href="help.php#links" onclick="window.open(this.href); return false;">'.$lang['img tag'].'</a>' 
					: '<span class="label label-danger">'.$lang['img tag'].'</span>'; ?>
				</li>
				<li><?php echo ($luna_config['o_smilies'] == '1')
					? '<a class="label label-success" href="help.php#smilies" onclick="window.open(this.href); return false;">'.$lang['Smilies'].'</a>' 
					: '<span class="label label-danger">'.$lang['Smilies'].'</span>'; ?>
				</li>
            </ul>
        </div>
    </div>
<?php

$checkboxes = array();
if ($can_edit_subject && $is_admmod)
{
    if (isset($_POST['stick_topic']) || $cur_post['sticky'] == '1')
        $checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="stick_topic" value="1" checked="checked" tabindex="'.($cur_index++).'" /> '.$lang['Stick topic'].'</label></div>';
    else
        $checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="stick_topic" value="1" tabindex="'.($cur_index++).'" /> '.$lang['Stick topic'].'</label></div>';
}

if ($luna_config['o_smilies'] == '1')
{
    if (isset($_POST['hide_smilies']) || $cur_post['hide_smilies'] == '1')
        $checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="hide_smilies" value="1" checked="checked" tabindex="'.($cur_index++).'" /> '.$lang['Hide smilies'].'</label></div>';
    else
        $checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="hide_smilies" value="1" tabindex="'.($cur_index++).'" /> '.$lang['Hide smilies'].'</label></div>';
}

if ($is_admmod)
{
    if ((isset($_POST['form_sent']) && isset($_POST['silent'])) || !isset($_POST['form_sent']))
        $checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="silent" value="1" tabindex="'.($cur_index++).'" checked="checked" /> '.$lang['Silent edit'].'</label></div>';
    else
        $checkboxes[] = '<div class="checkbox"><label><input type="checkbox" name="silent" value="1" tabindex="'.($cur_index++).'" /> '.$lang['Silent edit'].'</label></div>';
}

if (!empty($checkboxes))
{

?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Options'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <?php echo implode("\n\t\t\t\t\t\t\t", $checkboxes)."\n" ?>
            </fieldset>
        </div>
    </div>
<?php

    }

?>
</form>

<?php

    require FORUM_ROOT.'footer.php';