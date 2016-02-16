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
					<h2 class="username"><?php echo $user['username'] ?></h2>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="main profile container">
	<div class="row">
		<div class="col-xs-12 col-sm-3 sidebar">
			<div class="container-avatar">
				<img src="<?php echo get_avatar( $user['id'] ) ?>" alt="Avatar" class="img-avatar img-center">
			</div>
			<?php load_me_nav('profile'); ?>
		</div>
		<div class="col-xs-12 col-sm-9">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php _e('About user', 'luna') ?></h3>
				</div>
				<div class="panel-body">
					<?php echo implode("\n\t\t\t\t\t\t\t".'<br />', $user_personality)."\n" ?>
				</div>
			</div>
		<?php if (!empty($user_messaging)): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php _e('Contact', 'luna'); ?></h3>
				</div>
				<div class="panel-body">
					<?php echo implode("\n\t\t\t\t\t\t\t".'<br />', $user_messaging)."\n" ?>
				</div>
			</div>
		<?php
		endif;
		
		if ($luna_config['o_signatures'] == '1') {
			if (isset($parsed_signature)) {
		?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php _e('Signature', 'luna'); ?></h3>
				</div>
				<div class="panel-body">
					<?php echo $user_signature ?>
				</div>
			</div>
		<?php
			}
		}
		?>
		</div>
	</div>
</div>