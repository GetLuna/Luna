<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<div class="main profile container">
	<div class="jumbotron default">
		<h2><?php echo $user->getUsername() ?></h2>
	</div>
	<div class="row">
		<div class="col-md-3 col-12 sidebar">
			<div class="container-avatar d-none d-md-block">
				<img src="<?php echo $user->getAvatar() ?>" alt="Avatar" class="avatar">
			</div>
			<?php load_me_nav('profile'); ?>
		</div>
		<div class="col-12 col-sm-9">
            <h2><i class="fas fa-fw fa-user"></i> <?php echo $user->getUsername() ?></h2>
            <div class="row">
                <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                    <small><?php _e('Title', 'luna') ?></small>
                    <?php echo $user->getTitle() ?>
                </h3>
                <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                    <small><?php _e('Comments', 'luna') ?></small>
                    <?php echo $user->getNumComments() ?>
                </h3>
                <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                    <small><?php _e('Latest comment', 'luna') ?></small>
                    <?php echo $user->getLastComment() ?>
                </h3>
                <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                    <small><?php _e('Registered since', 'luna') ?></small>
                    <?php echo $user->getRegistered() ?>
                </h3>
                <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                    <small><?php _e('Latest visit', 'luna') ?></small>
                    <?php echo $user->getLastVisit() ?>
                </h3>
                <?php if ($user->getRealname() != '') { ?>
                <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                    <small><?php _e('Real name', 'luna') ?></small>
                    <?php echo $user->getRealname() ?>
                </h3>
                <?php } if ($user->getLocation() != '') { ?>
                    <h3 class="col-xl-3 col-lg-4 col-md-6 user-stat text-center">
                        <small><?php _e('Location', 'luna') ?></small>
                        <?php echo $user->getLocation() ?>
                    </h3>
                <?php } ?>
            </div>
            <h2><i class="fas fa-fw fa-chart-line"></i> <?php _e('Activity', 'luna') ?></h2>
            <a class="btn btn-primary" href="<?php echo $user->getThreadsUrl() ?>"><i class="fas fa-fw fa-stream"></i> <?php _e('Threads', 'luna') ?></a>
            <a class="btn btn-primary" href="<?php echo $user->getCommentsUrl() ?>"><i class="fas fa-fw fa-comment"></i> <?php _e('Comments', 'luna') ?></a>
            <?php if (($luna_user['is_admmod'] || $luna_user['id'] == $id) && $luna_config['o_thread_subscriptions'] == '1') { ?>
                <a class="btn btn-primary" href="<?php echo $user->getSubscriptionsUrl() ?>"><i class="fas fa-fw fa-star"></i> <?php _e('Subscriptions', 'luna') ?></a>
            <?php } ?>
            <h2><i class="fas fa-fw fa-paper-plane"></i> <?php _e('Contact', 'luna') ?></h2>
            <?php if ($user->getEmailSetting() == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1') { ?>
                <a class="btn btn-primary" href="misc.php?email=<?php echo $user->getId() ?>">
                    <i class="fas fa-fw fa-at"></i> <?php _e('Send email', 'luna') ?>
                </a>
            <?php } if ( $user->hasContactInfo() ) { ?>
                <div class="row row-contacts">
                    <?php if ( $user->getEmailSetting() == '0' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1' ) { ?>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="card card-email">
                                <div class="card-body">
                                    <h2><i class="fas fa-fw fa-at"></i></h2>
                                    <?php echo $user->getEmail() ?>
                                </div>
                            </div>
                        </div>
                    <?php } if ( $user->getUrl() ) { ?>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="card card-url">
                                <div class="card-body">
                                    <h2><i class="fas fa-fw fa-link"></i></h2>
                                    <?php echo $user->getUrl() ?>
                                </div>
                            </div>
                        </div>
                    <?php } if ( $user->getTwitter() ) { ?>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="card card-twitter">
                                <div class="card-body">
                                    <h2><i class="fab fa-fw fa-twitter"></i></h2>
                                    <?php echo $user->getTwitter() ?>
                                </div>
                            </div>
                        </div>
                    <?php } if ( $user->getMicrosoft() ) { ?>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="card card-microsoft">
                                <div class="card-body">
                                    <h2><i class="fab fa-fw fa-microsoft"></i></h2>
                                    <?php echo $user->getMicrosoft() ?>
                                </div>
                            </div>
                        </div>
                    <?php } if ( $user->getFacebook() ) { ?>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="card card-facebook">
                                <div class="card-body">
                                    <h2><i class="fab fa-fw fa-facebook"></i></h2>
                                    <?php echo $user->getFacebook() ?>
                                </div>
                            </div>
                        </div>
                    <?php } if ( $user->getGoogle() ) { ?>
                        <div class="col-12 col-sm-6 col-md-4">
                            <div class="card card-google">
                                <div class="card-body">
                                    <h2><i class="fab fa-fw fa-google-plus-g"></i></h2>
                                    <?php echo $user->getGoogle() ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } if ($luna_config['o_signatures'] == '1' && $user->getSignature() != '') { ?>
                <h2><i class="fas fa-fw fa-signature"></i> <?php _e('Signature', 'luna') ?></h2>
                <div class="card">
                    <div class="card-body">
                        <?php echo $user->getSignature( true ) ?>
                    </div>
                </div>
            <?php } ?>
		</div>
	</div>
</div>