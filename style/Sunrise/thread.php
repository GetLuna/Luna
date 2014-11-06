<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></h2>

<div class="row-nav">
	<a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><span class="fa fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
	<ul class="pagination pull-right">
		<?php echo $paging_links ?>
	</ul>
</div>

<?php draw_topic_list(); ?>
<form method="post" action="post.php?tid=<?php echo $id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
<?php draw_editor('10'); ?>
</form>

<div class="row-nav">
	<a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_topic['forum_id'] ?>"><span class="fa fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
	<ul class="pagination pull-right">
		<?php echo $paging_links ?>
	</ul>
</div>