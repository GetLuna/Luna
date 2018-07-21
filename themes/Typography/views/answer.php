<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;
?>

<form class="main container" id="report" method="post" action="misc.php?answer=<?php echo $comment_id ?>&amp;tid=<?php echo $thread_id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
	<div class="jumbotron default">
		<h2><?php _e('Answer', 'luna') ?></h2>
	</div>
	<div class="row">
		<div class="col-12">
			<input type="hidden" name="form_sent" value="1" />
			<p><?php _e('Are you certain that this comment is the solution to your thread?', 'luna') ?></p>
			<button type="submit" class="btn btn-success" name="submit" accesskey="s"><span class="fas fa-fw fa-check"></span> <?php _e('Yes', 'luna') ?></button>
		</div>
	</div>
</form>