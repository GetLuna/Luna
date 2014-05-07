<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Send email to'] ?> <?php echo luna_htmlspecialchars($recipient) ?></h3>
    </div>
    <form id="email" method="post" action="misc.php?email=<?php echo $recipient_id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
        <fieldset class="postfield">
            <input type="hidden" name="form_sent" value="1" />
            <input type="hidden" name="redirect_url" value="<?php echo luna_htmlspecialchars($redirect_url) ?>" />
            <label class="required hidden"><?php echo $lang['Subject'] ?></label>
            <input class="form-control" placeholder="<?php echo $lang['Subject'] ?>" type="text" name="req_subject" maxlength="70" tabindex="1" />
            <label class="required hidden"><?php echo $lang['Message'] ?></label>
            <textarea name="req_message" class="form-control" rows="10" tabindex="2"></textarea>
        </fieldset>
        <div class="panel-footer">
            <div class="btn-group"><input type="submit" class="btn btn-primary" name="submit" value="<?php echo $lang['Submit'] ?>" tabindex="3" accesskey="s" /><a href="javascript:history.go(-1)" class="btn btn-link"><?php echo $lang['Go back'] ?></a></div>
        </div>
    </form>
</div>

<?php

    require FORUM_ROOT.'footer.php';