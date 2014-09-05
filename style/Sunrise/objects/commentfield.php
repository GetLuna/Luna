<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>


<?php if ($can_edit_subject && FORUM_ACTIVE_PAGE == 'edit') { ?>
<form id="commentform" method="post" action="edit.php?id=<?php echo $id ?>&amp;action=edit" onsubmit="return process_form(this)">
	<input class="form-control power-control" type="text" name="req_subject" maxlength="70" tabindex="<?php echo $cur_index++ ?>" value="<?php echo luna_htmlspecialchars(isset($_POST['req_subject']) ? $_POST['req_subject'] : $cur_post['subject']) ?>" />
<?php } else { ?>
<form id="commentform" method="post" action="post.php?tid=<?php echo $id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
<?php } ?>

	<div class="row topic">
		<div class="col-md-12">
				<div class="panel panel-default">
				<fieldset class="commentfield<?php if (FORUM_ACTIVE_PAGE == 'edit') { ?> commentmax<?php } ?>">
					<input type="hidden" name="form_sent" value="1" />
<?php if ($luna_config['o_topic_subscriptions'] == '1' && ($luna_user['auto_notify'] == '1' || $cur_topic['is_subscribed'])): ?>                        <input type="hidden" name="subscribe" value="1" />
<?php endif; ?>
<?php

if ($luna_user['is_guest'])
{
    $email_label = ($luna_config['p_force_guest_email'] == '1') ? '<strong>'.$lang['Email'].' <span>'.$lang['Required'].'</span></strong>' : $lang['Email'];
    $email_form_name = ($luna_config['p_force_guest_email'] == '1') ? 'req_email' : 'email';

?>
					<label class="conl required hidden"><?php echo $lang['Guest name'] ?></label><input type="text" placeholder="<?php echo $lang['Guest name'] ?>" class="form-control" name="req_username" value="<?php if (isset($_POST['req_username'])) echo luna_htmlspecialchars($username); ?>" maxlength="25" tabindex="<?php echo $cur_index++ ?>" />
					<label class="conl<?php echo ($luna_config['p_force_guest_email'] == '1') ? ' required' : '' ?> hidden"><?php echo $email_label ?></label><input type="text" placeholder="<?php echo $lang['Email'] ?>" class="form-control" name="<?php echo $email_form_name ?>" value="<?php if (isset($_POST[$email_form_name])) echo luna_htmlspecialchars($email); ?>" maxlength="80" tabindex="<?php echo $cur_index++ ?>" />
<?php

    echo "\t\t\t\t\t\t".'<label class="required hidden"><strong>'.$lang['Message'].' <span>'.$lang['Required'].'</span></strong></label>';
}

?>
					<div class="btn-toolbar textarea-toolbar">
						<div class="btn-group">
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('b');" title="<?php echo $lang['Bold']; ?>"><span class="fa fa-bold fa-fw"></span></a>
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('u');" title="<?php echo $lang['Underline']; ?>"><span class="fa fa-underline fa-fw"></span></a>
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('i');" title="<?php echo $lang['Italic']; ?>"><span class="fa fa-italic fa-fw"></span></a>
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('s');" title="<?php echo $lang['Strike']; ?>"><span class="fa fa-strikethrough fa-fw"></span></a>
						</div>
						<div class="btn-group">
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('h');" title="<?php echo $lang['Heading']; ?>"><span class="fa fa-header fa-fw"></span></a>
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('sub');" title="<?php echo $lang['Subscript']; ?>"><span class="fa fa-subscript fa-fw"></span></a>
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('sup');" title="<?php echo $lang['Superscript']; ?>"><span class="fa fa-superscript fa-fw"></span></a>
						</div>
						<div class="btn-group hidden-xs">
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('quote');" title="<?php echo $lang['Quote']; ?>"><span class="fa fa-quote-left fa-fw"></span></a>
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('code');" title="<?php echo $lang['Code']; ?>"><span class="fa fa-code fa-fw"></span></a>
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('c');" title="<?php echo $lang['Inline code']; ?>"><span class="fa fa-file-code-o fa-fw"></span></a>
						</div>
						<div class="btn-group hidden-xs">
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('url');" title="<?php echo $lang['URL']; ?>"><span class="fa fa-link fa-fw"></span></a>
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('img');" title="<?php echo $lang['Image']; ?>"><span class="fa fa-image fa-fw"></span></a>
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('video');" title="<?php echo $lang['Video']; ?>"><span class="fa fa-play-circle fa-fw"></span></a>
						</div>
						<div class="btn-group hidden-xs">
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('list');" title="<?php echo $lang['List']; ?>"><span class="fa fa-list-ul fa-fw"></span></a>
							<a class="btn btn-default" href="javascript:void(0);" onclick="AddTag('*');" title="<?php echo $lang['List item']; ?>"><span class="fa fa-asterisk fa-fw"></span></a>
						</div>
						<div class="btn-group pull-right">
							<input class="btn btn-primary next-hidden-sm" type="submit" name="submit" tabindex="<?php echo $cur_index++ ?>" value="<?php echo $lang['Submit'] ?>" accesskey="s" />
							<input class="btn btn-default<?php if ($luna_config['o_post_responsive'] == 0) echo ' hidden-sm hidden-xs'; ?>" type="submit" name="preview" value="<?php echo $lang['Preview'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="p" />
						</div>
					</div>
					<textarea placeholder="<?php echo $lang['Start typing'] ?>" class="form-control" name="req_message" id="post_field" tabindex="<?php echo $cur_index++ ?>"></textarea>
				</fieldset>
			</div>
		</div>
	</div>

<?php

if (FORUM_ACTIVE_PAGE == 'edit') {
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
}
?>
</form>
<script>
function AddTag(tag) {
	var Field = document.getElementById('post_field');
	var val = Field.value;
	var selected_txt = val.substring(Field.selectionStart, Field.selectionEnd);
	var before_txt = val.substring(0, Field.selectionStart);
	var after_txt = val.substring(Field.selectionEnd, val.length);
	Field.value = before_txt + '[' + tag + ']' + selected_txt + '[/' + tag + ']' + after_txt;
}
</script>