<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="btn-group btn-breadcrumb">
    <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-home"></span></a>
    <a class="btn btn-primary" href="viewforum.php?id=<?php echo $cur_post['fid'] ?>"><?php echo luna_htmlspecialchars($cur_post['forum_name']) ?></a>
    <a class="btn btn-primary" href="viewtopic.php?pid=<?php echo $id ?>#p<?php echo $id ?>"><?php echo luna_htmlspecialchars($cur_post['subject']) ?></a>
    <a class="btn btn-primary" href="#"><?php echo $lang['Report post'] ?></a>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['Reason desc'] ?></h3>
    </div>
    <form id="report" method="post" action="misc.php?report=<?php echo $post_id ?>" onsubmit="this.submit.disabled=true;if(process_form(this)){return true;}else{this.submit.disabled=false;return false;}">
        <fieldset>
            <input type="hidden" name="form_sent" value="1" />
            <textarea class="form-control" name="req_reason" rows="5"></textarea>
        </fieldset>
        <div class="panel-footer">
            <input type="submit" class="btn btn-primary" name="submit" value="<?php echo $lang['Submit'] ?>" accesskey="s" /><a class="btn btn-link" href="javascript:history.go(-1)"><?php echo $lang['Go back'] ?></a>
        </div>
    </form>
</div>

<?php

    require FORUM_ROOT.'footer.php';