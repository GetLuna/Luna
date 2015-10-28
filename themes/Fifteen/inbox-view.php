<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;
?>
<div class="col-sm-3 profile-nav">
	<div class="user-card-profile">
		<h3 class="user-card-title"><?php echo luna_htmlspecialchars($luna_user['username']) ?></h3>
		<span class="user-card-avatar thumbnail">
			<?php echo $avatar_user_card ?>
		</span>
	</div>
<?php
	load_me_nav('inbox');
?>
</div>
<div class="col-sm-9 profile">
	<p><span class="pages-label"><?php echo paginate($num_pages, $page, 'inbox.php?') ?></span></p>
	<div class="btn-toolbar btn-toolbar-inbox">
		<div class="btn-group pull-right">
			<a type="button" class="btn btn-success" href="new_inbox.php?reply=<?php echo $tid ?>"><span class="fa fa-fw fa-reply"></span> <?php _e('Reply', 'luna') ?></a>
		</div>
	</div>
<?php
echo $paging_links;

draw_response_list();

echo $paging_links;

?>
	<!-- <form method="post" id="comment" action="new_inbox.php?reply=<?php echo $tid ?>" onsubmit="return process_form(this)">
	<?php draw_editor('10'); ?>
	</form> -->
</div>