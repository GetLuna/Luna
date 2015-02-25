<?php

$style = ' style="background-color:'.$cur_section['color'].';"';

if ($section_head == '1') {
?>
<div class="alert alert-info alert-section"<?php echo $style ?>>
	<h3 class="inline"><?php echo $cur_section['forum_name']; ?></h3><span class="pull-right"><a class="btn btn-default" href="post.php?fid=<?php echo $cur_section['id'] ?>">Post</a></span>
	<p><?php echo $cur_section['forum_desc']; ?></p>
</div>
<?php } else { ?>
<div class="alert alert-info alert-section alert-all">
	<h3>We're showing everything</h3>
</div>
<?php } ?>