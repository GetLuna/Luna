<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Upload avatar'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="upload_avatar" method="post" enctype="multipart/form-data" action="profile.php?action=upload_avatar2&amp;id=<?php echo $id ?>" onsubmit="return process_form(this)">
            <fieldset>
                <input type="hidden" name="form_sent" value="1" />
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $luna_config['o_avatars_size'] ?>" />
                <label><strong><?php echo $lang['File'] ?></strong><br /><input name="req_file" type="file" /></label>
                <span class="help-block"><?php echo $lang['Avatar desc'].' '.$luna_config['o_avatars_width'].' x '.$luna_config['o_avatars_height'].' '.$lang['pixels'].' '.$lang['and'].' '.forum_number_format($luna_config['o_avatars_size']).' '.$lang['bytes'].' ('.file_size($luna_config['o_avatars_size']).').' ?></span>
            </fieldset>
            <input type="submit" class="btn btn-primary" name="upload" value="<?php echo $lang['Upload'] ?>" /> <a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a>
        </form>
    </div>
</div>
<?php

    require FORUM_ROOT.'footer.php';