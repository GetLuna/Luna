<!DOCTYPE html>
<html class="<?php echo get_theme_mode() ?>">
	<head>
		<?php load_meta(); ?>
		<link rel="stylesheet" type="text/css" href="vendor/css/bootstrap.min.css">
        <?php if ($luna_config['o_fontawesomepro'] == 0) { ?>
            <?php if ($luna_config['o_use_cdn']) { ?>
                <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
            <?php } else { ?>
                <link rel="stylesheet" href="../vendor/css/fontawesome-all.min.css">
            <?php } ?>
        <?php } else { ?>
		    <link rel="stylesheet" href="vendor/fontawesome/css/fontawesome-all.min.css">
        <?php }?>
		<link rel="stylesheet" type="text/css" href="vendor/css/prism.css">
		<?php load_css(); ?>
		<script src="vendor/js/jquery.min.js"></script>
		<script src="vendor/js/bootstrap.bundle.min.js"></script>
		<script src="vendor/js/prism.js"></script>
        <?php
        if ($luna_config['o_use_custom_css']) {
            echo '<style>'.$luna_config['o_custom_css'].'</style>';
        }
        if (($luna_config['o_cookie_bar'] == 1) && ($luna_user['is_guest']) && (!isset($_COOKIE['LunaCookieBar']))) {
            echo '<style>';
			echo 'body { margin-bottom: 60px; }';
			echo '@media screen and (max-width: 767px) { body { margin-bottom: 80px; } }';
            echo '</style>';
        }
        ?>
        <link rel="icon" href="img/favicon.png" />
	</head>
	<body>
		<?php if ($luna_user['is_guest']): require load_page('login.php'); endif; ?>
        <div class="container">
            <div id="header">   
                <nav class="navbar navbar-expand-md navbar-light bg-white">
                    <a class="navbar-brand" href="index.php">
                        <img class="img-fluid img-brand" src="img/favicon.png" alt="Logo"> <?php echo $menu_title ?>
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mr-auto">
                            <?php foreach( $menu->getItems() as $item ) {
                                echo '<li class="nav-item"><a class="nav-link" href="'.$item->getUrl().'">'.$item->getName().'</a></li>';
                            } ?>
                        </ul>
                        <ul class="navbar-nav my-2 my-md-0">
                            <?php if ($luna_user['is_guest']) { ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo $menu->getRegisterUrl() ?>"><?php _e('Register', 'luna') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-toggle="modal" data-target="#login-form"><?php _e('Login', 'luna') ?></a>
                                </li>
                            <?php } else { ?>
                                <?php if ($luna_user['is_admmod']) { ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php echo $menu->getBackstageUrl() ?>"><i class="fas fa-fw fa-tachometer-alt"></i><span class="d-inline d-md-none"><?php _e('Backstage', 'luna') ?></span></a>
                                    </li>
                                <?php } ?>
                                <?php if ($luna_config['o_enable_inbox'] == '1' && $luna_user['g_inbox'] == '1' && $luna_user['use_inbox'] == '1') { ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php echo $menu->getInboxUrl( $luna_user['id'] ) ?>"><?php echo $inbox_count ?><i class="fas fa-fw fa-paper-plane"></i><span class="d-inline d-md-none"> <?php _e('Inbox', 'luna') ?></span></a>
                                    </li>
                                <?php } ?>
                                <?php if ($luna_config['o_notification_flyout'] == 1) { ?>
                                    <li class="nav-item dropdown dropdown-notifications">
                                        <a class="nav-link dropdown-toggle" href="#" id="notificationMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?php echo $notificon ?><span class="d-inline d-md-none"> <?php _e('Notifications', 'luna') ?></span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notificationMenu">
                                            <h6 class="dropdown-header"><?php _e('Notifications', 'luna') ?></h6>
                                            <div class="dropdown-divider"></div>
                                            <?php
                                            if ($notification_count == '0') {
                                                echo '<a class="dropdown-item" href="'.$menu->getNotificationsUrl( $luna_user['id'] ).'">'.__('No new notifications', 'luna').'</a>';
                                            } else {
                                                foreach ($notifications as $notification) {
                                                    echo '<a class="dropdown-item" href="'.$menu->getNotificationsUrl( $luna_user['id'] ).'&notification='.$notification->getId().'"><span class="timestamp">'.$notification->getTime().'</span> <span class="fas fa-fw '.$notification->getIcon().'"></span> '.$notification->getMessage().'</a>';
                                                }
                                            }
                                            ?>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item float-right" href="<?php echo $menu->getNotificationsUrl( $luna_user['id'] ) ?>"><?php _e('More', 'luna') ?> <i class="fas fa-fw fa-arrow-right"></i></a>
                                        </div>
                                    </li>
                                <?php } else { ?>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="<?php echo $menu->getNotificationsUrl( $luna_user['id'] ) ?>" class="<?php echo $notificon ?>"><span class="d-inline d-sm-none"> <?php _e('Notifications', 'luna') ?></span></a>
                                    </li>
                                <?php } ?>
                                <li class="nav-item dropdown dropdown-user">
                                    <a class="nav-link dropdown-toggle" id="profileMenu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <?php echo draw_user_avatar($luna_user['id'], true, 'avatar') ?>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profileMenu">
                                        <a class="dropdown-item" href="<?php echo $menu->getProfileUrl( $luna_user['id'] ) ?>"><i class="fas fa-fw fa-user"></i><?php _e('Profile', 'luna') ?></a>
                                        <a class="dropdown-item" href="<?php echo $menu->getSettingsUrl( $luna_user['id'] ) ?>"><i class="fas fa-fw fa-cogs"></i><?php _e('Settings', 'luna') ?></a>
                                        <a class="dropdown-item" href="<?php echo $menu->getHelpUrl() ?>"><i class="fas fa-fw fa-info-circle"></i><?php _e('Help', 'luna') ?></a>
                                        <a class="dropdown-item" href="<?php echo $menu->getLogoutUrl( $luna_user['id'] ) ?>"><i class="fas fa-fw fa-sign-out-alt"></i><?php _e('Logout', 'luna') ?></a>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>