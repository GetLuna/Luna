<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="row-nav">
	<a class="btn btn-primary" href="index.php?id=<?php echo $cur_topic['forum_id'] ?>"><span class="fa fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
	<span class="pull-right">
		<?php echo $paging_links ?>
	</span>
</div>

<div class="jumbotron thread-jumbotron">
	<h2><?php echo luna_htmlspecialchars($cur_topic['subject']) ?></h2>
</div>
<?php draw_topic_list(); ?>
<form method="post" action="post.php?tid=<?php echo $id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
<?php draw_editor('10'); ?>
</form>

<div class="row-nav">
	<a class="btn btn-primary" href="index.php?id=<?php echo $cur_topic['forum_id'] ?>"><span class="fa fa-chevron-left"></span> <?php echo luna_htmlspecialchars($cur_topic['forum_name']) ?></a>
	<span class="pull-right">
		<?php echo $paging_links ?>
	</span>
</div>