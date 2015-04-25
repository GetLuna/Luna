<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

<div class="panel panel-danger">
	<div class="panel-heading">
		<h3 class="panel-title"><?php printf($cur_delete['show_message'] ? __('Topic started by %s - %s', 'luna') : __('Reply by %s - %s', 'luna'), '<strong>'.luna_htmlspecialchars($cur_delete['sender']).'</strong>', format_time($cur_delete['posted'])) ?><span class="pull-right"><button class="btn btn-danger" type="submit" name="delete"><span class="fa fa-fw fa-trash"></span><?php _e('Delete', 'luna') ?></button></span></h3>
	</div>
	<div class="panel-body">
		<form action="viewinbox.php" method="post">
			<input type="hidden" name="action" value="delete" />
			<input type="hidden" name="mid" value="<?php echo $mid ?>" />
			<input type="hidden" name="tid" value="<?php echo $tid ?>" />
			<input type="hidden" name="delete_comply" value="1" />
			<input type="hidden" name="all_topic" value="<?php echo $cur_delete['show_message'] ?>" />
			<p><?php echo ($cur_delete['show_message']) ? __('The topic will be deleted from your inbox, but it will stays in the others receivers' boxes.', 'luna').'' : '<strong>'.__('Warning: critical features', 'luna').'</strong>' ?><br /><?php _e('The post you have chosen to delete is set out below for you to review before proceeding.', 'luna') ?></p>
			<?php if ($luna_user['is_admmod']) : ?>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="delete_all" value="1" /><?php _e('If you tick this checkbox, you will delete the message (or the topic) for all the receivers (available only for admins &amp; mods)', 'luna') ?>
				</label>
			</div>
			<?php endif; ?>
		</form>
	</div>
</div>

<div class="row comment">
	<div class="col-xs-12">
		<div class="panel panel-default">
			<div class="panel-body">
				<h3><?php printf(__('By %s', 'luna'), luna_htmlspecialchars($cur_delete['sender']) ?><small class="pull-right"><?php echo format_time($cur_delete['posted']) ?></small></h3>
				<hr />
				<?php echo $cur_delete['message']."\n" ?>
			</div>
		</div>
	</div>
</div>