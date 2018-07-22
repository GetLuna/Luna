<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

<form method="post" action="delete.php?id=<?php echo $id ?>" class="main container">
	<div class="jumbotron default">
		<h2><?php draw_delete_title(); ?></h2>
	</div>
	<div class="row">
		<div class="col-12">
			<p><?php echo ($is_thread_comment) ? '<strong>'.__('This is the first comment in the thread, the whole thread will be permanently deleted.', 'luna').'</strong> ' : '' ?><?php _e('The comment you have chosen to delete is set out below for you to review before proceeding.', 'luna') ?></p>
			<button type="submit" class="btn btn-danger" name="delete"><span class="fas fa-fw fa-trash-alt"></span> <?php _e('Delete', 'luna') ?></button>
			<hr />
			<?php echo $cur_comment['message'] ?>
		</div>
	</div>
</form>