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
			<label class="required hidden"><?php echo $lang['Guest name'] ?></label><input class="form-control" type="text" placeholder="<?php echo $lang['Guest name'] ?>" name="req_username" maxlength="25" tabindex="<?php echo $cur_index++ ?>" />
			<label class="conl<?php echo ($luna_config['p_force_guest_email'] == '1') ? ' required' : '' ?> hidden"><?php echo $email_label ?></label><input class="form-control" type="text" placeholder="<?php echo $lang['Email'] ?>" name="<?php echo $email_form_name ?>" maxlength="80" tabindex="<?php echo $cur_index++ ?>" />
<?php

}

if ($fid): ?>
			<label class="required hidden"><?php echo $lang['Subject'] ?></label><input class="longinput form-control" placeholder="<?php echo $lang['Subject'] ?>" type="text" name="req_subject" value="<?php if (isset($_POST['req_subject'])) echo luna_htmlspecialchars($subject); ?>" maxlength="70" tabindex="<?php echo $cur_index++ ?>" />
<?php endif; ?>
			<div class="btn-toolbar textarea-toolbar">
				<div class="btn-group">
					<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','b');" title="<?php echo $lang['Bold']; ?>"><span class="fa fa-bold fa-fw"></span></a>
					<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','u');" title="<?php echo $lang['Underline']; ?>"><span class="fa fa-underline fa-fw"></span></a>
					<a class="btn btn-default btn-editor hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','i');" title="<?php echo $lang['Italic']; ?>"><span class="fa fa-italic fa-fw"></span></a>
					<a class="btn btn-default btn-editor hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','s');" title="<?php echo $lang['Strike']; ?>"><span class="fa fa-strikethrough fa-fw"></span></a>
				</div>
				<div class="btn-group">
					<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','h');" title="<?php echo $lang['Heading']; ?>"><span class="fa fa-header fa-fw"></span></a>
					<a class="btn btn-default btn-editor hidden-md hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','sub');" title="<?php echo $lang['Subscript']; ?>"><span class="fa fa-subscript fa-fw"></span></a>
					<a class="btn btn-default btn-editor hidden-md hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','sup');" title="<?php echo $lang['Superscript']; ?>"><span class="fa fa-superscript fa-fw"></span></a>
				</div>
				<div class="btn-group">
					<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','quote');" title="<?php echo $lang['Quote']; ?>"><span class="fa fa-quote-left fa-fw"></span></a>
					<a class="btn btn-default btn-editor hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('code','code');" title="<?php echo $lang['Code']; ?>"><span class="fa fa-code fa-fw"></span></a>
					<a class="btn btn-default btn-editor hidden-md hidden-sm hidden-xs" href="javascript:void(0);" onclick="AddTag('inline','c');" title="<?php echo $lang['Inline code']; ?>"><span class="fa fa-file-code-o fa-fw"></span></a>
				</div>
				<div class="btn-group">
					<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','url');" title="<?php echo $lang['URL']; ?>"><span class="fa fa-link fa-fw"></span></a>
					<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','img');" title="<?php echo $lang['Image']; ?>"><span class="fa fa-image fa-fw"></span></a>
					<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','video');" title="<?php echo $lang['Video']; ?>"><span class="fa fa-play-circle fa-fw"></span></a>
				</div>
				<div class="btn-group">
					<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('list', 'list');" title="<?php echo $lang['List']; ?>"><span class="fa fa-list-ul fa-fw"></span></a>
					<a class="btn btn-default btn-editor" href="javascript:void(0);" onclick="AddTag('inline','*');" title="<?php echo $lang['List item']; ?>"><span class="fa fa-asterisk fa-fw"></span></a>
				</div>
			</div>
			<textarea class="form-control"  placeholder="<?php echo $lang['Start typing'] ?>" name="req_message" id="post_field" rows="20" tabindex="<?php echo $cur_index++ ?>"><?php echo isset($_POST['req_message']) ? luna_htmlspecialchars($orig_message) : (isset($quote) ? $quote : ''); ?></textarea>
		</fieldset>
		<div class="panel-footer">
			<div class="btn-group"><input class="btn btn-primary" type="submit" name="submit" value="<?php echo $lang['Submit'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="s" /><input class="btn btn-default" type="submit" name="preview" value="<?php echo $lang['Preview'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="p" /></div><div class="btn-group"><a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a></div>
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
<script>
function AddTag(type, tag) {
   var Field = document.getElementById('post_field');
   var val = Field.value;
   var selected_txt = val.substring(Field.selectionStart, Field.selectionEnd);
   var before_txt = val.substring(0, Field.selectionStart);
   var after_txt = val.substring(Field.selectionEnd, val.length);
   if (type == 'inline')
	   Field.value = before_txt + '[' + tag + ']' + selected_txt + '[/' + tag + ']' + after_txt;
   else if (type == 'list')
	   Field.value = before_txt + '[list]' + "\r" + '[*]' + selected_txt + '[/*]' + "\r" + '[/list]' + after_txt;
   else if (type == 'code')
	   Field.value = before_txt + '[' + tag + ']' + "\r" + '[[language]]' + "\r" + selected_txt + "\r" + '[/' + tag + ']' + after_txt;
   else if (type == 'emoji')
	   Field.value = before_txt + tag + after_txt;
}
</script>
<?php

}