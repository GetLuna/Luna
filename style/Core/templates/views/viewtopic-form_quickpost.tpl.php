<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

<div class="postview">
	<div class="row topic">
		<div class="col-md-3">
			<div class="profile-card">
				<div class="profile-card-head profile-card-quickpost">
					<div class="user-avatar thumbnail is-online">
						<?php echo generate_avatar_markup($luna_user['id']) ?>
					</div>
					<h2><?php echo $luna_user['username'] ?></h2>
					<h3><?php echo get_title($luna_user) ?></h3>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<form id="quickpostform" method="post" action="post.php?tid=<?php echo $id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
				<div class="panel panel-default panel-border">
					<div class="panel-heading panel-quickpost-heading">
						<div class="comment-arrow hidden-sm hidden-xs"></div>
						<h3 class="panel-title"><?php echo $lang['Quick post'] ?></h3>
					</div>
					<fieldset class="quickpostfield">
						<input type="hidden" name="form_sent" value="1" />
<?php if ($luna_config['o_topic_subscriptions'] == '1' && ($luna_user['auto_notify'] == '1' || $cur_topic['is_subscribed'])): ?>						<input type="hidden" name="subscribe" value="1" />
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
						<textarea placeholder="<?php echo $lang['Start typing'] ?>" class="form-control" name="req_message" id="post_field" rows="7" tabindex="<?php echo $cur_index++ ?>"></textarea>
					</fieldset>
					<div class="panel-footer">
						<div class="btn-group"><input class="btn btn-primary next-hidden-sm" type="submit" name="submit" tabindex="<?php echo $cur_index++ ?>" value="<?php echo $lang['Submit'] ?>" accesskey="s" /><input class="btn btn-default<?php if ($luna_config['o_post_responsive'] == 0) echo ' hidden-sm hidden-xs'; ?>" type="submit" name="preview" value="<?php echo $lang['Preview'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="p" /></div>
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
			</form>
		</div>
	</div>
</div>
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