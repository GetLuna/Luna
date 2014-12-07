<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

$jumbo_style = 'style="background:'.$cur_forum['color'].';"';

?>
</div>
<div class="jumbotron<?php echo $item_status ?>"<?php echo $jumbo_style ?>>
	<div class="container">
		<h2><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></h2><span class="pull-right"><?php echo $post_link ?><?php echo $paging_links ?></span>
	</div>
</div>
<div class="container">
    <div class="topic-entry-list">
        <?php draw_topics_list(); ?>
	</div>