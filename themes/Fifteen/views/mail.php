<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<div class="main container">
	<div class="row">
		<div class="col-12">
			<div class="title-block title-block-primary">
				<h2><i class="fas fa-fw fa-envelope"></i> <?php _e('Send an email', 'luna') ?></h2>
			</div>
			<div class="tab-content">
				<?php draw_mail_form($recipient_id); ?>
			</div>
		</div>
	</div>
</div>