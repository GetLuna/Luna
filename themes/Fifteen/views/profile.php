<?php

// Make sure no one attempts to run this view directly.
if (!defined('FORUM'))
	exit;

?>
<div class="jumbotron profile">
	<div class="container">
		<div class="row">
			<div class="col">
                <h4><?php echo $user->getUsername() ?></h4>
            </div>
        </div>
    </div>
</div>
<div class="main profile container">
	<div class="row">
		<div class="col-md-3 col-12 sidebar">
			<div class="container-avatar d-none d-md-block">
				<img src="<?php echo $user->getAvatar() ?>" alt="Avatar" class="avatar">
			</div>
			<?php load_me_nav('profile'); ?>
		</div>
		<div class="col-xs-12 col-sm-9">
            <div class="tab-set">
                <div class="title-block title-block-primary">
                    <h2><i class="fas fa-fw fa-user"></i> <?php echo $user->getUsername() ?></h2>
                </div>
                <div class="tab-content tab-about">
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
                </div>
                <div class="tab-footer">
                    <div class="row">
                        <a class="btn btn-light col" href="<?php echo $user->getThreadsUrl() ?>"><i class="fas fa-fw fa-stream"></i> <?php _e('Threads', 'luna') ?></a>
                        <a class="btn btn-light col" href="<?php echo $user->getCommentsUrl() ?>"><i class="fas fa-fw fa-comment"></i> <?php _e('Comments', 'luna') ?></a>
                        <?php if (($luna_user['is_admmod'] || $luna_user['id'] == $id) && $luna_config['o_thread_subscriptions'] == '1') { ?>
                            <a class="btn btn-light col" href="<?php echo $user->getSubscriptionsUrl() ?>"><i class="fas fa-fw fa-star"></i> <?php _e('Subscriptions', 'luna') ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="tab-set">
                <div class="title-block title-block-primary">
                    <h2><i class="fas fa-fw fa-paper-plane"></i> <?php _e('Contact', 'luna') ?><?php if ($user->getEmailSetting() == '1' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1') { echo '<span class="float-right"><a class="btn btn-light" href="misc.php?email='.$user->getId().'"><span class="fas fa-fw fa-at"></span> '.__('Send email', 'luna').'</a></span>'; } ?></h2>
                </div>
                <?php if ( $user->hasContactInfo() ) { ?>
                    <div class="tab-content tab-contact">
                        <div class="row row-contacts">
                            <?php if ( $user->getEmailSetting() == '0' && !$luna_user['is_guest'] && $luna_user['g_send_email'] == '1' ) { ?>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <a href="mailto:<?php echo $user->getEmail() ?>" class="input-group-text" id="mail-addon"><i class="fas fa-fw fa-envelope"></i></a>
                                    </div>
                                    <input type="text" class="form-control" value="<?php echo $user->getEmail() ?>" aria-describedby="mail-addon" readonly="readonly" />
                                </div>
                            <?php } if ( $user->getUrl() ) { ?>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <a href="<?php echo $user->getUrl() ?>" class="input-group-text" id="website-addon"><i class="fas fa-fw fa-link"></i></a>
                                    </div>
                                    <input type="text" class="form-control" value="<?php echo $user->getUrl() ?>" aria-describedby="website-addon" readonly="readonly" />
                                </div>
                            <?php } if ( $user->getTwitter() ) { ?>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <a href="http://twitter.com/<?php echo $user->getTwitter() ?>" class="input-group-text" id="twitter-addon"><i class="fab fa-fw fa-twitter"></i></a>
                                    </div>
                                    <input type="text" class="form-control" value="<?php echo $user->getTwitter() ?>" aria-describedby="twitter-addon" readonly="readonly" />
                                </div>
                            <?php } if ( $user->getMicrosoft() ) { ?>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <a href="mailto:<?php echo $user->getMicrosoft() ?>" class="input-group-text" id="microsoft-addon"><i class="fab fa-fw fa-microsoft"></i></a>
                                    </div>
                                    <input type="text" class="form-control" value="<?php echo $user->getMicrosoft() ?>" aria-describedby="microsoft-addon" readonly="readonly" />
                                </div>
                            <?php } if ( $user->getFacebook() ) { ?>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <a href="http://facebook.com/<?php echo $user->getFacebook() ?>" class="input-group-text" id="facebook-addon"><i class="fab fa-fw fa-facebook"></i></a>
                                    </div>
                                    <input type="text" class="form-control" value="<?php echo $user->getFacebook() ?>" aria-describedby="facebook-addon" readonly="readonly" />
                                </div>
                            <?php } if ( $user->getGoogle() ) { ?>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <a href="http://plus.google.com/<?php echo $user->getGoogle() ?>" class="input-group-text" id="google-addon"><i class="fab fa-fw fa-google-plus-g"></i></a>
                                    </div>
                                    <input type="text" class="form-control" value="<?php echo $user->getGoogle() ?>" aria-describedby="google-addon" readonly="readonly" />
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php if ($luna_config['o_signatures'] == '1' && $user->getSignature() != '') { ?>
                <div class="tab-set">
                    <div class="title-block title-block-primary">
                        <h2><i class="fas fa-fw fa-signature"></i> <?php _e('Signature', 'luna') ?></h2>
                    </div>
                    <div class="tab-content">
                        <?php echo $user->getSignature( true ) ?>
                    </div>
                </div>
            <?php } ?>
		</div>
	</div>
</div>