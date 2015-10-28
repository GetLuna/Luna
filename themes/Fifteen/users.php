<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
</div>
<div class="jumbotron">
	<div class="container">
		<h2 class="forum-title"><span class="fa fa-fw fa-users"></span> <?php _e('Users', 'luna') ?></h2>
		<span class="pull-right naviton">
			<form class="navbar-form navbar-right" id="userlist" method="get" action="userlist.php">
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
							<button class="btn btn-default btn-search" type="submit" name="search" accesskey="s" /><span class="fa fa-fw fa-search"></span></button>
						</span>
					</div>
				</div>
			</form>
		</span>
	</div>
</div>
<div class="container">
	<div class="row pagination-row">
		<div class="col-xs-12">
			<?php echo $paging_links ?>
		</div>
	</div>
	<div class="userlist row">
		<?php draw_user_list() ?>
	</div>
	<div class="row pagination-row">
		<div class="col-xs-12">
			<?php echo $paging_links ?>
		</div>
	</div>
