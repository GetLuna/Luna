<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<h2 class="profile-title"><?php _e('Users', 'luna') ?></h2>
<form class="form-inline" id="userlist" method="get" action="userlist.php">
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="form-group">
				<select class="form-control hidden-xs" name="sort">
					<option value="username"<?php if ($sort_by == 'username') echo ' selected' ?>><?php _e('Sort by username', 'luna') ?></option>
					<option value="registered"<?php if ($sort_by == 'registered') echo ' selected' ?>><?php _e('Sort by registration date', 'luna') ?></option>
					<option value="num_comments"<?php if ($sort_by == 'num_comments') echo ' selected' ?>><?php _e('Sort by number of comments', 'luna') ?></option>
				</select>
			</div>
			<div class="form-group">
				<div class="input-group">
					<input class="form-control" type="text" name="username" value="<?php echo luna_htmlspecialchars($username) ?>" placeholder="<?php _e('Search', 'luna') ?>" maxlength="25" />
					<span class="input-group-btn">
						<button class="btn btn-primary btn-search" type="submit" name="search" accesskey="s" /><span class="fa fa-fw fa-search"></span></button>
					</span>
				</div>
			</div>
		</div>
	</div>
</form>
<?php echo $paging_links ?>

<div class="panel panel-default panel-board">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e('Users', 'luna') ?></h3>
	</div>
	<div class="panel-body">
		<?php draw_user_list() ?>
	</div>
</div>

<?php echo $paging_links ?>