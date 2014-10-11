<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
    exit;

?>

<div class="row">
	<div class="col-sm-5">
		<h2><?php echo $lang['Users'] ?></h2>
	</div>
	<?php if ($luna_user['g_search_users'] == '1'): ?>
	<div class="col-sm-7">
		<span class="pull-right">
			<form class="form-inline" id="userlist" method="get" action="userlist.php">
				<select class="form-control" name="sort">
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

<div class="row">
    <div class="col-sm-12">
        <ul class="pagination pagination-user">
            <?php echo $paging_links ?>
        </ul>
    </div>
</div>

<div class="userlist">
	<div class="row forum-header">
		<div class="col-sm-8 col-xs-9"><?php echo $lang['Username'] ?></div>
		<div class="col-sm-1 align-center hidden-xs"><p class="text-center"><?php echo $lang['Posts table'] ?></p></div>
		<div class="col-sm-3 col-xs-3"><?php echo $lang['Registered table'] ?></div>
	</div>
	<?php draw_user_list() ?>
</div>

<div class="row">
    <div class="col-sm-12">
        <ul class="pagination pagination-user">
            <?php echo $paging_links ?>
        </ul>
    </div>
</div>