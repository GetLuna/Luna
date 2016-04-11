<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;
?>
<div class="profile-header container-fluid">
	<div class="jumbotron profile">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<h2 class="username"><?php echo $luna_user['username'] ?></h2>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="main profile container">
	<div class="row">
		<div class="col-xs-12 col-sm-3 sidebar">
			<div class="container-avatar">
				<img src="<?php echo get_avatar( $luna_user['id'] ) ?>" alt="Avatar" class="img-avatar img-center">
			</div>
			<?php load_me_nav('inbox'); ?>
		</div>
		<div class="col-xs-12 col-sm-9">
			<div class="title-block title-block-primary">
				<h2><i class="fa fa-paper-plane-o"></i> <?php _e('Inbox', 'luna') ?><span class="pull-right"><a type="button" class="btn btn-default" href="new_inbox.php?reply=<?php echo $tid ?>"><span class="fa fa-fw fa-reply"></span> <?php _e('Reply', 'luna') ?></a></span></h2>
			</div>
<?php
echo $paging_links;

draw_response_list();

echo $paging_links;
?>
			<form method="post" id="comment" action="new_inbox.php?reply=<?php echo $tid ?>" onsubmit="return process_form(this)">
                <input type="hidden" name="req_subject" placeholder="<?php _e('Subject', 'luna') ?>" value="<?php echo ($p_subject != '' ? luna_htmlspecialchars($p_subject) : ''); ?>" />
                <input type="hidden" name="form_user" value="<?php echo luna_htmlspecialchars($luna_user['username']) ?>" />
				<input type="hidden" name="reply" value="1" />
                <?php draw_editor('10'); ?>
			</form>
		</div>
	</div>
</div>