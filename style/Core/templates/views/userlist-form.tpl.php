<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<h2><?php echo $lang['Users'] ?></h2>
<?php if ($luna_user['g_search_users'] == '1'): ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $lang['User list'] ?></h3>
    </div>
    <div class="panel-body">
        <form id="userlist" class="usersearch" method="get" action="userlist.php">
            <fieldset>
                <div class="row search-bar">
                    <div class="col-sm-12">
                        <div class="input-group"><input class="form-control" type="text" name="username" value="<?php echo luna_htmlspecialchars($username) ?>" placeholder="<?php echo $lang['Username'] ?>" maxlength="25" /><span class="input-group-btn"><button class="btn btn-primary" type="submit" name="search" accesskey="s" /><span class="fa fa-search"></span></button></span></div>
                    </div>
                </div>
                <div class="row hidden-sm hidden-xs">
                    <div class="col-md-4">
                        <select class="form-control" name="sort">
                            <option value="username"<?php if ($sort_by == 'username') echo ' selected="selected"' ?>><?php echo $lang['Username'] ?></option>
                            <option value="registered"<?php if ($sort_by == 'registered') echo ' selected="selected"' ?>><?php echo $lang['Registered table'] ?></option>
							<option value="num_posts"<?php if ($sort_by == 'num_posts') echo ' selected="selected"' ?>><?php echo $lang['No of posts'] ?></option>
                        </select>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<?php endif; ?>