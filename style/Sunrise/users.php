<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>
</div>
<div class="jumbotron" style="background:#999;">
	<div class="container">
		<h2><?php echo $lang['Users'] ?></h2>
		<?php if ($luna_user['g_search_users'] == '1'): ?>
        <div class="pull-right">
            <span class="pull-right">
                <form class="form-inline" id="userlist" method="get" action="userlist.php">
                    <select class="form-control hidden-xs" name="sort">
                        <option value="username"<?php if ($sort_by == 'username') echo ' selected="selected"' ?>><?php echo $lang['Sort username'] ?></option>
                        <option value="registered"<?php if ($sort_by == 'registered') echo ' selected="selected"' ?>><?php echo $lang['Sort registered'] ?></option>
                        <option value="num_posts"<?php if ($sort_by == 'num_posts') echo ' selected="selected"' ?>><?php echo $lang['Sort no of posts'] ?></option>
                    </select>
                    <div class="input-group">
                        <input class="form-control" type="text" name="username" value="<?php echo luna_htmlspecialchars($username) ?>" placeholder="<?php echo $lang['Search'] ?>" maxlength="25" />
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit" name="search" accesskey="s" /><span class="fa fa-search"></span></button>
                        </span>
                    </div>
                </form>
            </span>
        </div>
        <?php endif; ?>
	</div>
</div>
<div class="container">
<ul class="pagination pagination-user">
    <?php echo $paging_links ?>
</ul>

<div class="userlist row">
	<?php draw_user_list() ?>
</div>

<ul class="pagination pagination-user">
    <?php echo $paging_links ?>
</ul>