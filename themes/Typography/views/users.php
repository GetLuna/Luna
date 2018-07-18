<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

include(LUNA_ROOT.'themes/Typography/functions.php');
?>
<div class="main container">
	<div class="row">
		<div class="col-12">
			<div class="tab-set">
				<div class="title-block title-block-primary">
					<h2><i class="fas fa-fw fa-users"></i> <?php _e('Users', 'luna') ?></h2>
				</div>
				<div class="tab-content tab-content-fix">
					<?php if ($luna_user['g_search_users'] == '1') { ?>
						<form class="row tab-row" id="userlist" method="get" action="userlist.php">
							<div class="col-6">
								<select class="form-control d-none d-md-block" name="sort">
									<option value="username"<?php if ($sort_by == 'username') echo ' selected' ?>><?php _e('Sort by username', 'luna') ?></option>
									<option value="registered"<?php if ($sort_by == 'registered') echo ' selected' ?>><?php _e('Sort by registration date', 'luna') ?></option>
									<option value="num_comments"<?php if ($sort_by == 'num_comments') echo ' selected' ?>><?php _e('Sort by number of comments', 'luna') ?></option>
								</select>
							</div>
							<div class="col-6">
								<div class="input-group">
									<input class="form-control" type="text" name="username" value="<?php echo luna_htmlspecialchars($username) ?>" placeholder="<?php _e('Search', 'luna') ?>" maxlength="25" />
									<div class="input-group-append">
										<button class="btn btn-primary" type="submit" name="search" accesskey="s"><span class="fas fa-fw fa-search"></span></button>
									</div>
								</div>
							</div>
						</form>
					<?php } ?>
					<hr />
					<?php typography_paginate($paging_links) ?>
					<div class="userlist row tab-row">
						<?php draw_user_list() ?>
					</div>
					<?php typography_paginate($paging_links) ?>
				</div>
			</div>
		</div>
	</div>
</div>