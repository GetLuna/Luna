<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2 class="profile-h2"><?php echo $lang['Change pass'] ?></h2>
<form class="form-horizontal" id="change_pass" method="post" action="profile.php?action=change_pass&amp;id=<?php echo $id ?>" onsubmit="return process_form(this)">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Change pass'] ?></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="form_sent" value="1" />
            <fieldset>
                <?php if (!$luna_user['is_admmod']): ?>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"><?php echo $lang['Old pass'] ?></label>
                        <div class="col-sm-9">
                            <input class="form-control" type="password" name="req_old_password" />
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['New pass'] ?></label>
                    <div class="col-sm-9">
                        <input class="form-control" type="password" name="req_new_password1" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Confirm new pass'] ?></label>
                    <div class="col-sm-9">
                        <input class="form-control" type="password" name="req_new_password2" />
                    </div>
                </div>
                <p class="help-block"><?php echo $lang['Pass info'] ?></p>
            </fieldset>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" name="update" value="<?php echo $lang['Submit'] ?>" /> <a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a>
        </div>
    </div>
</form>
<?php

    require FORUM_ROOT.'footer.php';