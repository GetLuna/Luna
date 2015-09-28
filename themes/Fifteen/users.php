<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<nav class="navbar navbar-default" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#users-nav">
			<span class="sr-only"><?php _e('Toggle navigation', 'luna') ?></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a href="userlist.php" class="navbar-brand"><span class="fa fa-fw fa-users"></span> <?php _e('Users', 'luna') ?></a>
	</div>
	<div class="collapse navbar-collapse" id="users-nav">
		<form class="navbar-form navbar-right" id="userlist" method="get" action="userlist.php">
			<div class="form-group">
				<select class="form-control hidden-xs" name="sort">
					<option value="username"<?php if ($sort_by == 'username') echo ' selected' ?>><?php _e('Sort by username', 'luna') ?></option>
					<option value="registered"<?php if ($sort_by == 'registered') echo ' selected' ?>><?php _e('Sort by registration date', 'luna') ?></option>
					<option value="num_posts"<?php if ($sort_by == 'num_posts') echo ' selected' ?>><?php _e('Sort by number of comments', 'luna') ?></option>
				</select>
			</div>
			<div class="form-group">
				<div class="input-group">
					<input class="form-control" type="text" name="username" value="<?php echo luna_htmlspecialchars($username) ?>" placeholder="<?php _e('Search', 'luna') ?>" maxlength="25" />
					<span class="input-group-btn">
						<button class="btn btn-default btn-search" type="submit" name="search" accesskey="s" /><span class="fa fa-fw fa-search"></span></button>
					</span>
				</div>
			</div>
		</form>
	</div>
</nav>
<?php echo $paging_links ?>

<div class="userlist row">
	<?php draw_user_list() ?>
</div>

<?php echo $paging_links ?>