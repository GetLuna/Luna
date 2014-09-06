<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="row-nav">
	<div class="btn-group btn-breadcrumb">
		<a class="btn btn-primary next-hidden-xs" href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><span class="fa fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
	</div>
	<?php echo $post_link ?>
	<ul class="pagination pull-right">
		<?php echo $paging_links ?>
	</ul>
</div>