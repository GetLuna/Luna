<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

// If there are errors, we display them
if (!empty($errors))
{

?>

<div class="alert alert-danger">
    <h4><?php echo $lang['Registration errors'] ?></h4>
<?php

    foreach ($errors as $cur_error)
        echo "\t\t\t\t".'<span class="error-list">'.$cur_error.'</span>'."<br />";
?>
</div>

<?php

}
?>
<h2><?php echo $lang['Register'] ?></h2>
<form class="form-horizontal" id="register" method="post" action="register.php?action=register" onsubmit="this.register.disabled=true;if(process_form(this)){return true;}else{this.register.disabled=false;return false;}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Register legend'] ?><span class="pull-right"><input type="submit" class="btn btn-primary" name="register" value="<?php echo $lang['Register'] ?>" /></span></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <input type="hidden" name="form_sent" value="1" />
                <label class="required usernamefield"><?php echo $lang['If human'] ?><input type="text" class="form-control" name="req_username" value="" maxlength="25" /></label>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Username'] ?><span class="help-block"><?php echo $lang['Username legend'] ?></span></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="req_user" value="<?php if (isset($_POST['req_user'])) echo luna_htmlspecialchars($_POST['req_user']); ?>" maxlength="25" />
                    </div>
                </div>
<?php if ($luna_config['o_regs_verify'] == '0'): ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Password'] ?><span class="help-block"><?php echo $lang['Pass info'] ?></span></label>
                    <div class="col-sm-9">
                        <div class="row">
                            <div class="col-sm-6">
                                <input type="password" class="form-control" name="req_password1" value="<?php if (isset($_POST['req_password1'])) echo luna_htmlspecialchars($_POST['req_password1']); ?>" />
                            </div>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" name="req_password2" value="<?php if (isset($_POST['req_password2'])) echo luna_htmlspecialchars($_POST['req_password2']); ?>" />
                            </div>
                        </div>
                    </div>
                </div>
<?php endif; ?>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><?php echo $lang['Email'] ?><?php if ($luna_config['o_regs_verify'] == '1'): ?><span class="help-block"><?php echo $lang['Email help info'] ?></span><?php endif; ?></label>
                    <div class="col-sm-9">
                        <?php if ($luna_config['o_regs_verify'] == '1'): ?>
                        <div class="row">
                            <div class="col-sm-6">
						<?php endif; ?>
                                <input type="text" class="form-control" name="req_email1" value="<?php if (isset($_POST['req_email1'])) echo luna_htmlspecialchars($_POST['req_email1']); ?>" maxlength="80" />
                        <?php if ($luna_config['o_regs_verify'] == '1'): ?>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="req_email2" value="<?php if (isset($_POST['req_email2'])) echo luna_htmlspecialchars($_POST['req_email2']); ?>" maxlength="80" />
                            </div>
                        </div>
						<?php endif; ?>
                    </div>
                </div>
<?php

        $languages = forum_list_langs();

        // Only display the language selection box if there's more than one language available
        if (count($languages) > 1)
        {

?>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Language'] ?></label>
                    <div class="col-sm-10">
                        <select class="form-control" name="language">
<?php

            foreach ($languages as $temp)
            {
                if ($luna_config['o_default_lang'] == $temp)
                    echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'" selected="selected">'.$temp.'</option>'."\n";
                else
                    echo "\t\t\t\t\t\t\t\t".'<option value="'.$temp.'">'.$temp.'</option>'."\n";
            }

?>
                        </select>
                    </div>
                </div>
<?php

        }
?>
            </fieldset>
        </div>
    </div>
</form>

<?php

    require FORUM_ROOT.'footer.php';