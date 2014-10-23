<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

$jumbo_style = 'style="background:'.$cur_forum['color'].';"';

?>
</div>
<div class="jumbotron"<?php echo $jumbo_style ?>>
	<div class="container">
		<h2><?php echo luna_htmlspecialchars($cur_forum['forum_name']) ?></h2><span class="pull-right"><?php echo $post_link ?><ul class="pagination"><?php echo $paging_links ?></ul></span>
	</div>
</div>
<div class="container">
	<?php draw_topics_list(); ?>