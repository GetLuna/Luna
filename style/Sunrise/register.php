<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

<?php draw_error_panel($errors); ?>
<h2><?php echo $lang['Register'] ?></h2>
<?php draw_registration_form(); ?>