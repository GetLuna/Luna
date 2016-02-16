<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

<div class="panel panel-danger">
	<div class="panel-heading">
		<h3 class="panel-title"><?php draw_delete_title(); ?></h3>
	</div>
	<div class="panel-body">
		<form method="post" action="delete.php?id=<?php echo $id ?>">
			<p><?php echo ($is_thread_comment) ? '<strong>'.__('This is the first comment in the thread, the whole thread will be permanently deleted.', 'luna').'</strong>' : '' ?><br /><?php _e('The comment you have chosen to delete is set out below for you to review before proceeding.', 'luna') ?></p>
			<div class="btn-toolbar">
				<a class="btn btn-default" href="thread.php?pid=<?php echo $id ?>#p<?php echo $id ?>"><span class="fa fa-fw fa-chevron-left"></span> <?php _e('Cancel', 'luna') ?></a>
				<button type="submit" class="btn btn-danger" name="delete"><span class="fa fa-fw fa-trash"></span> <?php _e('Delete', 'luna') ?></button>
			</div>
		</form>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo luna_htmlspecialchars($cur_comment['commenter']) ?></h3>
	</div>
	<div class="panel-body">
		<?php echo $cur_comment['message'] ?>
	</div>
</div>
