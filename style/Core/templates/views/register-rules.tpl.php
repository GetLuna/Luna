<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2><?php echo $lang['Forum rules'] ?></h2>
<form method="get" action="register.php">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Rules legend'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <div class="usercontent"><?php echo $luna_config['o_rules_message'] ?></div>
            </fieldset>
        </div>
    </div>
    <div class="alert alert-info"><input type="submit" class="btn btn-primary" name="agree" value="<?php echo $lang['Agree'] ?>" /> <input type="submit" class="btn btn-default" name="cancel" value="<?php echo $lang['Cancel'] ?>" /></div>
</form>

<?php

    require FORUM_ROOT.'footer.php';