<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;
?>
<div class="main container">
	<div class="row">
		<div class="col-xs-12">
			<form class="form-horizontal" id="report" method="post" action="misc.php?report=<?php echo $comment_id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
				<div class="title-block title-block-primary">
					<h2><i class="fas fa-fw fa-flag"></i> <?php _e( 'Report', 'luna' ) ?><span class="pull-right"><button type="submit" class="btn btn-default" name="submit" accesskey="s"><span class="fas fa-fw fa-check"></span> <?php _e('Submit', 'luna') ?></button></span></h2>
				</div>
				<div class="tab-content">
					<input type="hidden" name="form_sent" value="1" />
					<textarea class="form-control" placeholder="<?php _e('Tell us why you are reporting this', 'luna') ?>" name="req_reason" rows="5"></textarea>
				</div>
			</form>
		</div>
	</div>
</div>