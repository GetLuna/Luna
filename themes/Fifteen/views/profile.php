<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<div class="jumbotron profile">
	<div class="container">
		<div class="row">
			<div class="col">
                <h4><?php echo $user['username'] ?></h4>
            </div>
        </div>
    </div>
</div>
<div class="main profile container">
	<div class="row">
		<div class="col-md-3 col-12 sidebar">
			<div class="container-avatar d-none d-md-block">
				<img src="<?php echo get_avatar( $user['id'] ) ?>" alt="Avatar" class="avatar">
			</div>
			<?php load_me_nav('profile'); ?>
		</div>
		<div class="col-xs-12 col-sm-9">
            <div class="tab-set">
                <div class="title-block title-block-primary">
                    <h2><i class="fas fa-fw fa-user"></i> <?php echo luna_htmlspecialchars($user['username']) ?></h2>
                </div>
                <div class="tab-content tab-about">
                    <div class="row">
                        <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                            <small><?php _e('Title', 'luna') ?></small>
                            <?php echo get_title($user) ?>
                        </h3>
                        <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                            <small><?php _e('Comments', 'luna') ?></small>
                            <?php echo forum_number_format($user['num_comments']) ?>
                        </h3>
                        <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                            <small><?php _e('Latest comment', 'luna') ?></small>
                            <?php echo $last_comment ?>
                        </h3>
                        <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                            <small><?php _e('Registered since', 'luna') ?></small>
                            <?php echo format_time($user['registered'], true) ?>
                        </h3>
                        <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                            <small><?php _e('Latest visit', 'luna') ?></small>
                            <?php echo format_time($user['last_visit'], true) ?>
                        </h3>
                        <?php if ($user['realname'] != '') { ?>
                        <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                            <small><?php _e('Real name', 'luna') ?></small>
                            <?php echo $user['realname'] ?>
                        </h3>
                        <?php } if ($user['location'] != '') { ?>
                        <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                            <small><?php _e('Location', 'luna') ?></small>
                            <?php echo $user['location'] ?>
                        </h3>
                        <?php } ?>
                    </div>
                </div>
                <div class="tab-footer">
                    <div class="row">
                        <?php echo $user_activities ?>
                    </div>
                </div>
            </div>
            <?php if (!empty($user_messaging) || (($user['email_setting'] != '0' && ($luna_user['g_send_email'] == '1')))): ?>
                <div class="tab-set">
                    <div class="title-block title-block-primary">
                        <h2><i class="fas fa-fw fa-paper-plane"></i> <?php _e('Contact', 'luna') ?><?php if ($user['email_setting'] == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1') { echo '<span class="float-right"><a class="btn btn-default" href="misc.php?email='.$id.'"><span class="fas fa-fw fa-send-o"></span> '.__('Send email', 'luna').'</a></span>'; } ?></h2>
                    </div>
                    <?php if (!empty($user_messaging)): ?>
                        <div class="tab-content tab-contact">
                            <?php echo implode('', $user_messaging) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php
            endif;

            if ($luna_config['o_signatures'] == '1' && isset($parsed_signature)) {
            ?>
                <div class="tab-set">
                    <div class="title-block title-block-primary">
                        <h2><i class="fas fa-fw fa-signature"></i> <?php _e('Signature', 'luna') ?></h2>
                    </div>
                    <div class="tab-content">
                        <?php echo $user_signature ?>
                    </div>
                </div>
            <?php } ?>
		</div>
	</div>
</div>