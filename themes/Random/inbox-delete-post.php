<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

<div class="panel panel-danger">
	<div class="panel-heading">
		<h3 class="panel-title"><?php printf($cur_delete['show_message'] ? $lang['Topic by'] : $lang['Reply by'], '<strong>'.luna_htmlspecialchars($cur_delete['sender']).'</strong>', format_time($cur_delete['posted'])) ?><span class="pull-right"><button class="btn btn-danger" type="submit" name="delete"><span class="fa fa-fw fa-trash"></span><?php echo $lang['Delete'] ?></button></span></h3>
	</div>
	<div class="panel-body">
		<form action="viewinbox.php" method="post">
			<input type="hidden" name="action" value="delete" />
			<input type="hidden" name="mid" value="<?php echo $mid ?>" />
			<input type="hidden" name="tid" value="<?php echo $tid ?>" />
			<input type="hidden" name="delete_comply" value="1" />
			<input type="hidden" name="all_topic" value="<?php echo $cur_delete['show_message'] ?>" />
			<p><?php echo ($cur_delete['show_message']) ? $lang['Topic warning info'].'' : '<strong>'.$lang['Warning'].'</strong>' ?><br /><?php echo $lang['Delete info'] ?></p>
			<?php if ($luna_user['is_admmod']) : ?>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="delete_all" value="1" /><?php echo $lang['Delete for everybody'] ?>
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
				<h3>By <b><?php echo luna_htmlspecialchars($cur_delete['sender']) ?></b><small class="pull-right"><?php echo format_time($cur_delete['posted']) ?></small></h3>
				<hr />
				<?php echo $cur_delete['message']."\n" ?>
			</div>
		</div>
	</div>
</div>