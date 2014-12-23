<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<form id="subject" class="form-horizontal" method="post" action="moderate.php?fid=<?php echo $fid ?>&amp;tid=<?php echo $tid ?>">
<h2><?php echo $lang['Moderate'] ?></h2>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo $lang['Split posts'] ?></h3>
        </div>
        <div class="panel-body">
            <fieldset>
                <input type="hidden" class="form-control" name="posts" value="<?php echo implode(',', array_map('intval', array_keys($posts))) ?>" />
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['Move to'] ?></label>
                    <div class="col-sm-10">
                        <select class="form-control" name="move_to_forum">
<?php

    $cur_category = 0;
    while ($cur_forum = $db->fetch_assoc($result)) {
        if ($cur_forum['cid'] != $cur_category) { // A new category since last iteration?
            if ($cur_category)
                echo "\t\t\t\t\t\t\t".'</optgroup>'."\n";

            echo "\t\t\t\t\t\t\t".'<optgroup label="'.luna_htmlspecialchars($cur_forum['cat_name']).'">'."\n";
            $cur_category = $cur_forum['cid'];
        }

        echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_forum['fid'].'"'.($fid == $cur_forum['fid'] ? ' selected' : '').'>'.luna_htmlspecialchars($cur_forum['forum_name']).'</option>'."\n";
    }

?>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo $lang['New subject'] ?></label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="new_subject" maxlength="70" />
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" name="split_posts_comply" value="<?php echo $lang['Split'] ?>" />
        </div>
    </div>
</form>

<?php

    require load_page('footer.php');