<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

$jumbo_style = ' style="background:'.$cur_commenting['color'].';"';
$btn_style = ' style="color:'.$cur_commenting['color'].';"';

?>
<div class="jumbotron edit-title"<?php echo $jumbo_style ?>>
	<div class="container">
		<?php if ($fid) { ?>
			<h2 class="forum-title"><?php printf(__('New thread in %s', 'luna'), luna_htmlspecialchars($cur_commenting['forum_name'])) ?><span class="pull-right naviton"><a class="btn btn-default"<?php echo $btn_style ?> href="index.php?id=<?php echo $cur_commenting['fid'] ?>"><span class="fas fa-fw fa-chevron-left"></span> <?php _e('Cancel', 'luna') ?></a></span></h2>
		<?php } else { ?>
			<h2 class="forum-title"><?php printf(__('New comment in %s', 'luna'), luna_htmlspecialchars($cur_commenting['subject'])) ?><span class="pull-right naviton"><a class="btn btn-default"<?php echo $btn_style ?> href="thread.php?id=<?php echo $cur_commenting['tid'] ?>"><span class="fas fa-fw fa-chevron-left"></span> <?php _e('Cancel', 'luna') ?></a></span></h2>
		<?php } ?>
	</div>
</div>
<div class="main container">
    <div class="row">
        <div class="col-xs-12">
<?php
if (isset($errors))
	draw_error_panel($errors);
if (isset($message))
	draw_preview_panel($message);

echo $form;

if ($luna_user['is_guest']) {
	$email_form_name = ($luna_config['o_force_guest_email'] == '1') ? 'req_email' : 'email';

?>
                <label class="required hidden"><?php _e('Name', 'luna') ?></label><input class="info-textfield form-control" type="text" placeholder="<?php _e('Name', 'luna') ?>" name="req_username" value="<?php if (isset($_POST['req_username'])) echo luna_htmlspecialchars($username); ?>" maxlength="25" tabindex="<?php echo $cur_index++ ?>" autofocus />
                <label class="conl<?php echo ($luna_config['o_force_guest_email'] == '1') ? ' required' : '' ?> hidden"><?php _e('Email', 'luna') ?></label><input class="info-textfield form-control" type="text" placeholder="<?php _e('Email', 'luna') ?>" name="<?php echo $email_form_name ?>" value="<?php if (isset($_POST[$email_form_name])) echo luna_htmlspecialchars($email); ?>" maxlength="80" tabindex="<?php echo $cur_index++ ?>" />
<?php

}

if ($fid): ?>
                <label class="required hidden"><?php _e('Subject', 'luna') ?></label><input class="info-textfield form-control" placeholder="<?php _e('Subject', 'luna') ?>" type="text" name="req_subject" value="<?php if (isset($_POST['req_subject'])) echo luna_htmlspecialchars($subject); ?>" maxlength="70" tabindex="<?php echo $cur_index++ ?>"<?php if (!$luna_user['is_guest']) { echo ' autofocus'; } ?> />
<?php endif; ?>
                <?php draw_editor('20'); ?>
            </form>
        </div>
    </div>
</div>