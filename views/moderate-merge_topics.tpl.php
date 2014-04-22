<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2><?php echo $lang['Moderate'] ?></h2>
<form method="post" action="moderate.php?fid=<?php echo $fid ?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Merge topics'] ?></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="topics" value="<?php echo implode(',', array_map('intval', array_keys($topics))) ?>" />
            <fieldset>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="with_redirect" value="1" />
                        <?php echo $lang['Leave redirect'] ?>
                    </label>
                </div>
            </fieldset>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" name="merge_topics_comply" value="<?php echo $lang['Merge'] ?>" /><a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a>
        </div>
    </div>
</form>

<?php

    require FORUM_ROOT.'footer.php';