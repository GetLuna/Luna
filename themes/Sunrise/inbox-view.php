<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;
?>
<div class="row">
    <div class="col-xs-12">
        <?php
load_inbox_nav('view');

echo $paging_links;
        ?>
    </div>
</div>
<?php

draw_response_list();

?>
<div class="row">
    <div class="col-xs-12">
        <?php echo $paging_links; ?>
        <form method="post" id="comment" action="new_inbox.php?reply=<?php echo $tid ?>" onsubmit="return process_form(this)">
            <input type="hidden" name="req_subject" placeholder="<?php _e('Subject', 'luna') ?>" value="<?php echo ($p_subject != '' ? luna_htmlspecialchars($p_subject) : ''); ?>" tabindex="<?php echo $cur_index++ ?>" />
            <input type="hidden" name="form_user" value="<?php echo luna_htmlspecialchars($luna_user['username']) ?>" />
            <input type="hidden" name="reply" value="1" />
            <?php draw_editor('10'); ?>
        </form>
    </div>
</div>
