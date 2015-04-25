<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

load_inbox_nav('send');
?>
<?php
// If there are errors, we display them
if (!empty($errors)) {
?>
<div class="panel panel-danger">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e('Post errors', 'luna') ?></h3>
	</div>
	<div class="panel-body">
		<p><?php _e('Post errors info', 'luna') ?></p>
<?php
	foreach ($errors as $cur_error)
		echo "\t\t\t\t".$cur_error."\n";
?>
	</div>
</div>
<?php

} elseif (isset($_POST['preview'])) {
	require_once FORUM_ROOT.'include/parser.php';
	$preview_message = parse_message($p_message);

?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e('Post preview', 'luna') ?></h3>
	</div>
	<div class="panel-body">
		<p><?php echo $preview_message."\n" ?></p>
	</div>
</div>
<?php

}

$cur_index = 1;

?>
<form class="form-horizontal" method="post" id="post" action="new_inbox.php" onsubmit="return process_form(this)">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Write message', 'luna') ?></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<input type="hidden" name="form_sent" value="1" />
				<input type="hidden" name="form_user" value="<?php echo luna_htmlspecialchars($luna_user['username']) ?>" />
				<?php echo (($r != '0') ? '<input type="hidden" name="reply" value="'.$r.'" />' : '') ?>
				<?php echo (($edit != '0') ? '<input type="hidden" name="edit" value="'.$edit.'" />' : '') ?>
				<?php echo (($q != '0') ? '<input type="hidden" name="quote" value="1" />' : '') ?>
				<?php echo (($tid != '0') ? '<input type="hidden" name="tid" value="'.$tid.'" />' : '') ?>
				<input type="hidden" name="p_username" value="<?php echo luna_htmlspecialchars($p_destinataire) ?>" />
				<input type="hidden" name="req_subject" value="<?php echo luna_htmlspecialchars($p_subject) ?>" />
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Send to', 'luna') ?><span class="help-block">Separate names with commas, maximum <?php echo ($luna_config['o_pms_max_receiver']-1) ?> names</span></label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="p_username" id="p_username" size="30" value="<?php echo luna_htmlspecialchars($p_destinataire) ?>" tabindex="<?php echo $cur_index++ ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Subject', 'luna') ?></label>
					<div class="col-sm-9">
						<input class="form-control" type="text" name="req_subject" value="<?php echo ($p_subject != '' ? luna_htmlspecialchars($p_subject) : ''); ?>" tabindex="<?php echo $cur_index++ ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label"><?php _e('Message', 'luna') ?></label>
					<div class="col-sm-9">
						<?php draw_editor('10'); ?>
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</form>