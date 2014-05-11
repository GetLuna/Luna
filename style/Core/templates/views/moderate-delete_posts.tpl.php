<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2><?php echo $lang['Moderate'] ?></h2>
<form method="post" action="moderate.php?fid=<?php echo $fid ?>&amp;tid=<?php echo $tid ?>">
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Delete posts'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <input type="hidden" name="posts" value="<?php echo implode(',', array_map('intval', array_keys($posts))) ?>" />
                <p><?php echo $lang['Delete posts comply'] ?></p>
            </fieldset>
        </div>
        <div class="panel-footer">
            <input class="btn btn-primary" type="submit" name="delete_posts_comply" value="<?php echo $lang['Delete'] ?>" /> <a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a>
        </div>
    </div>
</form>

<?php

    require FORUM_ROOT.'footer.php';