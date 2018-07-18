<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

include(LUNA_ROOT.'themes/Typography/functions.php');

?>
<div class="main profile container">
	<div class="jumbotron profile">
		<div class="row">
			<div class="col">
				<h4><?php echo $user['username'] ?></h4>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3 col-12 sidebar">
			<div class="container-avatar d-none d-md-block">
				<img src="<?php echo get_avatar( $user['id'] ) ?>" alt="Avatar" class="avatar">
			</div>
			<?php load_me_nav('inbox'); ?>
		</div>
		<div class="col-12 col-md-9">
			<div class="title-block title-block-primary">
				<h2><i class="fas fa-fw fa-paper-plane"></i> <?php _e('Inbox', 'luna') ?><span class="float-right"><a type="button" class="btn btn-default" href="new_inbox.php?reply=<?php echo $tid ?>"><span class="fas fa-fw fa-reply"></span> <?php _e('Reply', 'luna') ?></a></span></h2>
			</div>
			<?php typography_paginate($paging_links) ?>
			<?php draw_response_list() ?>
			<?php typography_paginate($paging_links) ?>
			<a type="button" class="btn btn-primary btn-lg btn-block btn-bottom" href="new_inbox.php?reply=<?php echo $tid ?>"><span class="fas fa-fw fa-reply"></span> <?php _e('Reply', 'luna') ?></a>
		</div>
	</div>
</div>