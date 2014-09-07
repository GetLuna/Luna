<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></h2>

<div class="row-nav">
	<div class="btn-breadcrumb">
		<a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><span class="fa fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
	</div>
	<ul class="pagination pull-right">
		<?php echo $paging_links ?>
	</ul>
</div>

<?php require get_view_path('comment.php'); ?>

<div class="row-nav">
	<div class="btn-breadcrumb">
		<a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><span class="fa fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
	</div>
	<ul class="pagination pull-right">
		<?php echo $paging_links ?>
	</ul>
</div>