<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

$jumbo_style = ' style="background:'.$cur_commenting['color'].';"';
$btn_style = ' style="color:'.$cur_commenting['color'].';"';

?>
<div class="jumbotron titletron"<?php echo $jumbo_style ?>>
	<div class="container">
		<h2 class="forum-title">
			<?php if ($fid) { ?>
				<small>
					<?php _e('New thread', 'luna') ?>
				</small>
				<?php echo luna_htmlspecialchars($cur_commenting['forum_name']) ?>
				<span class="float-right naviton">
					<a class="btn btn-light"<?php echo $btn_style ?> href="index.php?id=<?php echo $cur_commenting['fid'] ?>">
						<i class="fas fa-fw fa-chevron-left"></i> <?php _e('Cancel', 'luna') ?>
					</a>
				</span>
			<?php } else { ?>
				<small>
					<?php _e('New comment', 'luna') ?>
				</small>
				<?php echo luna_htmlspecialchars($cur_commenting['subject']) ?>
				<span class="float-right naviton">
					<a class="btn btn-light"<?php echo $btn_style ?> href="thread.php?id=<?php echo $cur_commenting['tid'] ?>">
						<i class="fas fa-fw fa-chevron-left"></i> <?php _e('Cancel', 'luna') ?>
					</a>
				</span>
			<?php } ?>
		</h2>
	</div>
</div>
<div class="main container">
    <div class="row">
        <div class="col-12">
<?php
if (isset($errors))
	draw_error_panel($errors);
if (isset($message))
	draw_preview_panel($message);

echo $form;

if ($luna_user['is_guest']) {
	$email_form_name = ($luna_config['o_force_guest_email'] == '1') ? 'req_email' : 'email';

?>
                <label class="d-none required"><?php _e('Name', 'luna') ?></label>
				<input class="info-textfield form-control" type="text" placeholder="<?php _e('Name', 'luna') ?>" name="req_username" value="<?php if (isset($_POST['req_username'])) echo luna_htmlspecialchars($username); ?>" maxlength="25" tabindex="<?php echo $cur_index++ ?>" required autofocus />
                <label class="conl<?php echo ($luna_config['o_force_guest_email'] == '1') ? ' required' : '' ?> d-none"><?php _e('Email', 'luna') ?></label>
				<input class="info-textfield form-control" type="text" placeholder="<?php _e('Email', 'luna') ?>" name="<?php echo $email_form_name ?>" value="<?php if (isset($_POST[$email_form_name])) echo luna_htmlspecialchars($email); ?>" maxlength="80" tabindex="<?php echo $cur_index++ ?>" />
<?php
}

if ($fid): ?>
                <label class="required d-none"><?php _e('Subject', 'luna') ?></label><input class="info-textfield form-control" placeholder="<?php _e('Subject', 'luna') ?>" type="text" name="req_subject" value="<?php if (isset($_POST['req_subject'])) echo luna_htmlspecialchars($subject); ?>" maxlength="70" tabindex="<?php echo $cur_index++ ?>"<?php if (!$luna_user['is_guest']) { echo ' autofocus'; } ?> />
<?php endif; ?>
                <?php draw_editor('20'); ?>
            </form>
        </div>
    </div>
</div>