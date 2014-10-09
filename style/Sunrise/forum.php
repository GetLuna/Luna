<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></h2>
<div class="btn-toolbar">
	<div class="btn-group btn-breadcrumb">
		<a class="btn btn-primary" href="index.php"><span class="fa fa-chevron-left"></span> Index</a>
	</div>
	<div class="pull-right">
		<?php echo $post_link ?>
		<ul class="pagination">
			<?php echo $paging_links ?>
		</ul>
	</div>
</div>
<div class="forum-box">
    <div class="row forum-header">
        <div class="col-xs-12"><?php echo $lang['Topic'] ?></div>
    </div>
	<?php draw_topics_list(); ?>
</div>
<div class="btn-toolbar">
	<div class="btn-group btn-breadcrumb">
		<a class="btn btn-primary" href="index.php"><span class="fa fa-chevron-left"></span> Index</a>
	</div>
	<div class="pull-right">
		<?php echo $post_link ?>
		<ul class="pagination">
			<?php echo $paging_links ?>
		</ul>
	</div>
</div>