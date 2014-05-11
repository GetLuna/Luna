<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<form class="form" id="login" method="post" action="login.php?action=in" onsubmit="return process_form(this)">
    <fieldset>
        <h1 class="form-heading"><?php echo $lang['Login'] ?></h1>
        <input type="hidden" name="form_sent" value="1" />
        <input type="hidden" name="redirect_url" value="<?php echo luna_htmlspecialchars($redirect_url) ?>" />
        <div>
            <input class="form-control top-form" type="text" name="req_username" maxlength="25" tabindex="1" placeholder="<?php echo $lang['Username'] ?>" />
            <input class="form-control bottom-form" type="password" name="req_password" tabindex="2" placeholder="<?php echo $lang['Password'] ?>" />
        </div>
        <div class="form-content">
            <p class="actions"><?php if ($luna_config['o_regs_allow'] == '1') { ?><a href="register.php" tabindex="5"><?php echo $lang['Register'] ?></a> &middot; <?php }; ?><a href="login.php?action=forget" tabindex="6"><?php echo $lang['Forgotten pass'] ?></a></p>
            <div class="control-group">
                <div class="controls remember">
                    <label class="remember"><input type="checkbox" name="save_pass" value="1" tabindex="3" checked="checked" /> <?php echo $lang['Remember me'] ?></label>
                </div>
            </div>
            <div class="control-group pull-right">
                <input class="btn btn-primary" type="submit" name="login" value="<?php echo $lang['Login'] ?>" tabindex="4" />
            </div>
        </div>
    </fieldset>
</form>

<?php

    require FORUM_ROOT.'footer.php';