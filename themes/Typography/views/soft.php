<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

<form class="main container" method="post" action="delete.php?id=<?php echo $id ?>&action=soft">
	<div class="jumbotron default">
		<h2><?php draw_delete_title(); ?></h2>
	</div>
	<div class="row">
		<div class="col-12">
			<p><?php echo ($is_thread_comment) ? '<strong>'.__('This is the first comment in the thread, the whole thread will be hidden.', 'luna').'</strong> ' : '' ?><?php _e('The comment you have chosen to hide is set out below for you to review before proceeding. Deleting this comment is not permanent. If you want to delete a comment permanently, please use delete instead.', 'luna') ?></p>
			<button type="submit" class="btn btn-danger" name="soft_delete"><span class="fas fa-fw fa-eye-slash"></span> <?php _e('Hide', 'luna') ?></button>
			<hr />
			<?php echo $cur_comment['message'] ?>
		</div>
	</div>
</form>