<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
</div>
<div class="jumbotron">
	<div class="container">
		<h2><?php _e('Send email to', 'luna') ?> <?php echo luna_htmlspecialchars($recipient) ?></h2>
	</div>
</div>
<div class="container">
<?php draw_mail_form($recipient_id); ?>