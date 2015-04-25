<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

$jumbo_style = 'style="background:'.$cur_posting['color'].';"';

?>
</div>
<div class="jumbotron"<?php echo $jumbo_style ?>>
	<div class="container">
		<?php if ($fid) { ?>
			<h2><?php printf(__('New topic in "%s"', 'luna'), luna_htmlspecialchars($cur_posting['forum_name'])) ?></h2><span class="pull-right"><a class="btn btn-danger" href="index.php?id=<?php echo $cur_posting['id'] ?>"><span class="fa fa-fw fa-chevron-left"></span> Cancel</a></span>
		<?php } else { ?>
			<h2><?php printf(__('New comment in "%s"', 'luna'), luna_htmlspecialchars($cur_posting['subject'])) ?></h2><span class="pull-right"><a class="btn btn-danger" href="viewtopic.php?id=<?php echo $cur_posting['id'] ?>"><span class="fa fa-fw fa-chevron-left"></span> Cancel</a></span>
		<?php } ?>
	</div>
</div>
<div class="container">
<?php
if (isset($errors))
	draw_error_panel($errors);
if (isset($message))
	draw_preview_panel($message);

echo $form;

if ($luna_user['is_guest']) {
	$email_label = ($luna_config['p_force_guest_email'] == '1') ? '<strong>'.__('Email', 'luna').'</strong>' : __('Email', 'luna');
	$email_form_name = ($luna_config['p_force_guest_email'] == '1') ? 'req_email' : 'email';

?>
			<label class="required hidden"><?php _e('Name', 'luna') ?></label><input class="info-textfield form-control" type="text" placeholder="<?php _e('Name', 'luna') ?>" name="req_username" maxlength="25" tabindex="<?php echo $cur_index++ ?>" />
			<label class="conl<?php echo ($luna_config['p_force_guest_email'] == '1') ? ' required' : '' ?> hidden"><?php echo $email_label ?></label><input class="info-textfield form-control" type="text" placeholder="<?php _e('Email', 'luna') ?>" name="<?php echo $email_form_name ?>" maxlength="80" tabindex="<?php echo $cur_index++ ?>" />
<?php

}

if ($fid): ?>
			<label class="required hidden"><?php _e('Subject', 'luna') ?></label><input class="info-textfield form-control" placeholder="<?php _e('Subject', 'luna') ?>" type="text" name="req_subject" value="<?php if (isset($_POST['req_subject'])) echo luna_htmlspecialchars($subject); ?>" maxlength="70" tabindex="<?php echo $cur_index++ ?>" />
<?php endif; ?>
<?php draw_editor('20'); ?>
</form>