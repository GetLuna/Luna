<?php

	$style = ' style="background-color:'.$cur_section['color'].';"';

?>
<div class="alert alert-info alert-section"<?php echo $style ?>>
	<h3><?php echo $cur_section['forum_name']; ?></h3>
	<p><?php echo $cur_section['forum_desc']; ?></p>
</div>