<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

<h2><?php echo $lang['Forum rules'] ?></h2>
<?php draw_rules_form(); ?>