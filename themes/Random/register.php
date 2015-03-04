<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
</div>
<div class="jumbotron" style="background:#999;">
	<div class="container">
		<h2><?php echo $lang['Register'] ?></h2>
	</div>
</div>
<div class="container">
<?php draw_error_panel($errors); ?>
<?php draw_registration_form(); ?>