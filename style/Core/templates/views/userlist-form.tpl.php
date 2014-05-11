<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2><?php echo $lang['User list'] ?></h2>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['User list'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="userlist" class="usersearch" method="get" action="userlist.php">
            <fieldset>
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <?php if ($luna_user['g_search_users'] == '1'): ?>
                            <input class="form-control" type="text" name="username" value="<?php echo luna_htmlspecialchars($username) ?>" placeholder="<?php echo $lang['Username'] ?>" maxlength="25" />
                        <?php endif; ?>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <select class="form-control" name="show_group">
                            <option value="-1"<?php if ($show_group == -1) echo ' selected="selected"' ?>><?php echo $lang['All users'] ?></option>
<?php

$result = $db->query('SELECT g_id, g_title FROM '.$db->prefix.'groups WHERE g_id!='.FORUM_GUEST.' ORDER BY g_id') or error('Unable to fetch user group list', __FILE__, __LINE__, $db->error());

while ($cur_group = $db->fetch_assoc($result))
{
    if ($cur_group['g_id'] == $show_group)
        echo "\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'" selected="selected">'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
    else
        echo "\t\t\t\t\t\t\t".'<option value="'.$cur_group['g_id'].'">'.luna_htmlspecialchars($cur_group['g_title']).'</option>'."\n";
}

?>
                    </select>
                </div>
                <div class="col-md-2 col-sm-3">
                    <select class="form-control" name="sort_by">
                        <option value="username"<?php if ($sort_by == 'username') echo ' selected="selected"' ?>><?php echo $lang['Username'] ?></option>
                        <option value="registered"<?php if ($sort_by == 'registered') echo ' selected="selected"' ?>><?php echo $lang['Registered table'] ?></option>
                        <?php if ($show_post_count): ?>
                            <option value="num_posts"<?php if ($sort_by == 'num_posts') echo ' selected="selected"' ?>><?php echo $lang['No of posts'] ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2 col-sm-3">
                    <select class="form-control" name="sort_dir">
                        <option value="ASC"<?php if ($sort_dir == 'ASC') echo ' selected="selected"' ?>><?php echo $lang['Ascending'] ?></option>
                        <option value="DESC"<?php if ($sort_dir == 'DESC') echo ' selected="selected"' ?>><?php echo $lang['Descending'] ?></option>
                    </select>
                </div>
                <div class="col-md-1 col-sm-1">
                    <input class="btn btn-primary" type="submit" name="search" value="<?php echo $lang['Submit'] ?>" accesskey="s" />
                </div>
            </fieldset>
        </form>
    </div>
</div>