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
            <div class="title-block title-block-primary">
                <h2><i class="fa fa-fw fa-user"></i> <?php echo luna_htmlspecialchars($user['username']) ?></h2>
            </div>
            <div class="tab-content">
                <?php echo implode("\n\t\t\t\t\t\t\t".'<br />', $user_personality)."\n" ?>
            </div>
            <?php if (!empty($user_messaging)): ?>
                <div class="title-block title-block-primary">
                    <h2><i class="fa fa-fw fa-paper-plane-o"></i> <?php _e('Contact', 'luna') ?></h2>
                </div>
                <div class="tab-content">
                    <?php echo implode("\n\t\t\t\t\t\t\t".'<br />', $user_messaging)."\n" ?>
                </div>
            <?php
            endif;

            if ($luna_config['o_signatures'] == '1' && isset($parsed_signature)) {
            ?>
                <div class="title-block title-block-primary">
                    <h2><i class="fa fa-fw fa-map-signs"></i> <?php _e('Signature', 'luna') ?></h2>
                </div>
                <div class="tab-content">
                    <?php echo $user_signature ?>
                </div>
            <?php } ?>
		</div>
	</div>
</div>