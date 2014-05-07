<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<form method="post" action="moderate.php?fid=<?php echo $fid ?>">
    <h2><?php echo $lang['Moderate'] ?></h2>
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Delete topics'] ?></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="topics" value="<?php echo implode(',', array_map('intval', array_keys($topics))) ?>" />
            <fieldset>
                <p><?php echo $lang['Delete topics comply'] ?></p>
            </fieldset>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-danger" name="delete_topics_comply" value="<?php echo $lang['Delete'] ?>" /><a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a>
        </div>
    </div>
</form>

<?php

    require FORUM_ROOT.'footer.php';