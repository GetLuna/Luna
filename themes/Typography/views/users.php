<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

include(LUNA_ROOT.'themes/Typography/functions.php');
?>
<div class="main container">
	<div class="jumbotron default">
		<h2><?php _e('Users', 'luna') ?></h2>
	</div>
	<?php if ($luna_user['g_search_users'] == '1') { ?>
		<form class="row" id="userlist" method="get" action="userlist.php">
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
	<div class="userlist row">
		<?php if ( count( $users ) > 0 ) { ?>
			<?php foreach( $users as $user ) { ?>
				<div class="col-xl-4 col-lg-6 col-md-6 col-12">
					<div class="user-entry">
						<div class="media">
							<a href="<?php echo 'profile.php?id='.$user->getId() ?>">
								<img class="img-fluid" src="<?php echo $user->getAvatar() ?>" alt="">
							</a>
							<div class="media-body">
								<h5 class="mt-0 mb-0">
									<a title="<?php echo $user->getUsername() ?>" href="profile.php?id=<?php echo $user->getId() ?>"><?php echo $user->getUsername() ?></a>
								</h5>
								<h6><?php echo $user->getTitle() ?></h6>
								<?php echo $user->getNumComments().' '._n('comment since', 'comments since', $user->getNumComments(), 'luna').' '.$user->getRegistered(); ?>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } else { ?>
			<h3><?php _e('Your search returned no hits.', 'luna') ?></h3>
		<?php } ?>
	</div>
	<?php typography_paginate($paging_links) ?>
</div>