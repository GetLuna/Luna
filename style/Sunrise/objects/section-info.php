<?php

$style = ' style="background-color:'.$cur_section['color'].';"';

if ($section_head == '1') {
?>
<div class="alert alert-info alert-section"<?php echo $style ?>>
	<h3><?php echo $cur_section['forum_name']; ?></h3>
	<p><?php echo $cur_section['forum_desc']; ?></p>
</div>
<?php } else { ?>
<div class="alert alert-info alert-section alert-all">
	<h3>We're showing everything</h3>
</div>
<?php } ?>