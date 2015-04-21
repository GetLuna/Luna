<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>

<div class="btn-group btn-breadcrumb">
	<a class="btn btn-primary" href="viewtopic.php?pid=<?php echo $post_id ?>#p<?php echo $post_id ?>"><span class="fa fa-fw fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_post['subject']) ?></a>
</div>

<?php draw_report_form($post_id); ?>