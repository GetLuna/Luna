<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<h2 class="profile-title"><?php _e('Register', 'luna') ?></h2>
<?php draw_error_panel($errors); ?>
<?php draw_registration_form(); ?>