<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2><?php echo $lang['Moderate'] ?></h2>
<form class="form-horizontal" method="post" action="moderate.php?fid=<?php echo $fid ?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo ($action == 'single') ? $lang['Move topic'] : $lang['Move topics'] ?></h3>
        </div>
        <div class="panel-body">
            <input type="hidden" name="topics" value="<?php echo $topics ?>" />
            <fieldset>
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

        if ($cur_forum['fid'] != $fid)
            echo "\t\t\t\t\t\t\t\t".'<option value="'.$cur_forum['fid'].'">'.luna_htmlspecialchars($cur_forum['forum_name']).'</option>'."\n";
    }

?>
                            </optgroup>
                        </select>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="with_redirect" value="1"<?php if ($action == 'single') echo ' checked' ?> />
                                <?php echo $lang['Leave redirect'] ?>
                            </label>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" name="move_topics_to" value="<?php echo $lang['Move'] ?>" />
        </div>
    </div>
</form>

<?php

    require load_page('footer.php');