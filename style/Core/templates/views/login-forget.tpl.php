<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

// If there are errors, we display them
if (!empty($errors)) {
?>

<div id="posterror">
    <h2><?php echo $lang['New password errors'] ?></h2>
    <div class="error-info">
        <p><?php echo $lang['New passworderrors info'] ?></p>
        <ul class="error-list">
<?php

    foreach ($errors as $cur_error)
        echo "\t\t\t\t".'<li><strong>'.$cur_error.'</strong></li>'."\n";
?>
        </ul>
    </div>
</div>

<?php } ?>

<form class="form" id="request_pass" method="post" action="login.php?action=forget_2" onsubmit="this.request_pass.disabled=true;if(process_form(this)){return true;}else{this.request_pass.disabled=false;return false;}">
    <h1 class="form-heading"><?php echo $lang['Request pass'] ?></h1>
    <fieldset>
        <input type="hidden" name="form_sent" value="1" />
        <label class="required"><input class="form-control" type="text" name="req_email" placeholder="<?php echo $lang['Email'] ?>" /></label>
        <div class="pull-right" style="margin-top: 60px;">
            <?php if (empty($errors)): ?><a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a><?php endif; ?><input class="btn btn-primary" type="submit" name="request_pass" value="<?php echo $lang['Submit'] ?>" />
        </div>
    </fieldset>
</form>

<?php

    require FORUM_ROOT.'footer.php';