<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
	<div class="user-entry">
		<div class="media">
			<a class="pull-left" href="<?php echo 'profile.php?id='.$user_data['id'] ?>">
				<?php echo $user_avatar; ?>
			</a>
			<div class="media-body">
				<h2 class="media-heading"><?php echo '<a href="profile.php?id='.$user_data['id'].'">'.luna_htmlspecialchars($user_data['username']).'</a>' ?></h2>
				<h4><?php echo $user_title_field ?></h4>
				<?php echo forum_number_format($user_data['num_posts']) ?> posts since <?php echo format_time($user_data['registered'], true) ?>
			</div>
		</div>
	</div>
</div>