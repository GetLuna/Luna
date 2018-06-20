<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;
?>
<form class="form-horizontal" id="report" method="post" action="misc.php?unanswer=<?php echo $comment_id ?>&amp;tid=<?php echo $thread_id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
	<div class="main container">
		<div class="row">
			<div class="col-xs-12">
				<div class="title-block title-block-danger">
					<h2><i class="fas fa-fw fa-check"></i> <?php _e('Unsolved', 'luna') ?><span class="float-right"><button type="submit" class="btn btn-default" name="submit" accesskey="s"><span class="fas fa-fw fa-check"></span> <?php _e('Yes', 'luna') ?></button></span></h2>
				</div>
				<div class="tab-content tab-content-danger">
					<input type="hidden" name="form_sent" value="1" />
					<?php _e('Are you certain that this comment isn\'t the solution to your thread?', 'luna') ?>
				</div>
			</div>
		</div>
	</div>
</form>