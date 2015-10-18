<div class="row user-row">
	<div class="col-sm-8 col-xs-9">
		<span class="user-avatar">
			<?php echo $user_avatar; ?>
		</span>
		<span class="userlist-name"><?php echo '<a href="profile.php?id='.$user_data['id'].'">'.luna_htmlspecialchars($user_data['username']).'</a>' ?> <small><?php echo $user_title_field ?></small></span>
	</div>
	<div class="col-sm-1 collum-count align-center hidden-xs"><p class="text-center"><?php echo forum_number_format($user_data['num_comments']) ?></p></div>
	<div class="col-sm-3 col-xs-3 collum-count"><?php echo format_time($user_data['registered'], true) ?></div>
</div>