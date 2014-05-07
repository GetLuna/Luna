<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<form id="search" method="get" action="search.php?section=simple">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Search criteria legend'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <input type="hidden" name="action" value="search" />
                <div class="input-group"><input class="form-control" type="text" name="keywords" maxlength="100" /><span class="input-group-btn"><input class="btn btn-primary" type="submit" name="search" value="<?php echo $lang['Search'] ?>" accesskey="s" /></span></div>
                <?php if ($luna_config['o_enable_advanced_search'] == 1) { ?>
                <a class="hidden-xs" href="search.php?section=advanced"><?php echo $lang['Advanced search'] ?></a>
                <?php } ?>
            </fieldset>
        </div>
    </div>
</form>

<?php

    require FORUM_ROOT.'footer.php';