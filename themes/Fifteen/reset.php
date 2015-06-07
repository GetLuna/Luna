<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php draw_delete_title(); ?></h3>
	</div>
	<div class="panel-body">
		<?php draw_soft_reset_form($id); ?>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo luna_htmlspecialchars($cur_post['poster']) ?></h3>
	</div>
	<div class="panel-body">
		<?php echo $cur_post['message'] ?>
	</div>
</div>