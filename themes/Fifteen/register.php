<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<nav class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<a href="register.php" class="navbar-brand"><span class="fa fa-fw fa-user"></span> <?php _e('Register', 'luna') ?></a>
	</div>
</nav>
<?php draw_error_panel($errors); ?>
<?php draw_registration_form(); ?>