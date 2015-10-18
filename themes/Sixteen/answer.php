<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;
?>

<form class="form-horizontal" id="report" method="post" action="misc.php?answer=<?php echo $comment_id ?>&amp;tid=<?php echo $thread_id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e('Answer', 'luna') ?></h3>
		</div>
		<div class="panel-body">
			<fieldset>
				<input type="hidden" name="form_sent" value="1" />
				<?php _e('Are you certain that this comment is the solution to your thread?', 'luna') ?>
				<div class="btn-toolbar">
					<a href="thread.php?pid=<?php echo $comment_id ?>#p<?php echo $comment_id ?>" class="btn btn-danger"><span class="fa fa-fw fa-times"></span> <?php _e('No', 'luna') ?></a>
					<button type="submit" class="btn btn-success" name="submit" accesskey="s"><span class="fa fa-fw fa-check"></span> <?php _e('Yes', 'luna') ?></button>
				</div>
			</fieldset>
		</div>
	</div>
</form>