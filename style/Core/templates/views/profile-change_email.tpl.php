<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2 class="profile-h2"><?php echo $lang['Change email'] ?></h2>
<form id="change_email" method="post" action="profile.php?action=change_email&amp;id=<?php echo $id ?>" onsubmit="return process_form(this)">
    <fieldset>
        <h3><?php echo $lang['Email legend'] ?></h3>
        <input type="hidden" name="form_sent" value="1" />
        <label><strong><?php echo $lang['New email'] ?></strong><br /><input type="text" class="form-control" name="req_new_email" maxlength="80" /></label>
        <label><strong><?php echo $lang['Password'] ?></strong><br /><input type="password" name="req_password" /></label>
        <p><?php echo $lang['Email instructions'] ?></p>
    </fieldset>
    <p><input type="submit" class="btn btn-primary" name="new_email" value="<?php echo $lang['Submit'] ?>" /> <a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a></p>
</form>
<?php

    require FORUM_ROOT.'footer.php';