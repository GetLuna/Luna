<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

load_inbox_nav('view');

echo $paging_links;

draw_response_list();

echo $paging_links;

?>
<!-- <form method="post" id="comment" action="new_inbox.php?reply=<?php echo $tid ?>" onsubmit="return process_form(this)">
<?php draw_editor('10'); ?>
</form> -->