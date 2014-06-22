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
					<div class="panel-heading">
						<div class="comment-arrow hidden-sm hidden-xs"></div>
						<h3 class="panel-title"><?php echo $lang['Quick post'] ?></h3>
					</div>
					<fieldset class="quickpostfield">
                        <div class="btn-toolbar textarea-toolbar">
                            <div class="btn-group">
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[b][/b]');"><span class="fa fa-bold"></span></a>
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[u][/u]');"><span class="fa fa-underline"></span></a>
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[i][/i]');"><span class="fa fa-italic"></span></a>
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[s][/s]');"><span class="fa fa-strikethrough"></span></a>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[h][/h]');"><span class="fa fa-header"></span></a>
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[sub][/sub]');"><span class="fa fa-subscript"></span></a>
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[sup][/sup]');"><span class="fa fa-superscript"></span></a>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[quote][/quote]');"><span class="fa fa-quote-left"></span></a>
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[code][/code]');"><span class="fa fa-code"></span></a>
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[c][/c]');"><span class="fa fa-file-code-o"></span></a>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[url][/url]');"><span class="fa fa-link"></span></a>
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[img][/img]');"><span class="fa fa-image"></span></a>
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[video][/video]');"><span class="fa fa-play-circle"></span></a>
                            </div>
                            <div class="btn-group">
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[list][/list]');"><span class="fa fa-list-ol"></span></a>
                                <a class="btn btn-default" href="javascript:void(0);" onclick="inyectarTexto('req_message','[list=a][/list]');"><span class="fa fa-list-ul"></span></a>
                            </div>
                        </div>
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
						<textarea placeholder="Start typing..." class="form-control" name="req_message" rows="7" tabindex="<?php echo $cur_index++ ?>"></textarea>
					</fieldset>
					<div class="panel-footer">
						<div class="btn-group"><input class="btn btn-primary" type="submit" name="submit" tabindex="<?php echo $cur_index++ ?>" value="<?php echo $lang['Submit'] ?>" accesskey="s" /><input class="btn btn-default" type="submit" name="preview" value="<?php echo $lang['Preview'] ?>" tabindex="<?php echo $cur_index++ ?>" accesskey="p" /></div>
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
function inyectarTexto(elemento,valor) {
     var elemento_dom=document.getElementsByName(elemento)[0];
     if(document.selection) {
         elemento_dom.focus();
         sel=document.selection.createRange();
         sel.text=valor;
         return;
     } if(elemento_dom.selectionStart||elemento_dom.selectionStart=="0") {
         var t_start=elemento_dom.selectionStart;
         var t_end=elemento_dom.selectionEnd;
         var val_start=elemento_dom.value.substring(0,t_start);
         var val_end=elemento_dom.value.substring(t_end,elemento_dom.value.length);
         elemento_dom.value=val_start+valor+val_end;
     } else {
         elemento_dom.value+=valor;
     }
}
</script>